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
//use App\Helpers\RepaymentHelper;

use App\Models\Loan; 

class LoanController extends Controller 
{
    public $actionStatus = 200;
    /*
    * @todo create loan application by customer
    * @method create
    * @params Object $request
    * @return Array Json
    */
    public function create(Request $request)
    { 
        $request_params = $request->all();

        $config_param = config('setting_var');
        $min_loan = $config_param['min_loan_amount'];
        $max_loan = $config_param['max_loan_amount'];
        $min_term = $config_param['min_week_duration'];
        $max_term = $config_param['max_week_duration'];

        $validator = Validator::make($request_params,[ 
            'amount' => "required|integer|between:$min_loan,$max_loan",
            'term' => "required|integer|between:$min_term,$max_term",
        ]);

        $return_params['error'] = '';
        $return_params['success'] = '';
        $return_params['msg'] = '';
        $return_params['data'] = array();
        if($validator->fails())
        {
            $return_params['error'] = 'Yes';
            $return_params['data'] = $validator->messages();
        } 
        else
        {        
            try 
            {
                $dt = Carbon::now();                
                $loan_data['id_user'] = $request->user()->id;
                $loan_data['amount'] = $request_params['amount'];
                $loan_data['term'] = $request_params['term'];
                $loan_data['application_date'] = $dt->format("Y-m-d");
                $loan_data['remain_capital'] = $request_params['amount'];
                Loan::create($loan_data);

                $return_params['success'] = 'Yes';
                $return_params['msg'] = 'Loan Created Successfully';
            }
            catch(Exception $e) 
            {
                Log::info("Error In Loan Create");
                Log::info($e->getMessage());    
                $return_params['msg'] = 'Please Try Again After Some time';
                $return_params['error'] = 'Yes';
            }            
        }

        return response()->json($return_params, $this->actionStatus);    
    }

    /*
    * @todo approve loan application by admin
    * @method loanapprove
    * @params Object $request
    * @return Array Json
    */
    public function loanapprove(Request $request,$id_loan)
    { 
        $return_params['data'] = array();
       
        //Check if loan is in None stat?
        $loan_application = Loan::whereIdLoan($id_loan)->whereisApprove('None')->first();

        if($loan_application)
        {
            $dt = Carbon::now();
            $current_datetime = $dt->toDateTimeString();                
            
            $id_user = $loan_application->id_user;
            $loan_amount = $loan_application->amount;
            $loan_terms = $loan_application->term;

            $loan_start_date = $dt->toDateString();
            $dt->addDays((7*$loan_application->term) - 1);             

            $RepaymentHelper = new \App\Helpers\RepaymentHelper;
            $RepaymentHelper->calculateMonthlyPayments($loan_amount,$loan_terms,$id_loan,$id_user);

            //update loan status and approe date
            Loan::where('id_loan',$id_loan)->update(['is_approve'=>'yes','approve_date'=>$dt->format("Y-m-d")]);

            $return_params['success'] = 'Yes';
            $return_params['msg'] = 'Loan Approved Successfully';
        }   
        else
        {
            $return_params['error'] = 'Yes';
            $return_params['msg'] = 'Loan Not Available';
        } 

        return response()->json($return_params,$this->actionStatus);    
    }    

    /*
    * @todo view loan application by self user/admin
    * @method view
    * @params Object $request
    * @return Array Json
    */
    public function view(Request $request)
    { 
        $return_params['loan_data'] = array();

        $role_type = $request->user()->role_type;
        $id_user = $request->user()->id;

        if($role_type == "A")
        {
            //for admin user all loan list
             $loan_data = DB::table('loan')
                ->select('loan.*')
                ->get();
        }
        else
        {
            //for non-admin user only self loan list
             $loan_data = DB::table('loan')
                ->select('loan.*')
                ->where('id_user',$id_user)
                ->get();
        }
        
        $loan_info_data = array();
        if($loan_data->count() > 0)
        {
            foreach($loan_data as $value)
            {
                $value = (array) $value;
                $loan_info_data[] = $value;
            }
        }

        $return_params['loan_data'] = $loan_info_data;

        return response()->json($return_params,$this->actionStatus);    
    }         
}