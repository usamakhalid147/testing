<?php

/**
 * Handle Paypal Payout Information
 *
 * @package     HyraHotel
 * @subpackage  Services\Payouts
 * @category    PaypalPayout
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\Payouts;

class PaypalPayout
{
    public function __construct()
    {
        $paymode  = credentials('paymode','Paypal');
        $environment = ($paymode == 'sandbox') ? 'sandbox.' : '';

        $this->base_url = "https://api.".$environment."paypal.com/v1/";
    }

    public function validateRequest($request)
    {
        $rules = array(
            'country_code' => 'required',
            'paypal_email' => 'required|email',
            'address1' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if(isApiRequest()) {
                return response()->json([
                    'status_code' => '0',
                    'status_message' => $validator->messages()->first(),
                ]);
            }

            return [
                'error' => true,
                'error_messages' => $validator->messages(),
            ];
        }

        return ['error' => false];
    }

    public function createPayoutPreference($request)
    {
        $recipient['email'] = $request->paypal_email;
        $recipient['id'] = $request->paypal_email;
        return array(
            'status' => true,
            'recipient' => $recipient,
        );
    }

    protected function getAuthorizationHeader()
    {
        $client  = credentials('client_id','Paypal');
        $secret  = credentials('secret_key','Paypal');

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->base_url."oauth2/token");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_USERPWD, $client.":".$secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);
        
        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }
        try {
            return array('status' => true, "access_token" => $response->access_token);
        }
        catch(\Exception $e) {
            return array('status' => false,"status_message" => \Lang::get('admin_messages.unable_to_token_from_paypal'));
        }
    }

    protected function sendBatchRequest($pay_data,$access_token)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->base_url."payments/payouts");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pay_data); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$access_token,""));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }

        return array('status' => true, "data" => $response);
    }

    public function makePayout($payout_account,$pay_data)
    {
        try {
            $authorization = $this->getAuthorizationHeader();
            if(!$authorization['status']) {
                return array(
                    'status' => false,
                    'status_message' => $authorization['status_message'],
                );
            }

            $batch_response = $this->sendBatchRequest($pay_data,$authorization['access_token']);
            if(!$batch_response['status']) {
                return array(
                    'status' => false,
                    'status_message' => $batch_response['status_message'],
                );
            }

            $payout_response = $batch_response['data'];

            if(@$payout_response->batch_header->batch_status == "PENDING") {
                $payout_batch_id = $payout_response->batch_header->payout_batch_id;
                
                $payout_data = $this->fetchPayoutViaBatchId($payout_batch_id,$authorization['access_token']);
                if(!$payout_data['status']) {
                    return array(
                        'status' => false,
                        'status_message' => $payout_data['status_message'],
                    );
                }

                return array(
                    'status' => true,
                    'transaction_id' => $payout_batch_id,
                    'status_message' => \Lang::get('admin_messages.payout_process_initiated'),
                    'payout_status' => "Processing",
                );
            }

            return array(
                'status' => false,
                'status_message' => $payout_response->name,
            );
        }
        catch (\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function fetchPayoutViaBatchId($batch_id, $access_token = '')
    {
        if($access_token == '') {
            $authorization = $this->getAuthorizationHeader();
            if(!$authorization['status']) {
                return array(
                    'status' => false,
                    'status_message' => $authorization['status_message'],
                );
            }
            $access_token = $authorization['access_token'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->base_url."payments/payouts/$batch_id");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$access_token,""));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }

        return array('status' => true, "data" => $response);
    }

    public function fetchPayoutViaItemId($item_id, $access_token = '')
    {
        if($access_token == '') {
            $authorization = $this->getAuthorizationHeader();
            if(!$authorization['status']) {
                return array(
                    'status' => false,
                    'status_message' => $authorization['status_message'],
                );
            }
            $access_token = $authorization['access_token'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->base_url."payments/payouts-item/$item_id");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$access_token,""));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }

        return array('status' => true, "data" => $response);
    }

    public function getPayoutStatus($payout_data)
    {
        if(!isset($payout_data->items[0])) {
            return array('status' => false, "status_message" => "Requested Payment Not Found");
        }

        if(in_array($payout_data->items[0]->transaction_status, ['FAILED','RETURNED','BLOCKED'])) {
            return array(
                'status' => false,
                'payout_status' => $payout_data->items[0]->transaction_status,
                "status_message" => @$payout_data->items[0]->errors->message ?? \Lang::get('messages.something_went_wrong'),
            );
        }

        return array(
            'status' => true,
            'payout_status' => "Completed",
            'transaction_id' => @$payout_data->items[0]->transaction_id ?? '',
            'status_message' => \Lang::get('admin_messages.payout_amount_transfered'),
        );
    }

    public function makeRefund($order_id,$refund_data)
    {
        $payment_service = resolve("App\Services\Payment\PaypalPaymentService");
        $refund_data = [
            'amount' => array(
                'value' => $refund_data['amount'],
                'currency_code' => $refund_data['currency_code'],
            )
        ];
        $refund_result = $payment_service->refundOrder($order_id,$refund_data);
        if($refund_result['error']) {
            return array(
                'status'            => false,
                'status_message'    => $refund_result['error_message'],
            );
        }
        return array(
            'status'            => true,
            'status_message'    => \Lang::get('admin_messages.amount_refunded_success'),
            'transaction_id'    => $refund_result['data']['id'],
            'payout_status'     => "Completed",
        );
    }
}