<?php

/**
 * Cron Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    CronController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payout;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Reservation;
use App\Models\User;
use App\Models\ReferralUser;
use App\Models\ImportedCalendar;
use Illuminate\Support\Facades\Http;
use Lang;

class CronController extends Controller
{
	/**
     * Update Pending paypal payout status
     *
     * @return Json updated payout details
     */
    public function updatePaypalPayouts()
	{
		$pending_payouts = Payout::with('reservation','user.default_payout_method')->where('status','Processing')->get();
		if($pending_payouts->count() == 0) {
			return response()->json(['status' => false, 'status_message' => 'No Pending Payouts found']);
		}

		$paypal_payout = resolve("App\Services\Payouts\PaypalPayout");
		$pending_payouts->each(function($pending_payout) use($paypal_payout) {
			$batch_id = $pending_payout->transaction_id;
			$payment_data = $paypal_payout->fetchPayoutViaBatchId($batch_id);
			if($payment_data['status']) {
				$payout_data = $paypal_payout->getPayoutStatus($payment_data['data']);

				if($payout_data['status']) {
					if($payout_data['payout_status'] == 'SUCCESS') {
						$pending_payout->status = "Completed";
					}

					if(in_array($payout_data['payout_status'], ['FAILED','RETURNED','BLOCKED'])) {
						$pending_payout->status = "Future";
					}
					$pending_payout->save();
				}
			}
		});
		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}

	/**
     * Update Latest Currency Rate
     *
     * @return Json updated currency details
     */
	public function updateCurrencyRate()
	{
		$currency_list = Currency::all();

        $history_data = $currency_list->map(function($currency) {
            return [
                'code' => $currency->code,
                'rate' => $currency->rate,
            ];
        })
        ->values()
        ->toJson();

        $historical_currency = \App\Models\HistoricalCurrency::firstOrNew(['date' => date('Y-m-d')]);
        $historical_currency->rates = $history_data;
        $historical_currency->save();

		$result = array();

		$currency_list->each(function($currency) use (&$result) {
			$rate = 1;
			$result_data = ['status' => "success",'status_message' => "",'code' => $currency->code, 'rate' => $rate];
			try {
				if($currency->code != global_settings('default_currency')) {
					$search_query = '1 '.global_settings('default_currency').' '.$currency->code;

			        $ch = curl_init();
			        curl_setopt($ch, CURLOPT_URL, 'http://www.google.com/search?q='.urlEncode($search_query));
			        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			        $query_result = curl_exec($ch);
			        curl_close($ch);
			        
			        $matches = array();
			        preg_match('/(([0-9]|\.|,|\ )*) '.$currency->name.'/', $query_result, $matches);
			        $rate = 0;
			        if(isset($matches[1])) {
			        	$rate = $matches[1];
			        }

					if($rate > 0) {
						$result_data = ['status' => "success",'status_message' => "",'code' => $currency->code, 'rate' => $rate];
					}
					else {
						$result_data = ['status' => "failed",'status_message' => "Currency Data Not Found",'code' => $currency->code, 'rate' => "-"];
					}
				}
				
				$currency->rate = $rate;
				$currency->save();
			}
			catch(\Exception $e) {
				$result_data = ['status' => "failed",'status_message' => $e->getMessage(),'code' => $currency->code, 'rate' => "-"];
			}

			$result[] = $result_data;
		});

		return response()->json($result);
	}

	/**
     * Update Expired Status Of Booking Request
     *
     * @return Json updated payout details
     */
    public function updateExpiredStatus()
	{
		$pending_requests = \App\Models\Reservation::where('status','Pending')->get();

		$result = array();

		$pending_requests->each(function($reservation) use(&$result) {
			$expired_at = $reservation->created_at->addDay();
			$diff_mins = now()->diffInMinutes($expired_at,0);
			
			$diff_hours = now()->diffInHours($expired_at,0);

			if ($diff_hours == '19' || $diff_hours == '5') {
				resolveAndSendNotification("RequestRemainder",$reservation->id,$diff_hours);
			}
			
			if($diff_mins <= 0) {
				if($reservation->penalty_enabled) {
					$host_cancel_limit = fees('host_cancel_limit');
					$host_cancel_count = \App\Models\Reservation::cancelCount($reservation->host_id)->count();

					if($host_cancel_count >= $host_cancel_limit) {
						$reservation->host_penalty = currencyConvert(fees("cancel_before_days"),global_settings('default_currency'),$reservation->getRawOriginal("currency_code"));
						updateUserPenalty($reservation->host_id,$reservation->currency_code,$reservation->host_penalty);
					}
				}

				$reservation->status = 'Expired';
				$reservation->expired_on = "Host";
				$reservation->save();

				resolveAndSendNotification("requestExpired",$reservation->id);

				$result[] = ['status' => true,'status_message' => "Reservation Request ".$reservation->id." has been Expired"];
			}
		});

		return response()->json($result);
	}

	/**
     * Send Remainder About Review for the Completed Booking
     *
     * @return Json updated payout details
     */
    public function reviewRemainder()
	{
		$reservations = Reservation::where('status','Accepted')
			->where('checkout',date('Y-m-d',strtotime('-1 day')))
			->get();

		if($reservations->count() == 0) {
			return response()->json(['status' => false, 'status_message' => 'No Recently Completed Reservations found']);
		}

		$result = array();

		$reservations->each(function($reservation) use(&$result) {
			if($reservation->checkoutCrossed()) {
				resolveAndSendNotification("writeReview",$reservation->id);
				$result[] = ['status' => true,'status_message' => "Review Remainder for Reservation ".$reservation->id." has been Sent"];
			}
		});

		return response()->json($result);
	}

	/**
     * Sync Calendars
     *
     * @return Json updated calendar result
     */
    public function syncCalendars()
	{
		$calendars = ImportedCalendar::get();

		$result = [];
		$calendar_controller = resolve("App\Http\Controllers\CalendarController");

		$calendars->each(function($calendar) use(&$result,$calendar_controller) {
			$calendar_controller->syncCalendar($calendar);
			$result[] = ['status' => true,'status_message' => "Calendar #".$calendar->id." has been Imported"];
		});

		return response()->json($result);
	}

	public function updateUserStatus()
	{
		$user_active_days = date("Y-m-d", strtotime("-".global_settings('user_inactive_days')."day"));
		if (global_settings('user_inactive_days') > 0) {
			$users = User::activeOnly()->where('last_active_at', '<' ,$user_active_days)->update(['status' => 'Inactive']);
		}
		$result = ['status' => true, 'status_message' => Lang::get('messages.user_status_update_success')];
		return response()->json($result);
	}

	public function autoPayout()
	{ 
		$all_payouts = Payout::with('hotel_reservation','experience_reservation')->where('status','Future')->get();

		$result = [];
		$payout_controller = resolve("App\Http\Controllers\Admin\PayoutController");

		$all_payouts->each(function($payout) use (&$result,$payout_controller) {
			if($payout->reservation->adminAbletoPayout()){
				try {
					$payout_controller->processPayout($payout->id);
					$result[] = "Payout transferred for ".$payout->reservation_id;
				}
				catch(\Exception $e){
					
				}
			}
		});
		$response = ['status' => true, 'status_message' => Lang::get('admin_messages.payout_amount_transfered'),$result => $result];
		return response()->json($response);
	}

	/**
     * Update Referral Credit
     *
     * @return Json $result
     */
    public function referralCredit()
	{
		$reservations = Reservation::where('status','Accepted')
			->where('checkout','=',date('Y-m-d',strtotime('-1 day')))
			->get();

		if($reservations->count() == 0) {
			return response()->json(['status' => false, 'status_message' => 'No Completed Reservations']);
		}

		$result = array();

		$reservations->each(function($reservation) use(&$result) {
			if($reservation->checkoutCrossed()) {
				$referral_user = ReferralUser::where('referral_user_id',$reservation->user_id)->where('user_become_guest_status',0)->first();
				if($referral_user != '') {
					$referral_user->user_credited_amount = $referral_user->getRawOriginal('user_credited_amount') + $referral_user->getRawOriginal('user_become_guest_amount');
					$referral_user->user_become_guest_status = 1;
					$referral_user->save();					
					$result[] = ['status' => true,'status_message' => "Referral #".$referral_user->id." has been Completed and guest referral amount credited"];
				}
			}
		});

		return response()->json($result);
	}


	public function sendAdminReport()
	{
		$today = date('Y-m-d');

		$reservations = Reservation::where('checkout',$today)->get();
		$reservations->map(function ($reservation) {
			resolveAndSendNotification('generateReport',$reservation->id);
		});
		return response()->json(['status' => true]);
	}
}
