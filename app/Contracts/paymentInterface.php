<?php

/**
 * Interface that describe Payment
 *
 * @package     HyraHotel
 * @subpackage  Contracts
 * @category    Payment Interface
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Contracts;

use Illuminate\Http\Request;

interface paymentInterface
{
	public function preparePayment();
	public function validationData();
	public function paymentDetailsFromRequest(Request $request);
	public function doPayment(array $payment_data,array $purchase_data);
	public function isTwoStep();
	public function getPayment(string $pay_key);
}