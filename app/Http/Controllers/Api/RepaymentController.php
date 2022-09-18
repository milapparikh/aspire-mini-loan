<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Traits\LoanCalculation;

class RepaymentController extends Controller 
{

    /*
    * @todo payment paid of repayment by customer
    * @method payRepayment
    * @params Object $request
    * @return Array Json
    */
    public function payRepayment(Request $request)
    { 
        $return_params['data'] = array();
        $return_params['error'] = '';
        $return_params['success'] = '';
        $return_params['msg'] = '';        

        $request_params = $request->all();

        $validator = Validator::make($request_params,[ 
            'id_loan' => "required",
            'repayment_amount' => "required",
            'repayment_date' => "required",
        ]);

        if($validator->fails())
        {
            $return_params['error'] = 'Yes';
            $return_params['data'] = $validator->messages();
        } 
        else
        {
            $RepaymentHelper = new \App\Helpers\RepaymentHelper;
            //get open loan of customer
            $id_user = $request->user()->id;

            //check if any loan application in_complete
            $loan = DB::table('loan')
                ->select('loan.*')
                ->where('id_user',$id_user)
                ->where('id_loan',$request_params['id_loan'])
                ->first();

            if($loan && $loan->loan_status == "Paid" )
            {
                $return_params['error'] = 'Yes';
                $return_params['msg'] = 'This loan is allready paid!';  
            }                

            if($loan)
            {
                //check upcoming repayments
                $loan_repayment = DB::table('repayment')
                    ->select('repayment.*')
                    ->where('repayment_status','Pending')
                    ->where('id_loan',$loan->id_loan)
                    ->orderBy('id_repayment','ASC')
                    ->get();

                $id_loan = $loan->id_loan;

                if($loan_repayment->count() > 0)
                {
                    $loan_remain_capital_amount = $loan->remain_capital;
                    $exceed_amount = 0;
                    foreach($loan_repayment as $val)
                    {
                        $val = (array) $val;   
                        //paid repayment all logic comes here.

                        //check if pay repayment amount > schedule payment

                       

                        if($request_params['repayment_amount'] < $val['installment_amount'])
                        {
                            $return_params['error'] = 'Yes';
                            $return_params['msg'] = 'Pay amount must be greate than or equal to schedule amount!';  
                            return response()->json($return_params,200);   
                            break;
                        }
                        else if($request_params['repayment_amount'] >= $val['installment_amount'])
                        {
                            $exceed_amount = $request_params['repayment_amount'] - $val['installment_amount'];
                        }
                        break;
                    }

                    if($exceed_amount > 0)
                    {   //if extra amount paid than covered next payment

                        //Regular payment paid scenarios
                        DB::table('repayment')->where('id_repayment',$val['id_repayment'])->update(['repayment_status'=>'Paid','paid_amount'=>$request_params['repayment_amount'],'paid_date'=>date("Y-m-d")]);
                        $RepaymentHelper->setRepaymentsRemainAmount($id_loan,$exceed_amount);
                    }
                    else
                    {
                        //Regular payment paid scenarios
                        DB::table('repayment')->where('id_repayment',$val['id_repayment'])->update(['repayment_status'=>'Paid','paid_amount'=>$request_params['repayment_amount'],'paid_date'=>date("Y-m-d")]);
                    }

                    $loan_remain_capital_amount = $loan_remain_capital_amount - $request_params['repayment_amount'];
                    if($loan_remain_capital_amount <= 0)
                    {
                        //if whole remaining amount paid than
                        DB::table('loan')->where('id',$loan->id)->update(['loan_status'=>'Paid','remain_capital'=>
                '0']);
                    }
                    else
                    {
                        //update remain_capital loan amount
                        DB::table('loan')->where('id_loan',$loan->id_loan)->update(['remain_capital'=>$loan_remain_capital_amount]);
                    }

                    $return_params['success'] = 'Yes';
                    $return_params['msg'] = 'Repayment paid successfully';    
                }
                else
                {
                    $return_params['error'] = 'Yes';
                    $return_params['msg'] = 'No more repayment Schedule fund!';                        
                }
            }
            else
            {
                $return_params['error'] = 'Yes';
                $return_params['msg'] = 'No any associated loan found!';    
            }
        }


        return response()->json($return_params,200);    
    }

     
}