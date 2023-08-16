<?php

/**
 * Handle Stripe Payout Information
 *
 * @package     HyraHotel
 * @subpackage  Services\Payouts
 * @category    StripePayout
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\Payouts;

class StripePayout
{
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient([
            "api_key" => credentials('secret_key','Stripe'),
            "stripe_version" => credentials('api_version','Stripe')
        ]);
    }
    
    public function getIbanRequiredCountries() 
    {
        $iban_required_countries = array(
            'AT',
            'BE',
            'CH',
            'DE',
            'DK',
            'ES',
            'FI',
            'FR',
            'GI',
            'IE',
            'IT',
            'LU',
            'NL',
            'NO',
            'PT',
            'SE',
        );
        return $iban_required_countries;
    }

    public function getBranchCodeRequiredCountries() 
    {
        $branch_code_countries = array(
            'CA',
            'HK',
            'JP',
            'SG',
        );
        return $branch_code_countries;
    }

    public function getCurrencyList()
    {
        $currency = array();
        $currency['AT'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['AU'] = ['AUD'];
        $currency['BE'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['CA'] = ['CAD', 'USD'];
        $currency['CH'] = ['CHF', 'EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD'];
        $currency['DE'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['DK'] = ['DKK', 'EUR', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['ES'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['FI'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['FR'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['GB'] = ['GBP', 'EUR', 'DKK', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['HK'] = ['HKD'];
        $currency['IE'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['IT'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['JP'] = ['JPY'];
        $currency['LU'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['MY'] = ['MYR'];
        $currency['NL'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['NO'] = ['NOK', 'EUR', 'DKK', 'GBP', 'SEK', 'USD', 'CHF'];
        $currency['NZ'] = ['NZD'];
        $currency['PT'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['SE'] = ['SEK', 'EUR', 'DKK', 'GBP', 'NOK', 'USD', 'CHF'];
        $currency['SG'] = ['SGD'];
        $currency['US'] = ['USD'];

        return $currency;
    }

    public function getPayoutCoutryList()
    {
        $payout_countries = array(
            'AT' => 'Austria',
            'AU' => 'Australia',
            'BE' => 'Belgium',
            'CA' => 'Canada',
            'CH' => 'Switzerland',
            'DE' => 'Germany',
            'DK' => 'Denmark',
            'ES' => 'Spain',
            'FI' => 'Finland',
            'FR' => 'France',
            'GB' => 'United Kingdom',
            'HK' => 'Hong Kong',
            'IE' => 'Ireland',
            'IT' => 'Italy',
            'JP' => 'Japan',
            'LU' => 'Luxembourg',
            'MY' => 'Malaysia',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NZ' => 'New Zealand',
            'PT' => 'Portugal',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'US' => 'United States',
        );
        return $payout_countries;
    }

    public function getMandatoryFieldList()
    {
        $mandatory_fields = [];
        $mandatory_fields['AT'] = array('IBAN');
        $mandatory_fields['AU'] = array('BSB', 'Account Number');
        $mandatory_fields['BE'] = array('IBAN');
        $mandatory_fields['CA'] = array('Transit Number', 'Account Number', 'Institution Number');
        $mandatory_fields['CH'] = array('IBAN');
        $mandatory_fields['DE'] = array('IBAN');
        $mandatory_fields['DK'] = array('IBAN');
        $mandatory_fields['ES'] = array('IBAN');
        $mandatory_fields['FI'] = array('IBAN');
        $mandatory_fields['FR'] = array('IBAN');
        $mandatory_fields['GB'] = array('Sort Code', 'Account Number');
        $mandatory_fields['HK'] = array('Clearing Code', 'Account Number', 'Branch Code');
        $mandatory_fields['IE'] = array('IBAN');
        $mandatory_fields['IT'] = array('IBAN');
        $mandatory_fields['JP'] = array('Bank Code', 'Account Number', 'Branch Code', 'Bank Name', 'Branch Name', 'Account Owner Name ');
        $mandatory_fields['LU'] = array('IBAN');
        $mandatory_fields['MY'] = array('Routing Number', 'Account Number', 'Personal Id');
        $mandatory_fields['NL'] = array('IBAN');
        $mandatory_fields['NZ'] = array('Routing Number', 'Account Number');
        $mandatory_fields['NO'] = array('IBAN');
        $mandatory_fields['PT'] = array('IBAN');
        $mandatory_fields['SE'] = array('IBAN');
        $mandatory_fields['SG'] = array('Bank Code', 'Account Number', 'Branch Code');
        $mandatory_fields['US'] = array('Routing Number', 'Account Number');
        $mandatory_fields['OT'] = array('Account Holder Name', 'Account Number','','Bank Name','Branch Name');
        
        return $mandatory_fields;
    }

    public function getMandatoryRules()
    {
        $mandatory_rules = array();

        $mandatory_rules['AT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['AU'] = array('bsb' => 'required','account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['BE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['CA'] = array('transit_number' => 'required','account_number' => 'required','institution_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['CH'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['DE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['DK'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['ES'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['FI'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['FR'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['GB'] = array('sort_code' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['HK'] = array('clearing_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['IE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['IT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['JP'] = array('bank_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'bank_name' => 'required', 'branch_name' => 'required', 'account_owner_name' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['LU'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['MY'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required', 'personal_id' => 'required');
        $mandatory_rules['NL'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['NO'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['NZ'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['PT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['US'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required', 'ssn_last_4' => 'required');
        $mandatory_rules['SE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['SG'] = array('bank_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
        $mandatory_rules['OT'] = array('account_number' => 'required', 'bank_name' => 'required', '', 'account_holder_name' => 'required','branch_name' => 'required');

        return $mandatory_rules;
    }

    public function getMandatoryField($country)
    {
        $mandatory_rules = $this->getMandatoryRules();

        if(isset($mandatory_rules[$country])) {
            return $mandatory[$country];
        }
        return array();
    }

    public function getPayoutCurrencyList()
    {
        $currency_list = [];
        $currency_list['AT'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['AU'] = ['AUD'];
        $currency_list['BE'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['CA'] = ['CAD','USD'];
        $currency_list['CH'] = ['CHF','EUR','DKK','GBP','NOK','SEK','USD'];
        $currency_list['DE'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['DK'] = ['DKK','EUR','GBP','NOK','SEK','USD','CHF'];
        $currency_list['ES'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['FI'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['FR'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['GB'] = ['GBP','EUR','DKK','NOK','SEK','USD','CHF'];
        $currency_list['HK'] = ['HKD'];
        $currency_list['IE'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['IT'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['JP'] = ['JPY'];
        $currency_list['LU'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['MY'] = ['MYR'];
        $currency_list['NZ'] = ['NZD'];
        $currency_list['NL'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['NO'] = ['NOK','EUR','DKK','GBP','SEK','USD','CHF'];
        $currency_list['PT'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency_list['SG'] = ['SGD'];
        $currency_list['SE'] = ['SEK','EUR','DKK','GBP','NOK','USD','CHF'];
        $currency_list['US'] = ['USD'];

        return $currency_list;
    }

    public function getPayoutCurrency($country)
    {
        $currency_list = $this->getPayoutCurrencyList();
        if(isset($currency_list[$country])) {
            return $currency_list[$country];
        }
        return array();
    }

    public function validateRequest($request)
    {
        $user = getCurrentUser();

        $rules = array(
            'country_code' => 'required',
            'account_number'=> 'required',
            'address1' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'legal_document'=> 'required|mimes:png,jpeg,jpg',
            'additional_document'=> 'required|mimes:png,jpeg,jpg',
        );

        $country_code = $request->country_code;

        if($country_code == 'US') {
            $rules['ssn_last_4'] = 'required';
        }

        if($country_code == 'JP') {
            $rules['bank_name'] = 'required';
            $rules['branch_name'] = 'required';
            $rules['address1'] = 'required';
            $rules['kanji_address1'] = 'required';
            $rules['kanji_address2'] = 'required';
            $rules['kanji_city'] = 'required';
            $rules['kanji_state'] = 'required';
            $rules['kanji_postal_code'] = 'required';
        }

        if (!isApiRequest()) {
            $rules['stripe_token'] = 'required';
        }

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

    protected function getVerificationData($request)
    {
        $user = getCurrentUser();
        $country_code = $request->country_code;

        if($country_code == 'JP') {
            $address_kana = array(
                'line1'         => $request->address1,
                'town'          => $request->address2,
                'city'          => $request->city,
                'state'         => $request->state,
                'postal_code'   => $request->postal_code,
                'country'       => $country_code,
            );
            $address_kanji = array(
                'line1'         => $request->kanji_address1,
                'town'          => $request->kanji_address2,
                'city'          => $request->kanji_city,
                'state'         => $request->kanji_state,
                'postal_code'   => $request->kanji_postal_code,
                'country'       => $country_code,
            );
            $individual = array(
                "first_name_kana"   => $user->first_name,
                "last_name_kana"    => $user->last_name,
                "first_name_kanji"  => $user->first_name,
                "last_name_kanji"   => $user->last_name,
                "address" => array(
                    "line1"     => $request->address1,
                    "line2"     => $request->address2,
                    "city"      => $request->city,
                    "country"   => $country_code,
                    "state"     => $request->state,
                    "postal_code" => $request->postal_code,
                ),
                "address_kana"  => $address_kana,
                "address_kanji" => $address_kanji,
            );
        }
        else {
            $holder_name = explode(' ', $request->holder_name);
            $individual = [ 
                "address" => array(
                    "line1"     => $request->address1,
                    "line2"     => $request->address2,
                    "city"      => $request->city,
                    "postal_code"=> $request->postal_code,
                    "state"     => $request->state
                ),
                "dob" => array(
                    "day"   => "15",
                    "month" => "04",
                    "year"  => "1996",
                ),
                "first_name"    => $holder_name[0] ?? $user->first_name,
                "last_name"     => $holder_name[1] ?? $user->last_name,
                "phone"         => $request->phone_number ?? $user->mobile_number ?? "8754727065",
                "email"         => $user->email,
            ];

            if($country_code == 'US') {
                $individual['ssn_last_4'] = $request->ssn_last_4;
            }

            if(in_array($country_code,['CA','MY','SG'])) {
                $individual['id_number'] =  $request->branch_code;
            }
        }

        $capability_countries = ['AU','AT','BE','CH','CZ','DE','DK','EE','ES','FI','FR','GB','GR','IE','IT','LV','LT','LU','MY','NL','NO','NZ','PL','PT','SE','SG','SI','SK','US'];
        $url = url('/');
        if(strpos($url, "localhost") > 0) {
            $url = 'http://hyrahotels.cron24.com';
        }

        $verification = array(
            "country"       => $country_code,
            "business_type" => "individual",
            "business_profile" => array(
                'mcc' => 6513,
                'url' => $url,
            ),
            "tos_acceptance"=> array(
                "date"  => time(),
                "ip"    => $_SERVER['REMOTE_ADDR']
            ),
            "type"          => "custom",
            "individual"    => $individual,
        );

        if(in_array($country_code, $capability_countries)) {
            $verification["requested_capabilities"] = ["transfers","card_payments"];
        }

        return $verification;
    }

    public function createStripeAccount($verification)
    {
        try {
            $recipient = $this->stripe->accounts->create($verification);
            return array(
                'status' => true,
                'recipient' => $recipient,
            );
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function uploadDocument($document_path,$recipient_id)
    {
        try {
            $stripe_file = $this->stripe->files->create(
                array(
                    "purpose"   => "identity_document",
                    "file"      => fopen($document_path, 'r')
                ),
                array('stripe_account' => $recipient_id)
            );

            $stripe_document = $stripe_file->id;

            return array(
                'status'            => true,
                'status_message'    => 'document uploaded',
                'stripe_document'   => $stripe_document,
            );
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function attachDocumentToRecipient($recipient,$document_id,$document_type)
    {
        try {
            $update_data = array(
                'verification' => [
                    $document_type => [
                       'front' => $document_id       
                    ]
                ]
            );
            $this->stripe->accounts->updatePerson($recipient->id,$recipient->individual->id,$update_data);
            return array(
                'status'            => true,
                'status_message'    => 'document attached',
            );
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function createStripeToken($bank_account)
    {
        try {
            $stripe_token = $this->stripe->tokens->create(
                array("bank_account" => $bank_account),
            );
            return [
                'status' => true,
                'token'  => $stripe_token,
            ];
        }
        catch(\Exception $e) {
            return [
                'status'         => false,
                'status_message' => $e->getMessage(),
            ];
        }        
    }

    public function createPayoutAccount($request)
    {
        $verification = $this->getVerificationData($request);
        $recipient_data = $this->createStripeAccount($verification);
        if(!$recipient_data['status']) {
            return array(
                'status' => false,
                'status_message' => $recipient_data['status_message'],
            );
        }
        $recipient = $recipient_data['recipient'];
        $user = getCurrentUser();
        $recipient->email = $user->email;

        try {
            $recipient->external_accounts->create(
                array("external_account" => $request->stripe_token)
            );
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
        $recipient->save();

        return array(
            'status' => true,
            'recipient' => $recipient,
        );
    }

    public function makeAccountLink($account_data)
    {
        try {
            $account_links = $this->stripe->accountLinks->create($account_data);
            return array(
                'status' => true,
                'account_link' => $account_links->url,
            );
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function retrieveStripeAccount($data)
    {
        try {
            $recipient = $this->stripe->accounts->retrieve($data);
            return array(
                'status' => true,
                'recipient' => $recipient,
            );
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function makePayout($payout_account,$pay_data)
    {
        try {
            $response = $this->stripe->transfers->create(array(
                "amount" => $pay_data['amount'] * 100,
                "currency" => $pay_data['currency'],
                "destination" => $payout_account,
                "source_type" => "card"
            ));
        }
        catch (\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }

        return array(
            'status'            => true,
            'status_message'    => \Lang::get('admin_messages.payout_amount_transfered'),
            'transaction_id'    => $response->id,
            'payout_status'     => "Completed",
        );
    }

    public function makeRefund($intent_id,$refund_data)
    {
        try {
            $refund = $this->stripe->refunds->create([
              'amount' => $refund_data["amount"] * 100,
              'payment_intent' => $intent_id,
            ]);
            
            return array(
                'status' => true,
                'status_message' => \Lang::get('admin_messages.amount_refunded_success'),
                'transaction_id' => $refund->id,
                'payout_status' => "Completed",
            );
        }
        catch(\Exception $exception) {
            return array(
                'status' => false,
                'status_message' => $exception->getMessage(),
            );
        }        
    }
}