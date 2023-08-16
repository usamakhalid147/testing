<?php

/**
 * Handle Paypal Payment
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    PaypalPaymentService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\Payment;

use Illuminate\Http\Request;
use App\Contracts\paymentInterface;

class PaypalPaymentService implements paymentInterface
{
    /**
     * Intialize Paypal Payment Service
     *
     */ 
    public function __construct()
    {
        $environment = (credentials('paymode','Paypal') == 'sandbox') ? 'sandbox.' : '';

        $this->base_url = "https://api-m.".$environment."paypal.com";

        $this->preparePayment();
    }

    /**
     * Prepare payment Driver
     *
     */
    public function preparePayment()
    {
        $this->clientId = credentials('client_id','Paypal');
        $this->clientSecret = credentials('secret_key','Paypal');
    }

    /**
     * new Curl Request
     *
     */
    protected function newCurlRequest($method,$api,$params = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url."/".$api,
            CURLOPT_USERPWD => $this->clientId.":".$this->clientSecret,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS =>  is_array($params) ? http_build_query($params) : $params,
        ));

        $result = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($result,true);

        if($result == null) {
            return [
                'error' => true,
                'error_message' => "Invalid Request",
            ];
        }

        return [
            'status' => 'success',
            'data' => $result,
        ];
    }

    /**
     * Get Required Validation data based on payment Type
     *
     *
     * @return array ruels,attributes and message
     */
    public function validationData()
    {
        $rules      = array(
            'order_id' => 'required',
        );
        $attributes = array(
            'order_id' => 'Order Id',
        );
        $messages   = array();
        return compact('rules','attributes','messages');
    }

    public function paymentDetailsFromRequest(Request $request)
    {
        $orderId = $request->order_id;
        return compact('orderId');
    }

    public function doPayment(array $payment_data, array $purchase_data)
    {
        try {
            $api_url = "v2/checkout/orders/".$purchase_data['orderId']."/capture";
            $result = $this->newCurlRequest("post",$api_url);

            if($result['status'] == 'success') {
                $response = $result['data'];
            }

            if(isset($response['name']) && $response['name'] == 'UNPROCESSABLE_ENTITY') {
                return [
                    'error' => true,
                    "error_message" => $response['message'] ?? \Lang::get('messages.invalid_request'),
                ];
            }
            $this->payment_order = $response;

            return [
                'error' => false,
                'data' => $response,
            ];
        }
        catch(\Exception $e) {
            $error_message = $e->getMessage();
            if($error = json_decode($error_message,true)) {
                $error_message = @$error['details'][0]['issue'];
            }
            return [
                'error' => true,
                'error_message' => $error_message,
            ];
        }
    }

    public function isPaymentCompleted()
    {
        return ($this->payment_status == 'COMPLETED');
    }

    public function checkPaymentCompleted($payment_order)
    {
        return (isset($payment_order['status']) && $payment_order['status'] == 'COMPLETED');
    }

    public function checkPaymentIsProcessing($payment_order)
    {
        return (isset($payment_order['status']) && $payment_order['status'] == 'PENDING');
    }

    public function getTransactionIdFromOrder($payment_order)
    {
        try {
            return $payment_order['purchase_units'][0]['payments']['captures'][0]['id'];
        }
        catch (\Exception $e) {
            return $payment_order['id'];
        }
    }

    public function isTwoStep()
    {
        return false;
    }

    public function getTwoStepColumn()
    {
        return "";
    }

    public function getTwoStepData()
    {
        return array(
            "error" => true,
            "error_message" => "Not Supported",
        );
    }

    public function doStepTwo()
    {
        
    }

    public function getPaymentOrder()
    {
        return array(
            "error" => false,
            "data" => '',
        );
    }

    public function getPayment(string $order_id = '')
    {
        $payment_order = $this->payment_order;
        if($order_id != '') {
            $order = $this->getPaymentOrder($order_id);
            
            if($order["error"]) {
                return $order;
            }
            $payment_order = $order['data'];
        }
        
        if($payment_order != '') {
            return array(
                "error" => false,
                "data" => $payment_order,
            );
        }
        return array(
            "error" => true,
            "data" => "Invalid Payment",
        );        
    }

    /*
     * Perform a refund on the capture
     *
     */
    public function refundOrder($captureId,$refund_data)
    {
        $api_url = "v2/payments/captures/".$captureId."/refund";
        $result = $this->newCurlRequest("post",$api_url,json_encode($refund_data));

        if($result['status'] == 'success') {
            $response = $result['data'];
        }

        if(isset($response['name']) && in_array($response['name'],['MALFORMED_REQUEST','RESOURCE_NOT_FOUND','INVALID_REQUEST'])) {
            return [
                'error' => true,
                "error_message" => $response['message'] ?? \Lang::get('messages.invalid_request'),
            ];
        }

        return [
            'error' => false,
            'data' => $response,
        ];
    }
}