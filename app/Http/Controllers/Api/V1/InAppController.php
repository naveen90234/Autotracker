<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
// use App\Models\Notification;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Config;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use Carbon\Carbon;
use App\Models\ProductsPurchaseHistory;

use ReceiptValidator\iTunes\Validator as iTunesValidator;
use ReceiptValidator\GooglePlay\Validator as PlayValidator;

class InAppController extends Controller
{

    public $response = ['status' => false, 'message' => "", 'data' => []];
    public $status_code = 200;


    // 1. Cron for subscription management
    public function subscriptionStatusCron(Request $request)
    {
        $current_date = Carbon::now('utc')->format('Y-m-d');

        $payments = SubscriptionPayment::select('subscription_payments.*', 'subscription_plans.time_limit', 'subscription_plans.duration', 'subscription_plans.plan_id as product_id')
            ->leftJoin('subscription_plans', 'subscription_plans.id', 'subscription_payments.plan_id')
            ->whereIn('subscription_payments.status', [1, 2])->where('subscription_payments.end_date', '<', $current_date)->get();

        $expired_users = [];
        foreach ($payments as $row) {

            $res = $this->checkPlanStatus($row->platform, $row->receipt, 'restore');

            if ($res['status']) {

                $start_date = $res['data']['start_date'];
                $end_date = $res['data']['end_date'];

                if ($end_date >= $current_date) {
                    SubscriptionPayment::whereId($row->id)->update(['start_date' => $start_date, 'end_date' => $end_date, 'status' => 1]);
                    User::whereId($row->user_id)->update(['is_premium' => '1', 'plan_id' => $row->product_id, 'is_subscription_expired' => 0]);
                } else {
                    array_push($expired_users, $row->user_id);
                    SubscriptionPayment::whereId($row->id)->update(['start_date' => $start_date, 'end_date' => $end_date, 'status' => 2]);
                    User::whereId($row->user_id)->update(['is_premium' => '0', 'is_subscription_expired' => 1, 'plan_id' => NULL]);
                }
            } else {
                array_push($expired_users, $row->user_id);
                SubscriptionPayment::whereId($row->id)->update(['status' => 0]);
                User::whereId($row->user_id)->update(['is_premium' => '0', 'is_subscription_expired' => 1, 'plan_id' => NULL]);
            }
        }
    }


    // 2. Purchase Plan
    public function purchasePlan(Request $request)
    {
        $rules = [
            'receipt'    => 'required',
            'platform'   => 'required|String|in:ANDROID,IOS',
            'plan_id'    => 'required',
            // 'start_date' => 'required',
            'amount'     => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->response['message'] = $validator->errors()->first();
            return response()->json($this->response, $this->status_code);
        }
        $user_id  = JWTAuth::parseToken()->authenticate()->id;
        $userData = User::whereId($user_id)->first();

        $receipt = $request->receipt;

        $res = $this->checkPlanStatus($request->platform, $receipt, 'purchase');

        if ($res['status']) {

            $start_date = $res['data']['start_date'];
            $end_date = $res['data']['end_date'];


            //deactivate all the current active subscriptions
            SubscriptionPayment::where('user_id', $user_id)->update(['status' => 0]);

            $plan = SubscriptionPlan::where('plan_id', $request->plan_id)->first();
            // $start_date = date('Y/m/d h:i:s', $start_date);

            $payment = new SubscriptionPayment;
            $payment->user_id = $user_id;

            $payment->platform   = $request->platform;
            $payment->start_date = $start_date;
            $payment->end_date   = $end_date;
            $payment->plan_id    = $plan->id;
            $payment->amount     = $request->amount;
            $payment->status     = 1;

            if ($request->platform == 'IOS') {
                $payment->receipt = str_replace("%2B", "+", $request->receipt);
            } elseif ($request->platform == 'ANDROID') {
                $payment->receipt = $request->receipt;
            }
            $payment->save();
            $user = User::find($user_id);
            $user->update(['is_premium' => '1', 'plan_id' => $request->plan_id, 'is_subscription_expired' => 0, 'is_free_plan' => 0]);

            $this->response['status']  = true;
            $this->response['message'] = Config::get('message-constants.PLAN_PURCHASED');
            $this->response['data']    = $user;

            return response()->json($this->response, $this->status_code);
        } else {
            return response()->json($res);
        }
    }


