<?php

namespace App\Helpers;

use App\Models\Repayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RepaymentHelper
{

	/*
    * @todo calculate repayment amounts
    * @method calculateMonthlyPayments
    * @params Object $request
    * @return Array Json
    */
	public function calculateMonthlyPayments($loanAmount,$weekTerms,$id_loan,$id_user)
	{
		$dt = Carbon::now();
		$current_datetime = $dt->toDateTimeString();
		$dt->addDays(7);	//First installment date will be after 7 days

		$weekly_principal_amount = round(($loanAmount/$weekTerms),2);
		$capital = $loanAmount;

		for ($i=0;$i<$weekTerms;$i++) 
		{
			$remaining_loan_capital = $capital;
	        $start_date = $dt->toDateString();

			$record = [
		        'id_loan' => $id_loan,
		        'repayment_date' => $start_date,
		        'installment_amount' => $weekly_principal_amount,
		        'remain_loan_capital' => $remaining_loan_capital,
		        'repayment_status' => 'Pending',
		    ];	
	        
	        $dt->addDays(7);
	        $capital = $capital - $weekly_principal_amount; 
	        $records[] = $record; 
		}

		Repayment::insert($records);
	}

	/*
    * @todo calculate repayment amounts
    * @method getUpcomingRepayments
    * @params $id_loan
    * @params $exceed_amount
    * @return Array Json
    */
	public function setRepaymentsRemainAmount($id_loan,$exceed_amount)
	{
		//get all pending status related repaymetns
		$loan_repayment = DB::table('repayment')
            ->select('repayment.*')
            ->where('repayment_status','Pending')
            ->where('id_loan',$id_loan)
            ->orderBy('id_repayment','DESC')
            ->get();

        if($loan_repayment->count() > 0)
        {
        	$cover_more_repayment = 'No';
        	foreach($loan_repayment as $val)
        	{
        		$val = (array) $val;
        		if($val['installment_amount'] >= $exceed_amount && $cover_more_repayment == 'No')
        		{
        			$new_installment_amount = $val['installment_amount'] - $exceed_amount;
        			$new_remain_loan_capital = $val['remain_loan_capital'] - $exceed_amount;

        			DB::table('repayment')->where('id_repayment',$val['id_repayment'])->update(['installment_amount'=>$new_installment_amount,'remain_loan_capital'=>$new_remain_loan_capital]);
        			$cover_more_repayment = 'No';
        			break;
        		}
        		else
        		{
        			$cover_more_repayment = 'Yes';
        			if($exceed_amount > 0)
        			{
        				$new_installment_amount = $val['installment_amount'] - $exceed_amount;
        				if($new_installment_amount < 0)
        					$new_installment_amount = 0;
        				$new_remain_loan_capital = $val['remain_loan_capital'] - $exceed_amount;
        				if($new_remain_loan_capital < 0)
        					$new_remain_loan_capital = 0;
        				$exceed_amount = $exceed_amount - $val['installment_amount'];

	        			DB::table('repayment')->where('id_repayment',$val['id_repayment'])->update(['installment_amount'=>$new_installment_amount,'remain_loan_capital'=>$new_remain_loan_capital,'repayment_status'=>($exceed_amount > 0) ? 'Paid' : 'Pending']);
        			}
        		}

        	}
        }
	}
  
}