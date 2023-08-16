<?php

/**
 * Handle Stripe Payment
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    StripePaymentService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\Payment;

use Illuminate\Http\Request;
use App\Contracts\paymentInterface;

class StripePaymentService implements paymentInterface
{
    protected $payment_status = false;
    protected $payment_intent = false;

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
        $this->stripe = new \Stripe\StripeClient([
            "api_key" => credentials('secret_key','Stripe'),
            "stripe_version" => credentials('api_version','Stripe')
        ]);
    }

    protected function createPaymentMethod($stripe_card)
    {
        try {
            $payment_method = $this->stripe->paymentMethods->create(array(
                'card' => $stripe_card,
                'type' => 'card'
            ));
            return ['error' => false,'data' => $payment_method];
        }
        catch(\Exception $exception) {
            return ['error' => true,'error_message' => $exception->getMessage()];
        }
    }

    public function createPaymentIntent(array $purchaseData)
    {
        try {
            $purchaseData['confirm'] = true;
            $payment_intent = $this->stripe->paymentIntents->create($purchaseData);
            return ['error' => false,'data' => $payment_intent];
        }
        catch(\Exception $exception) {
            if($exception instanceOf \Stripe\Exception\CardException) {
                $error_code = $exception->getError()->code;
                if($error_code == 'authentication_required') {
                    $payment_intent = $exception->getError()->payment_intent;
                    
                    if($payment_intent->status == 'requires_payment_method' && $purchaseData['payment_method']) {
                        $payment_intent = $this->stripe->paymentIntents->confirm($payment_intent->id,
                            ['payment_method' => $purchaseData['payment_method']]
                        );
                    }

                    $this->payment_intent = $payment_intent;
                    $this->payment_status = $this->payment_intent->status;
                    return ['error' => false,'data' => $this->payment_intent];
                }
            }
            return ['error' => true,'error_message' => $exception->getMessage()];
        }
    }

    protected function getPaymentIntent($intent_id)
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve(
                $intent_id
            );
            return ['error' => false,'data' => $intent];
        }
        catch(\Exception $exception) {
            return ['error' => true,'error_message' => $exception->getMessage()];
        }
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
            'stripe_token' => 'required_if:saved_payment_method,=,null',
        );
        $attributes = array(
            'stripe_token' => 'Token',
        );
        $messages   = array();

        return compact('rules','attributes','messages');
    }

    public function paymentDetailsFromRequest(Request $request)
    {
        $token = $request->stripe_token;
        return compact('token');
    }

    public function doPayment(array $payment_data, array $purchase_data)
    {
        $payment_method = $this->createPaymentMethod($payment_data);
        if($payment_method['error']) {
            return $payment_method;
        }
        $payment_method = $payment_method["data"];
        $purchase_data["payment_method"] = $payment_method->id;
        
        $payment_intent = $this->createPaymentIntent($purchase_data);
        if($payment_intent['error']) {
            return $payment_intent;
        }
        $payment_intent = $payment_intent['data'];

        $this->payment_intent = $payment_intent;
        $this->payment_status = $payment_intent->status;

        return [
            'error' => false,
            'card' => $payment_method['card'],
        ];
    }

    public function isPaymentCompleted()
    {
        return ($this->payment_status == 'succeeded');
    }

    public function checkPaymentCompleted($payment_intent)
    {
        return ($payment_intent->status == 'succeeded');
    }

    public function isTwoStep()
    {
        return ($this->payment_status == 'requires_action');
    }

    public function getTwoStepData()
    {
        $payment_intent = $this->getPayment();
        if($payment_intent['error']) {
            return $payment_intent;
        }

        return array(
            "error" => false,
            "client_secret" => $payment_intent["data"]->client_secret,
        );
    }

    public function getPayment(string $intent_id = '')
    {
        $payment_intent = $this->payment_intent;
        if($intent_id != '') {
            $intent = $this->getPaymentIntent($intent_id);
            
            if($intent["error"]) {
                return $intent;
            }
            $payment_intent = $intent['data'];
        }
        
        if($payment_intent != '') {
            return array(
                "error" => false,
                "data" => $payment_intent,
            );
        }
        return array(
            "error" => true,
            "data" => "Invalid Payment",
        );        
    }

    public function createCustomer($customer_data)
    {
        try {
            $customer = $this->stripe->customers->create($customer_data);
            return ['error' => false,'data' => $customer];
        }
        catch(\Exception $exception) {
            return ['error' => true,'error_message' => $exception->getMessage()];
        }
    }
}