    // 3. Check if user is subscribed
    public function checkSubscriptionStatus(Request $request)
    {

        $is_subscribed = JWTAuth::parseToken()->authenticate()->is_premium;

        if ($is_subscribed) {
            $message = Config::get('message-constants.SUBSCRIPTION_ACTIVE');
        } else {
            $message = Config::get('message-constants.SUBSCRIPTION_EXPIRED');
        }
        $status = $is_subscribed ? true : false;

        $this->response['status'] = true;
        $this->response['message'] = $message;
        $this->response['data'] = ['is_subscribed' => $status];

        return response()->json($this->response, $this->status_code);
    }


    // 4. Purchase Restore
    public function purchaseRestore(Request $request)
    {
        $rules = [
            'receipt'    => 'required',
            'platform'   => 'required|String|in:ANDROID,IOS',
            // 'plan_id'    => 'required',
            // 'start_date' => 'required',
            'amount'     => 'nullable'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->response['message'] = $validator->errors()->first();
            return response()->json($this->response, $this->status_code);
        }
        $user_id  = JWTAuth::parseToken()->authenticate()->id;

        $receipt = $request->receipt;

        $isAlreadySubscribed = SubscriptionPayment::where('receipt', str_replace("%2B", "+", $request->receipt))->count();

        if($isAlreadySubscribed > 0){
            return response()->json(['status' => false, 'message' => 'No active subscription found.', 'data' => (object) []]);
        }

        $res = $this->checkPlanStatus($request->platform, $receipt, 'restore');
        if ($res['status']) {
            $plan = SubscriptionPlan::where('plan_id', $res['data']['product_id'])->first();

            $start_date = $res['data']['start_date'];
            $end_date = $res['data']['end_date'];

            //deactivate all the current active subscriptions
            SubscriptionPayment::where('user_id', $user_id)->update(['status' => 0]);

            $payment = SubscriptionPayment::where(['receipt' => $receipt, 'user_id' => $user_id])->first();

            if(!$payment){
                $payment = new SubscriptionPayment;
                $payment->user_id = $user_id;
            }

            // $start_date = date('Y/m/d h:i:s', $start_date);

            $payment->platform   = $request->platform;
            $payment->start_date = $start_date;
            $payment->end_date   = $end_date;
            $payment->plan_id    = $plan->id;
            $payment->amount     = $request->amount??$plan->amount;
            $payment->status     = 1;

            if ($request->platform == 'IOS') {
                $payment->receipt = str_replace("%2B", "+", $request->receipt);
            } elseif ($request->platform == 'ANDROID') {
                $payment->receipt = $request->receipt;
            }
            $payment->save();
            $user = User::find($user_id);
            $user->update(['is_premium' => '1', 'plan_id' => $res['data']['product_id'], 'is_subscription_expired' => 0, 'is_free_plan' => 0]);

            $this->response['status']  = true;
            $this->response['message'] = Config::get('message-constants.PLAN_RESTORED');
            $this->response['data']    = $user;

            return response()->json($this->response, $this->status_code);
        } else {
            return response()->json($res);
        }
    }


