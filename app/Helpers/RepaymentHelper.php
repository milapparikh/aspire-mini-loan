<?php

namespace App\Helpers;

use App\Models\Repayment;
use Carbon\Carbon;

class RepaymentHelper
{

	protected $aprRate = '8.9';	  //Apr Interest Rate
	protected $loanFees = 0.02;   //2% of total loan amounts

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

  
}