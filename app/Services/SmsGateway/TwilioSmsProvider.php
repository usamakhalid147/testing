<?php

/**
 * Twilio SMS Provider
 *
 * @package     HyraHotel
 * @subpackage  Services\SmsGateway
 * @category    TwilioSmsProvider
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\SmsGateway;

use App\Contracts\SmsGateway;
use Lang;

class TwilioSmsProvider implements SmsGateway
{
	private $base_url,$account_sid,$auth_token,$from_number;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->account_sid = credentials('account_sid','Twilio');
		$this->auth_token = credentials('auth_token','Twilio');
		$this->from_number = credentials('from_number','Twilio');
		$this->base_url = "https://api.twilio.com/2010-04-01/Accounts/".$this->account_sid."/Messages.json";
	}

	/**
	 * Send Text message
	 *
	 * @param Array $data
	 * @return Array $response
	 */
	protected function sendSMS($data)
	{
		$curl = curl_init($this->base_url);
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_USERPWD, $this->account_sid.":".$this->auth_token);
		
		$result = curl_exec($curl);

		if (curl_errno($curl)) {
		    return [
		    	'status' => "failed",
		    	'message' => Lang::get('messages.failed'),
		    ];
		}
		curl_close($curl);

		return json_decode($result,true);
	}

	/**
	 * Get Response from restult
	 *
	 * @param Array $result
	 * @return Array $response
	 */
	protected function getResponse($result)
	{
		if ($result['status'] != 'queued') {
			return [
				'status' => false,
				'status_message' => $result['message']
			];
		}

		return [
			'status' => true,
			'status_message' => Lang::get('messages.success'),
		];
	}

	/**
	 * Send SMS to Given Number
	 *
	 * @param String $number
	 * @param Array $data
	 * @return Array $response
	 */
	public function send($number, $data)
	{
		if(displayCrendentials()) {
			return [
				'status' => true,
				'message' => $data['text'],
				'status_message' => Lang::get('messages.success'),
			];
		}
		$sms_data = array(
			"Body" => $data['text'],
			"From" => $this->from_number,
			"To"=> '+'.$number
		);

		return $this->getResponse($this->sendSMS($sms_data));
	}
}