    public function purchaseProduct(Request $request)
    {
        $rules = [
            'receipt' => 'required',
            'platform' => 'required|String|in:ANDROID,IOS',
            'plan_id' => 'required',
            'amount' => 'required',
            'purchase_type' => 'required|String|in:KEYS,SPEED_DATING_PASS',
            'speed_dating_date' => 'nullable',
            'key_quantity' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->response['message'] = $validator->errors()->first();
            return response()->json($this->response, $this->status_code);
        }
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $userData = User::whereId($user_id)->first();

        $receipt = $request->receipt;

        $plan_type = ($request->platform == 'IOS') ? 'purchase' : 'product';
        $res = $this->checkPlanStatus($request->platform, $receipt, $plan_type);

        if ($res['status']) {

            $plan = SubscriptionPlan::where('plan_id', $request->plan_id)->first();

            $payment = new ProductsPurchaseHistory;
            $payment->user_id = $user_id;

            $payment->plan_id = $plan->id;
            $payment->amount = $request->amount;
            $payment->platform = $request->platform;
            $payment->purchase_type = $request->purchase_type;

            if ($request->platform == 'IOS') {
                $payment->receipt = str_replace("%2B", "+", $request->receipt);
            } elseif ($request->platform == 'ANDROID') {
                $payment->receipt = $request->receipt;
            }

            if($request->purchase_type == 'KEYS'){

                $purchased_keys = (int)$request->key_quantity??0;
                $payment->status = 0;
                $payment->purchased_keys = $purchased_keys;

                //increment user keys count
                $user = User::find($user_id);
                $user->increment('avilable_key', (int) $purchased_keys);

            }else{

                $payment->status = 1;
                $payment->speed_dating_date = Carbon::parse($request->speed_dating_date)->format('Y-m-d');

            }

            $payment->save();

            $user = User::find($user_id);

            $this->response['status'] = true;
            $this->response['message'] = Config::get('message-constants.PRODUCT_PURCHASED');
            $this->response['data'] = $user;

            return response()->json($this->response, $this->status_code);
        } else {
            return response()->json($res);
        }
    }



