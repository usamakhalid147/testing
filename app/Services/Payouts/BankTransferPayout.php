<?php

/**
 * Handle Bank Transfer Payout Information
 *
 * @package     HyraHotel
 * @subpackage  Services\Payouts
 * @category    BankTransferPayout
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\Payouts;

class BankTransferPayout
{
    public function validateRequest($request)
    {
        $rules = array(
            'bank_holder_name' => 'required',
            'bank_account_number'=> 'required',
            'bank_name' => 'required',
            'bank_code' => 'required',
            'bank_location' => 'required',
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
        $recipient['email'] = $request->bank_account_number;
        $recipient['id'] = $request->bank_account_number;
    	return array(
    		'status' => true,
    		'recipient' => $recipient,
    	);
    }

    public function makePayout($payout_account,$pay_data)
    {
        return array(
            'status' => true,
            'status_message' => \Lang::get('admin_messages.payout_status_transfered'),
            'transaction_id' => "",
            'payout_status' => "Completed",
        );
    }
}