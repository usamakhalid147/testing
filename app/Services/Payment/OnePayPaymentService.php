<?php

/**
 * Handle OnePay Payment
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    OnePayPaymentService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\Payment;

use Illuminate\Http\Request;
use App\Contracts\paymentInterface;
use Lang;

class OnePayPaymentService implements paymentInterface
{
    protected $base_url = 'https://onepay.vn/paygate/vpcpay.op';

    /**
     * Intialize Stripe Payment Service
     *
     */ 
    public function __construct()
    {
        $this->preparePayment();
    }

    /**
     * Prepare payment Driver
     *
     */
    public function preparePayment()
    {
        $this->base_url = credentials("paymode","OnePay") ? "https://mtf.onepay.vn/paygate/vpcpay.op" : "https://onepay.vn/paygate/vpcpay.op";
    }

    /**
     * Get Required Validation data based on payment Type
     *
     *
     * @return array ruels,attributes and message
     */
    public function validationData()
    {
        $rules = array(
        );
        $attributes = array();
        $messages   = array();

        return compact('rules','attributes','messages');
    }

    public function paymentDetailsFromRequest(Request $request)
    {
        return [];
    }

    public function create_vpc_SecureHash($args)
    {
        $stringHashData = "";

        // arrange array data a-z before make a hash
        ksort( $args );

        foreach ( $args as $key => $value ) {

            if ( strlen( $value ) > 0 ) {
                if ( ( strlen( $value ) > 0 ) && ( ( substr( $key, 0, 4 ) == "vpc_" ) || ( substr( $key, 0, 5 ) == "user_" ) ) ) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
        }
        $stringHashData = rtrim( $stringHashData, "&" );

        $secure_hash = strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', credentials('hash_key','OnePay'))));
        return $secure_hash;
    }

    public function doPayment(array $payment_data, array $purchase_data)
    {
        $params = [
            "vpc_Version" => "2",
            "AgainLink" => $_SERVER['HTTP_REFERER'],
            "Title" => global_settings('site_name'),
            'vpc_AccessCode' => credentials('access_code',"OnePay"),
            'vpc_Amount' => (string)round($purchase_data['amount']),
            // 'vpc_Amount' => "1000",
            "vpc_Command" => "pay",
            "vpc_Customer_Email" => $purchase_data['email'],
            "vpc_Customer_Id" => $purchase_data['user_id'],
            "vpc_Customer_Phone" => $purchase_data['phone_number'],
            "vpc_Locale" => "en",
            "vpc_MerchTxnRef" => date('YmdHis'),
            'vpc_Merchant' => credentials('merchant',"OnePay"),
            "vpc_OrderInfo" => truncateString($purchase_data['description'],30),
            "vpc_ReturnURL" => resolveRoute('complete_onepay_payment',['hotel_id' => $purchase_data['hotel_id'],'booking_attempt_id' => $purchase_data['booking_attempt_id']]),
            'vpc_TicketNo' => $_SERVER['REMOTE_ADDR'],
        ];

        $params['vpc_SecureHash'] = $this->create_vpc_SecureHash($params);

        return $this->base_url.'?'.http_build_query($params);
    }

    public function isTwoStep()
    {
        return false;
    }

    public function getPayment(string $intent_id = '')
    {
    }

    public function validatePayment($pay_data,$secure_hash)
    {
        if($pay_data["vpc_TxnResponseCode"] == "7" || $pay_data["vpc_TxnResponseCode"] == "No Value Returned") {
            return [
                'error' => true,
                'error_message' => $pay_data["vpc_Message"] ?? $pay_data["vpc_TxnResponseCode"] ?? Lang::get('messages.invalid_request'),
            ];
        }

        $hash = $this->create_vpc_SecureHash($pay_data);
        if($secure_hash != $hash) {
            return [
                'error' => true,
                'error_message' => Lang::get('messages.invalid_request'),
            ];
        }

        $response_code = $pay_data["vpc_TxnResponseCode"];
        if($response_code == 0) {
            return [
                'status' => true,
                'message' => $pay_data['vpc_Message'],
                'data' => [
                    'transaction_id' => $pay_data['vpc_TransactionNo'],
                    'receipt_no' => $pay_data['vpc_ReceiptNo'] ?? $pay_data['vpc_NetworkTransactionID'] ?? '',
                ],
            ];            
        }

        return [
            'error' => true,
            'error_message' => $this->getResponseDescription($response_code),
        ];
    }

    protected function getResponseDescription($responseCode)
    {
        switch ($responseCode) {
            case "0" :
                $result = "Transaction Successful";
                break;
            case "?" :
                $result = "Transaction status is unknown";
                break;
            case "1" :
                $result = "Bank system reject";
                break;
            case "2" :
                $result = "Bank Declined Transaction";
                break;
            case "3" :
                $result = "No Reply from Bank";
                break;
            case "4" :
                $result = "Expired Card";
                break;
            case "5" :
                $result = "Insufficient funds";
                break;
            case "6" :
                $result = "Error Communicating with Bank";
                break;
            case "7" :
                $result = "Payment Server System Error";
                break;
            case "8" :
                $result = "Transaction Type Not Supported";
                break;
            case "9" :
                $result = "Bank declined transaction (Do not contact Bank)";
                break;
            case "A" :
                $result = "Transaction Aborted";
                break;
            case "C" :
                $result = "Transaction Cancelled";
                break;
            case "D" :
                $result = "Deferred transaction has been received and is awaiting processing";
                break;
            case "F" :
                $result = "3D Secure Authentication failed";
                break;
            case "I" :
                $result = "Card Security Code verification failed";
                break;
            case "L" :
                $result = "Shopping Transaction Locked (Please try the transaction again later)";
                break;
            case "N" :
                $result = "Cardholder is not enrolled in Authentication scheme";
                break;
            case "P" :
                $result = "Transaction has been received by the Payment Adaptor and is being processed";
                break;
            case "R" :
                $result = "Transaction was not processed - Reached limit of retry attempts allowed";
                break;
            case "S" :
                $result = "Duplicate SessionID (OrderInfo)";
                break;
            case "T" :
                $result = "Address Verification Failed";
                break;
            case "U" :
                $result = "Card Security Code Failed";
                break;
            case "V" :
                $result = "Address Verification and Card Security Code Failed";
                break;
            case "99" :
                $result = "You Canceled the payment";
                break;
            default  :
                $result = "Unable to be determined";
        }
        return $result;
    }
}