    // 5. Check Plan Status Function
    public function checkPlanStatus($platform, $receipt_data, $plan_type)
    {
        $plan_status = ['status' => false, 'data' => []];
        if ($platform == 'IOS') {

            $sharedSecret = Config::get('constants.IOS_SHARED_SECRET'); // Generated in iTunes Connect's In-App Purchase menu
            $data = [
                'receipt-data' => $receipt_data,
                'password'     => $sharedSecret,
            ];

            $sandbox_url =  "https://sandbox.itunes.apple.com/verifyReceipt"; // for sandbox
            $live_url = "https://buy.itunes.apple.com/verifyReceipt"; // for production

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $live_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $encodedResponse = curl_exec($ch);
            curl_close($ch);
            $decodeResponse = json_decode($encodedResponse, TRUE);
            $applestatus1 = $decodeResponse['status'];

            if ($applestatus1 == 21007) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, $sandbox_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $encodedResponse = curl_exec($ch);

                curl_close($ch);

                $decodeResponse = json_decode($encodedResponse, TRUE);
                $applestatus1   = $decodeResponse['status'];
            }

            if ($applestatus1 == 21002 || $applestatus1 == 21003) {
                // throw new \Exception('Invalid receipt code 21002');
                return  $data = ['status' => false, 'message' => Config::get('message-constants.RECEIPT_NOT_VALID'), 'data' => []];
            } else if ($applestatus1 == 21007) {

                return $data = ['status' => false, 'message' => Config::get('message-constants.PASSED_SANDBOX_RECEIPT'), 'data' => []];

            } else if ($applestatus1 == 21008) {

                return $data = ['status' => false, 'message' => Config::get('message-constants.PASSED_PRODUCTION_RECEIPT'), 'data' => []];

            } else {
                $plan_status['status'] = true;

                if($plan_type == 'purchase'){
                    foreach ($decodeResponse['receipt']['in_app'] as $purchase) {
                        $transaction_id = $purchase['transaction_id'];
                        $product_id = $purchase['product_id'];
                        // $start_date = date("Y-m-d", strtotime($purchase['purchase_date']));

                        $start_date = date("Y-m-d", $purchase['purchase_date_ms'] / 1000);
                        if(isset($purchase['expires_date_ms'])){
                            $end_date = date("Y-m-d", $purchase['expires_date_ms'] / 1000);
                            $plan_status['data']['end_date'] = $end_date;
                        }
                    }
                }else{

                    $transaction_id = $decodeResponse['latest_receipt_info'][0]['transaction_id'];
                    $product_id = $decodeResponse['latest_receipt_info'][0]['product_id'];
                    // $start_date = date("Y-m-d", strtotime($decodeResponse['latest_receipt_info'][0]['purchase_date']));

                    $start_date = date("Y-m-d", $decodeResponse['latest_receipt_info'][0]['purchase_date_ms'] / 1000);
                    $end_date = date("Y-m-d", $decodeResponse['latest_receipt_info'][0]['expires_date_ms'] / 1000);

                    $plan_status['data']['end_date'] = $end_date;

                }

                $plan_status['data']['start_date'] = $start_date;
                $plan_status['data']['transaction_id'] = $transaction_id;
                $plan_status['data']['product_id'] = $product_id;

            }
        } elseif ($platform == 'ANDROID') {
            $pathToServiceAccountJsonFile = public_path() . '/app_android.json';

            $googleClient = new \Google_Client();
            $googleClient->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
            $googleClient->setApplicationName(env('APP_NAME'));
            $googleClient->setAuthConfig($pathToServiceAccountJsonFile);

            $googleAndroidPublisher = new \Google_Service_AndroidPublisher($googleClient);
            $validator = new \ReceiptValidator\GooglePlay\Validator($googleAndroidPublisher);

            $receipt = json_decode($receipt_data);
            try {

                if ($plan_type == 'product') {

                    $response = $validator->setPackageName($receipt->packageName)
                        ->setProductId($receipt->productId)
                        ->setPurchaseToken($receipt->purchaseToken)
                        ->validatePurchase();

                    foreach ((array) $response as $key => $value) {
                        foreach ((array) $value as $key_inner => $value_inner) {
                            if ($key_inner == "purchaseTimeMillis") {
                                $plan_status['data']['start_date'] = date("Y-m-d", $value_inner / 1000);
                            }
                        }
                    }

                } else {

                    $response = $validator->setPackageName($receipt->packageName)
                        ->setProductId($receipt->productId)
                        ->setPurchaseToken($receipt->purchaseToken)
                        ->validateSubscription();

                    foreach ((array) $response as $key => $value) {
                        foreach ((array) $value as $key_inner => $value_inner) {
                            if ($key_inner == "startTimeMillis") {
                                $plan_status['data']['start_date'] = date("Y-m-d", $value_inner / 1000);
                            }
                            if ($key_inner == "expiryTimeMillis") {
                                $plan_status['data']['end_date'] = date("Y-m-d", $value_inner / 1000);
                            }
                        }
                    }

                }


                $plan_status['status'] = true;
                $plan_status['data']['product_id'] = $receipt->productId;

            } catch (\Exception $e) {
                // var_dump($e->getMessage());
                return  $data = ['status' => false, 'message' => $e->getMessage(), 'data' => []];
                // example message: Error calling GET ....: (404) Product not found for this application.
            }
        }
        return $plan_status;
    }

    // 5. Get Plan List
    public function getPlanList(Request $request)
    {

        try {
            $jwt = JWTAuth::toUser(JWTAuth::getToken());
            $user_id = $jwt->id;

            $subscriptions = SubscriptionPlan::select('id', 'name', 'description')->orderBy('id', 'DESC')->get();

            if (count($subscriptions) > 0) {
                return response()->json(['status' => true, 'message' => Config::get('message-constants.SUCCESS'), 'data' => $subscriptions]);
            } else {
                return response()->json(['status' => false, 'message' => Config::get('message-constants.NO_RECORD_FOUND'), 'data' => $subscriptions]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }
}
