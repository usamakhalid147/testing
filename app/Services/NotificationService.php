<?php

/**
 * Service to send email or notification to user, host and admin
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    NotificationService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Admin;
use App\Models\Message;
use App\Models\MessageConversation;
use App\Models\Payout;
use App\Models\ReferralUser;
use App\Models\Credential;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Config;

use Auth;
use Lang;

class NotificationService
{
	function __construct()
	{
		$this->default_locale = global_settings('default_language');
		$this->currentDateTime = date(DATE_FORMAT);
	}

	/**
	 * Get Hotel data
	 *
	 * @param  \App\Models\Hotel $hotel
	 * @return Array
	 */
	protected function getHotelData($hotel)
	{
		$hotel_data['list_type'] = 'hotel';
		$hotel_data['listing_name'] = $hotel->name;
		$hotel_data['listing_link'] = $hotel->link;
		$hotel_data['listing_thumb'] = $hotel->image_src;
		$hotel_data['room_type_name'] = $hotel->room_type_name;
		$hotel_data['property_type_name'] = $hotel->property_type_name;
		$hotel_data['address_line'] = $hotel->hotel_address->address_line_display;
		$hotel_data['latitude'] = $hotel->hotel_address->latitude;
		$hotel_data['longitude'] = $hotel->hotel_address->longitude;
		
		return $hotel_data;
	}

	/**
	 * Get Reservation data
	 *
	 * @param  \App\Models\Reservation $reservation
	 * @return Array
	 */
	protected function getReservationData($reservation,$special_offer_id='')
	{
		$reservation_data['id'] = $reservation->id;
		$reservation_data['code'] = $reservation->code;
		$reservation_data['status'] = $reservation->status;
		$reservation_data['guests'] = $reservation->guests;
		$reservation_data['list_type'] = 'hotel';
		if($reservation->status == 'Accepted') {
			$reservation_data['itinerary_link'] = resolveRoute('view_itinerary',['code' => $reservation->code]);
			$reservation_data['receipt_link'] = resolveRoute('view_receipt',['code' => $reservation->code]);
		}
		else if($reservation->status == 'Pending' || $reservation->status == 'Inquiry') {
			$message = Message::where('reservation_id',$reservation->id)->first();
			$reservation_data['inbox_link'] = resolveRoute('conversation',['id' => $message->id]);
		}
		else if($reservation->status == 'Pre-Accepted' || $reservation->status == 'Pre-Approved') {
			$reservation_data['booking_url'] = resolveRoute('confirm_reserve',['reservation_id' => $reservation->id]);
			if ($special_offer_id != '' || $special_offer_id != null) {
			$reservation_data['booking_url'] = resolveRoute('confirm_reserve',['reservation_id' => $reservation->id,'special_offer_id' => $special_offer_id]);
			}
		}
		else if(in_array($reservation->status,['Declined','Expired'])) {
			$message = Message::where('reservation_id',$reservation->id)->first();
			$reservation_data['search_url'] = resolveRoute('search');
			$reservation_data['inbox_link'] = resolveRoute('conversation',['id' => $message->id]);
		}
		$reservation_data['checkin'] = $reservation->formatted_checkin;
		$reservation_data['checkout'] = $reservation->formatted_checkout;
		$reservation_data['room_type'] = $reservation->hotel->room_type_name;
		$reservation_data['checkin_at'] = $reservation->getTimingText('checkin_at');
		$reservation_data['checkout_at'] = $reservation->getTimingText('checkin_at');

		return $reservation_data;
	}

	/**
	 * Get User data
	 *
	 * @param  \App\Models\User $user
	 * @return Array
	 */
	protected function getUserData($user)
	{
		$user_data['user_name'] = $user->first_name;
		$user_data['since'] = $user->created_at->format('F Y');
		$user_data['user_profile_pic'] = $user->profile_picture_src;

		return $user_data;
	}

	/**
	 * Send Email Via Swift Mailer
	 *
	 * @param  \Illuminate\Mail\Mailable $mailable
	 * @param  string $email
	 * @param  string $first_name default empty
	 * @return Array
	 */
	public function sendEmail($mailable, $email,$locale = '')
	{
		try {
			if($locale == '') {
				$locale = $this->default_locale;
			}
			Mail::to($email)->locale($locale)->queue($mailable);
			return [
				'status' => true,
				'status_message' => Lang::get('messages.mail_sent_successfully'),
			];
			
		}
		catch (\Exception $e) {
			return [
				'status' => false,
				'status_message' => Lang::get('messages.failed_to_send_mail'),
				'error_message' => $e->getMessage(),
			];
		}
	}

	/**
	 * Send Message
	 *
	 * @param  string $user
	 * @param  string $text
	 * @return Array
	 */
	public function sendSms($user, $message_text = '')
	{
		try {
			$sms_service = resolve("App\Contracts\SmsGateway");
			$number = $user->phone_code.$user->phone_number;
			if($number == '') {
				return [
					'status' => false,
					'status_message' => Lang::get('messages.failed_to_sent'),
					'error_message' => Lang::get('messages.failed_to_sent'),
				];
			}

			$data = [
				'text' => global_settings('site_name').': '.$message_text,
			];
			$result = $sms_service->send($number,$data);
			return [
				'status' => true,
				'status_message' => Lang::get('messages.sms_sent_successfully'),
			];
		}
		catch (\Exception $e) {
			return [
				'status' => false,
				'status_message' => Lang::get('messages.failed_to_sent'),
				'error_message' => $e->getMessage(),
			];
		}
	}

	/**
	 * Send Mail to All Primary Admins
	 *
	 * @param  \Illuminate\Mail\Mailable $mailable
	 */
	public function sendEmailToAdmins($mailable)
	{
		$admins = Admin::primaryUsers()->get();
		
		$result = [];
		$admins->each(function($admin) use($mailable,&$result) {
			$mailable = $mailable->mergeData(['admin_name' => $admin->username]);
			$result = $this->sendEmail($mailable,$admin->email);
		});

		return $result;
	}

	/**
	 * Send Custom Mail to Users
	 *
	 * @param  Array $mail_data
	 * @param  Array $user_data
	 */
	public function customMail($mail_data,$user_data)
	{
		$mailable = new \App\Mail\Admin\CustomMail($mail_data);

		$result = $this->sendEmail($mailable,$user_data['email'],$user_data['locale']);
		
		return $result;
	}

	/**
	 * Send Contact Us Mail to Admin
	 *
	 * @param  Array $contact_data
	 */
	public function contactAdmin($contact_data)
	{
		$mailable = new \App\Mail\Admin\ContactAdmin($contact_data);

		return $this->sendEmailToAdmins($mailable);
	}

    /**
	 * Send Confirmation Mail To User
	 *
	 * @param  Integer $user_id
	 */
	public function confirmUserEmail($user_id)
	{
		$user = User::find($user_id);
		$mail_data['name'] = $user->first_name;
		$mail_data['link'] = resolveRoute('login');
		if ($user->user_type == 'host') {
			$mail_data['link'] = resolveRoute('host.login');
		}
		if($user->status != 'active') {
			$mail_data['subject'] = Lang::get('messages.welcome_mail_hotelier',[],$user->user_language);
			$mailable = new \App\Mail\ConfirmUserEmail($mail_data);
			$mail_data['text'] = Lang::get('messages.new_user_signup_desc');
		}
		else {
			$mail_data['subject'] = Lang::get('messages.welcome_mail',[],$user->user_language).' '.global_settings('site_name');
			if ($user->user_type == 'host') {
				$mailable = new \App\Mail\HotelierWelcomeUserMail($mail_data);
			}
			else
			{
				$mailable = new \App\Mail\WelcomeUserMail($mail_data);
			}
			$mail_data['text'] = Lang::get('messages.new_user_signup_desc1');
		}
		if(isset($user->user_type))
		{
			if($user->user_type=='host')
			{
				$mailerDetail='HotelierEmailConfig';
				return;
			}
			else if($user->user_type=='user')
			{
				$mailerDetail='MemberEmailConfig';

			}
			$host = Credential::where('site', $mailerDetail)
				->where('name', 'host')
				->first()->value;
			$port = Credential::where('site', $mailerDetail)
				->where('name', 'port')
				->first()->value;
			$from_address = Credential::where('site', $mailerDetail)
				->where('name', 'from_address')
				->first()->value;
			$from_name = Credential::where('site', $mailerDetail)
				->where('name', 'from_name')
				->first()->value;
			$encryption = Credential::where('site', $mailerDetail)
				->where('name', 'encryption')
				->first()->value;
			$username = Credential::where('site', $mailerDetail)
				->where('name', 'username')
				->first()->value;
			$password = Credential::where('site', $mailerDetail)
				->where('name', 'password')
				->first()->value;
			$password = Credential::where('site', $mailerDetail)
				->where('name', 'password')
				->first()->value;
			
			Config::set('mail.mailers.smtp', [
				'transport' => 'smtp',
				'host' => $host,
				'port' => $port,
				'username' => $username,
				'password' => $password,
				'encryption' => $encryption,
			]);

			Config::set('mail.from.address', $from_address);
			Config::set('mail.from.name', $from_name);
		}
		(new MailServiceProvider(app()))->register();
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		return $result;
	}

	/**
	 * Send Confirmation Mail To User
	 *
	 * @param  Integer $user_id
	 * @param  Array $activity_data
	 */
	public function userActivity($user_id,$activity_data)
	{
		$user = User::find($user_id);
		$mail_data['name'] = $user->first_name;
		$mail_data['subject'] = Lang::get('messages.account_activity',[],$user->user_language);
		if($activity_data['type'] == 'password_changed') {
			$mail_data['subject'] .= ': '.Lang::get('messages.password_changed',[],$user->user_language);
			$mail_data['when'] = now($user->timezone)->format('D d F Y, H:i T');
			$ip_data = getIpBasedData($_SERVER['REMOTE_ADDR']);
			$mail_data['where'] = $ip_data['address'];
			$mail_data['device'] = '';
			$mail_data['review_link'] = resolveRoute('update_account_settings',[ 'page'=> 'login-and-security']);
		}

		$mailable = new \App\Mail\UserActivity($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		
		return $result;
	}

	/**
	 * Send Confirmation Mail To User
	 *
	 * @param  Integer $user_id
	 */
	public function resetUserPassword($user_id)
	{
		$user = User::find($user_id);

		$mail_data['name'] = $user->first_name;
		$mail_data['reset_link'] = $user->resetPasswordUrl('reset_password');

		$mail_data['subject'] = Lang::get('messages.reset_your_password',[],$user->user_language);

		$mailable = new \App\Mail\ResetUserPassword($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		
		return $result;
	}

	/**
	 * Send Confirmation Mail To User
	 *
	 * @param  Integer $user_id
	 */
	public function resetHostPassword($user_id)
	{
		$user = User::authBased()->find($user_id);

		$mail_data['name'] = $user->first_name;
		$mail_data['reset_link'] = $user->resetPasswordUrl('host.reset_password');

		$mail_data['subject'] = Lang::get('messages.reset_your_password',[],$user->user_language);

		$mailable = new \App\Mail\ResetUserPassword($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		
		return $result;
	}

	/**
	 * Send Notification when add or update Payment Method 
	 *
	 * @param  Integer $user_id
	 * @param  Integer $payment_method_id
	 */
	public function paymentMethodNotification($user_id,$payment_method_id)
	{
		$user = User::find($user_id);

		$mail_data['subject'] = Lang::get('messages.payment_method_added',[],$user->user_language);

		$mailable = new \App\Mail\PaymentMethodNotification($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		
		return $result;
	}

	/**
	 * Send Email when Listings Details Updated
	 *
	 * @param  Integer $listing_id
	 * @param  Array $update_data
	 */
	public function listingDetailsUpdated($listing_id,$update_data)
	{
		$hotel = Hotel::find($hotel_id);
		$user = $hotel->user;

		$mail_data['name'] = $user->first_name;
		$mail_data['listing_link'] = $hotel->link;
		$mail_data['listing_name'] = $hotel->name;
		$mail_data['date'] = $this->currentDateTime;
		$mail_data['field'] = $update_data['field'];
		$mail_data['subject'] = Lang::get('messages.listing_details_updated',[],$user->user_language);

		$this->sendSms($user,$mail_data['subject']);
		
		$mailable = new \App\Mail\ListingDetailsUpdated($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);

		return $result;
	}

	/**
	 * Send Email when Listings Status Changed
	 *
	 * @param  Integer $listing_id
	 * @param  String $status
	 */
	public function listingStatusUpdated($listing_id,$status)
	{
		$hotel = Hotel::find($listing_id);
		$user = $hotel->user;

		$mail_data['name'] = $user->first_name;
		$mail_data['status'] = $status;
		$mail_data['listing_link'] = $hotel->link;
		$mail_data['listing_name'] = $hotel->name;
		// $mail_data['listing_edit_link'] = resolveRoute('listing_home',['id' => $hotel->id]);
		$mail_data['date'] = $this->currentDateTime;

		if($status == "listed") {
			$mail_data['subject'] = Lang::get('messages.your_listing_listed_on',[],$user->user_language).' '.SITE_NAME;
		}
		else {
			$mail_data['subject'] = Lang::get('messages.listing_deactivated_from',[],$user->user_language).' '.SITE_NAME;
		}
		
		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\ListingStatusUpdated($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);

		return $result;
	}

	/**
	 * Send awaiting For Approval mail to Host and Admin
	 *
	 * @param  Integer $listing_id
	 */
	public function awaitingForApproval($listing_id)
	{
		$hotel = Hotel::find($hotel_id);
		$user = $hotel->user;

		$mail_data['name'] = $user->first_name;
		$mail_data['listing_name'] = $hotel->name;
		$mail_data['listing_link'] = $hotel->link;

		$mail_data['date'] = $this->currentDateTime;
		$mail_data['subject'] = Lang::get('messages.listing_sent_for_approval',[],$user->user_language);

		$mailable = new \App\Mail\AwaitingForApprovalHost($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		$mail_data['subject'] = Lang::get('messages.listing_waiting_for_your_approval',[],$this->default_locale);

		$this->sendSms($user,$mail_data['subject']);
		
		$mailable = new \App\Mail\Admin\AwaitingForApprovalAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Listing Approved Notification mail to Host
	 *
	 * @param  Integer $listing_id
	 */
	public function listingApproved($listing_id)
	{
		$hotel = Hotel::find($hotel_id);
		$user = $hotel->user;

		$mail_data['name'] = $user->first_name;
		$mail_data['listing_name'] = $hotel->name;
		$mail_data['listing_link'] = resolveRoute('room_details',['id' => $hotel->id]);
		$mail_data['listing_edit_link'] = resolveRoute('listing_home',['id' => $hotel->id]);
		$mail_data['date'] = $this->currentDateTime;
		$mail_data['name'] = $user->first_name;
		$mail_data['subject'] = Lang::get('messages.your_listing_approved',[],$user->user_language).' '.SITE_NAME;

		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\ListingApprovedByAdmin($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		return $result;
	}

	/**
	 * Send Booking Confirmed Mail to User, Host and Admin
	 *
	 * @param  Integer $reservation_id
	 */
	public function bookingConfirmed($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.your_booking_confirmed',[],$user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.you_are_going_to',[],$user->user_language).' '.$hotel->hotel_address->city;
		$mail_data['subject'] = Lang::get('messages.booking_confirmed',[],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];

		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\BookingConfirmedGuest($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);
		
		session(['currency' => $host_user->currency_code]);
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.booking_confirmed',[],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.new_booking_for',[],$host_user->user_language).' '.$hotel->name;
		$mail_data['header_subtitle'] = Lang::get('messages.you_will_earn',[],$host_user->user_language).' '.$reservation->currency_symbol.$reservation->calcHostPayoutAmount();

		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Host"),
		];
		
		$this->sendSms($host_user,$mail_data['subject']);

		$mailable = new \App\Mail\BookingConfirmedHost($mail_data);
		$this->sendEmail($mailable,$hotel->contact_email,$host_user->user_language);

		$mail_data['subject'] = Lang::get('messages.booking_confirmed',[],$this->default_locale);
		$mail_data['header_subtitle'] = Lang::get('messages.confirmed_by',[],$host_user->user_language).' '.$user->first_name;

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Admin"),
		];

		$mailable = new \App\Mail\Admin\BookingConfirmedAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Share Booking Confirmed Itinerary to Others
	 *
	 * @param  Integer $reservation_id
	 */
	public function itineraryShared($request_data)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($request_data['reservation_id']);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.guest_share_itinerary',['guest_name' => $user->first_name],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.guest_share_itinerary',['guest_name' => $user->first_name],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['list_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];

		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\ItineraryShared($mail_data);
		$result = $this->sendEmail($mailable,$request_data['email'],$user->user_language);
		return $result;
	}

	/**
	 * Send Booking Cancelled Mail to Guest, Host and Admin
	 *
	 * @param  Integer $reservation_id
	 * @param  String $cancelled_by Guest | Host
	 */
	public function bookingCancelled($reservation_id,$cancelled_by)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$cancelled_user = ($cancelled_by == 'Guest') ? $user : $host_user;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.booking_cancelled_by',[],$user->user_language).' '.$cancelled_user->first_name;
		$mail_data['header_subtitle'] = Lang::get('messages.in',[],$user->user_language).' '.$reservation->hotel->name;
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');

		$mail_data['hotel_data'] = $this->getHotelData($reservation->hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);

		if($cancelled_by == 'Guest') {
			$mail_data['subject'] = Lang::get('messages.booking_cancelled_by',[],$host_user->user_language).' '.Lang::get('messages.'.strtolower($cancelled_by),[],$host_user->user_language);
			$this->sendSms($host_user,$mail_data['subject']);
			$mailable = new \App\Mail\BookingCancelledHost($mail_data);
			$this->sendEmail($mailable,$host_user->email,$host_user->user_language);
		}
		else {
			$mail_data['subject'] = Lang::get('messages.booking_cancelled_by',[],$user->user_language).' '.Lang::get('messages.'.strtolower($cancelled_by),[],$user->user_language);
			$this->sendSms($user,$mail_data['subject']);
			$mailable = new \App\Mail\BookingCancelledGuest($mail_data);
			$this->sendEmail($mailable,$user->email,$user->user_language);
		}

		$mail_data['subject'] = Lang::get('messages.booking_cancelled_by',[],$this->default_locale).' '.Lang::get('messages.'.strtolower($cancelled_by),[],$this->default_locale);
		
		$mailable = new \App\Mail\Admin\BookingCancelledAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail about Booking Request to Guest and Host
	 *
	 * @param  Integer $reservation_id
	 */
	public function newRequestFromGuest($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.your_request_sent_to_host',[],$user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.wait_unitl_host_responds',[],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.your_request_sent_to_host',[],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];
		
		$mailable = new \App\Mail\RequestSentToHost($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		session(['currency' => $host_user->currency_code]);
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.new_request_from_guest',['replace_key_1' => $user->first_name],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.new_request_from_guest_for_listing',['replace_key_1' => $user->first_name,'replace_key_2' => $hotel->name],$host_user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.respond_quickly_to_maintain_rate',[],$host_user->user_language);

		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Host"),
		];

		$this->sendSms($host_user,$mail_data['subject']);
		
		$mailable = new \App\Mail\RequestFromGuest($mail_data);
		$this->sendEmail($mailable,$host_user->email,$host_user->user_language);

		$mail_data['header_subtitle'] = Lang::get('messages.sent_to_host',['replace_key_1' => $host_user->first_name],$host_user->user_language).' '.$user->first_name;

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Admin"),
		];

		$mailable = new \App\Mail\Admin\NewBookingRequest($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail Notification about Booking Request expire time to Host
	 *
	 * @param  Integer $reservation_id
	 */
	public function RequestRemainder($reservation_id,$time)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.guest_request_expire_soon',['guest_name' => $user->first_name,'time'=>$time],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.guest_request_expire_soon',['guest_name' => $user->first_name,'time'=>$time],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];
		$mail_data['respond_to_inquired_text'] = Lang::get('messages.respond_to_inquired',['guest_name' => $user->first_name],$user->user_language);
		$mail_data['going_to_expired_text'] = Lang::get('messages.going_to_expired',['time' => $time,'room' => $hotel->name],$user->user_language);
		$mailable = new \App\Mail\RequestRemainderForHost($mail_data);
		$result = $this->sendEmail($mailable,$host_user->email,$user->user_language);
		return $result;
	}

	/**
	 * Send Mail about Booking Request Expired to Guest, Host and Admin
	 *
	 * @param  Integer $reservation_id
	 */
	public function requestExpired($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.host_didnt_reponds',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.host_didnt_reponds',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$this->sendSms($user,$mail_data['subject']);
		
		$mailable = new \App\Mail\RequestExpiredGuest($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		session(['currency' => $host_user->currency_code]);
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.request_for_listing',['replace_key_1' => $user->first_name,'replace_key_2' => $hotel->name],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.request_for_listing',['replace_key_1' => $user->first_name,'replace_key_2' => $hotel->name],$host_user->user_language);

		$this->sendSms($host_user,$mail_data['subject']);
		
		$mailable = new \App\Mail\RequestExpiredHost($mail_data);
		$this->sendEmail($mailable,$host_user->email,$host_user->user_language);

		$mail_data['header_subtitle'] = Lang::get('messages.sent_by_guest_to_host',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language).' '.$user->first_name;

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Admin"),
		];

		$mailable = new \App\Mail\Admin\RequestExpiredAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail to Guest and Admin about Request Pre Accepted
	 *
	 * @param  Integer $reservation_id
	 */
	public function requestPreAccepted($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.your_request_accepted_by_host',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.complete_payment_to_confirm',[],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.your_request_accepted_by_host',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];
		
		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\RequestPreAccepted($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.host_accepted_guest_request',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.host_accepted_guest_request',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language);
		$mail_data['header_subtitle'] = "";

		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Admin"),
		];

		$mailable = new \App\Mail\Admin\RequestPreAcceptedAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail to Guest and Admin about Request Declined
	 *
	 * @param  Integer $reservation_id
	 */
	public function requestDeclined($reservation_id)
	{
		$reservation = Reservation::with('user','host','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.your_request_declined_by_host',['replace_key_1' => $host_user->first_name],$user->user_language);
		$message = Message::where('reservation_id',$reservation->id)->first();
		$decline_message = MessageConversation::where('message_id',$message->id)->where('message_type',getMessageType('booking_declined'))->first();
		$mail_data['header_subtitle'] = '<span style="font-weight:bold">'.$host_user->first_name.':</span> '.optional($decline_message)->message;
		$mail_data['subject'] = Lang::get('messages.your_request_declined_by_host',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$this->sendSms($user,$mail_data['subject']);
		
		$mailable = new \App\Mail\RequestDeclined($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.user_request_declined_by_host',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.user_request_declined_by_host',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language);

		$mailable = new \App\Mail\Admin\RequestDeclinedAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail about Inquiry to Guest and Host
	 *
	 * @param  Integer $reservation_id
	 */
	public function newInquiryFromGuest($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.new_inquiry_from_guest_for_listing',['replace_key_1' => $user->first_name,'replace_key_2' => $hotel->name],$user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.wait_unitl_host_responds',[],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.new_inquiry_from_guest_for_listing',['replace_key_1' => $user->first_name,'replace_key_2' => $hotel->name],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];

		$mailable = new \App\Mail\RequestSentToHost($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		session(['currency' => $host_user->currency_code]);
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.new_inquiry_from_guest',['replace_key_1' => $user->first_name],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.new_inquiry_from_guest_for_listing',['replace_key_1' => $user->first_name,'replace_key_2' => $hotel->name],$host_user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.respond_quickly_to_maintain_rate',[],$host_user->user_language);

		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Host"),
		];

		$this->sendSms($host_user,$mail_data['subject']);
		
		$mailable = new \App\Mail\RequestFromGuest($mail_data);
		$this->sendEmail($mailable,$host_user->email,$host_user->user_language);

		$mail_data['header_subtitle'] = Lang::get('messages.sent_to_host',['replace_key_1' => $host_user->first_name],$host_user->user_language).' '.$user->first_name;

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Admin"),
		];

		$mailable = new \App\Mail\Admin\NewBookingRequest($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail to Guest and Admin about Request Pre Approved
	 *
	 * @param  Integer $reservation_id
	 */
	public function requestPreApproved($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.your_request_accepted_by_host',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.complete_payment_to_confirm',[],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.your_request_accepted_by_host',['replace_key_1' => $host_user->first_name],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];
		
		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\RequestPreAccepted($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		session()->forget('currency');
		$reservation = Reservation::find($reservation_id);
		$mail_data['subject'] = Lang::get('messages.host_accepted_guest_request',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language);
		$mail_data['header_title'] = Lang::get('messages.host_accepted_guest_request',['replace_key_1' => $user->first_name,'replace_key_2' => $host_user->first_name],$host_user->user_language);
		$mail_data['header_subtitle'] = "";

		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Admin"),
		];

		$mailable = new \App\Mail\Admin\RequestPreAcceptedAdmin($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Mail about Host Sent a Special offer to Guest
	 *
	 * @param  Integer $reservation_id
	 */
	public function specialOfferSent($reservation_id,$special_offer_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;
		$special_offer = SpecialOffer::findorFail($special_offer_id);

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = Lang::get('messages.host_sent_special_offer_to_you',['replace_key_1' => $host_user->first_name,'room' => $hotel->name,'booking_date' => getDateInFormat($reservation->checkin)."-".getDateInFormat($reservation->checkout)],$user->user_language);
		$mail_data['header_subtitle'] = Lang::get('messages.complete_payment_to_confirm',[],$user->user_language);
		$mail_data['subject'] = Lang::get('messages.host_sent_special_offer_to_you',['replace_key_1' => $host_user->first_name,'room' => $hotel->name,'booking_date' => getDateInFormat($reservation->checkin)."-".getDateInFormat($reservation->checkout)],$user->user_language);
		$mail_data['help_link'] = resolveRoute('help');
		$mail_data['contact_link'] = resolveRoute('contact_us');
		
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['reservation_data'] = $this->getReservationData($reservation,$special_offer_id);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['user_data'] = $this->getUserData($user);
		$expired_at = $special_offer->created_at->addDay();
		$mail_data['diff_hours'] = now()->diffInHours($expired_at,0);
		$mail_data['sent_special_offer_text'] = Lang::get('messages.sent_special_offer',['host_name' => $host_user->first_name,'room' => $hotel->name,'time' => $mail_data['diff_hours']],$user->user_language);
		
		$mail_data['price_data'] = [
			'pricing_form' => $reservation->getPricingForm("Guest"),
		];
		
		$this->sendSms($user,$mail_data['subject']);

		$mailable = new \App\Mail\SentSpecialOffer($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);

		return $result;
	}

	/**
	 * Send Notification mail to User about Reservation Conversation Message
	 *
	 * @param  Integer $message_id
	 * @param  Array $message_data
	 */
	public function userConversation($message_id)
	{
		$message = MessageConversation::findOrFail($message_id);
		$messages = Message::findOrFail($message->message_id);

		if($messages->list_type != 'hotel') {
			return true;
		}

		$hotel = Hotel::findOrFail($messages->list_id);
		$sent_user = $message->from_user;
		$user = $message->to_user;

		$mail_data['name'] = $user->first_name;
		$mail_data['subject'] = Lang::get('messages.you_got_new_message_from',[],$user->user_language).' '.$sent_user->first_name;
		$mail_data['hotel_data'] = $this->getHotelData($hotel);
		$mail_data['user_data'] = $this->getUserData($sent_user);
		$mail_data['message_data'] = $message;
		$mailable = new \App\Mail\UserConversation($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		
		return $result;
	}

	/**
	 * Send request to add account details Mail To User
	 *
	 * @param  Integer $user_id
	 */
	public function addAccountDetails($user_id)
	{
		$user = User::find($user_id);

		$mail_data['name'] = $user->first_name;
		$mail_data['subject'] = Lang::get('messages.add_account_details_time_to_paid',[],$user->user_language);
		$mail_data['add_account_link'] = resolveRoute('host.payout_methods');

		$mailable = new \App\Mail\AddAccountDetails($mail_data);
		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		
		return $result;
	}

	/**
	 * Send Payout Sent Notification Mail To Host and Admin
	 *
	 * @param  Integer $payout_id
	 */
	public function payoutIssued($payout_id)
	{
		$payout = Payout::with('user')->findOrFail($payout_id);
		$user = $payout->user;
		$currency = resolve("Currency")->where('code',$user->currency_code)->first();
		$mail_data['amount'] = $currency->symbol.currencyConvert($payout->amount,$payout->currency_code,$user->currency_code);
		$mail_data['date'] = $this->currentDateTime;
		$mail_data['list_type'] = $list_type = $payout->list_type;
		$mail_data['detail'] = $payout->reservation()->$list_type->name;
		$mail_data['name'] = $user->first_name;
		$mail_data['transaction_history_link'] = resolveRoute('update_account_settings',['page' => 'transactions']);
		$mail_data['subject'] = Lang::get('messages.payout_sent_to_your_account',[],$user->user_language).' '.SITE_NAME;

		$mailable = new \App\Mail\PayoutIssuedToHost($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		$mail_data['subject'] = Lang::get('messages.payout_sent_to_host',[],$this->default_locale);

		$mailable = new \App\Mail\Admin\PayoutSent($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;

	}

	/**
	 * Send Refund Processed Notification Mail To Guest And Admin
	 *
	 * @param  Integer $payout_id
	 */
	public function refundProcessed($payout_id)
	{
		$payout = Payout::with('user')->findOrFail($payout_id);
		$user = $payout->user;
		$currency = resolve("Currency")->where('code',$user->currency_code)->first();
		$mail_data['amount'] = $currency->symbol.currencyConvert($payout->amount,$payout->currency_code,$user->currency_code);
		$mail_data['date'] = $this->currentDateTime;
		$mail_data['list_type'] = $list_type = $payout->list_type;
		$mail_data['listing_name'] = $payout->reservation()->$list_type->name;
		$mail_data['room_type'] = '';
		$mail_data['name'] = $user->first_name;
		$mail_data['transaction_history_link'] = resolveRoute('update_account_settings',['page' => 'transactions']);

		$mail_data['subject'] = Lang::get('messages.refund_processed_to_your_account',[],$user->user_language).' '.SITE_NAME;

		$mailable = new \App\Mail\RefundProcessed($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);

		$mail_data['subject'] = Lang::get('messages.refund_sent_to_guest',[],$this->default_locale);

		$mailable = new \App\Mail\Admin\RefundProcessedToGuest($mail_data);
		$result = $this->sendEmailToAdmins($mailable);
		
		return $result;
	}

	/**
	 * Send Notification mail about Write Review to Host and Guest
	 *
	 * @param  Integer $reservation_id
	 */
	public function writeReview($reservation_id)
	{
		$reservation = Reservation::with('user','host_user','hotel.hotel_address')->find($reservation_id);

		$host_user = $reservation->host_user;
		$user = $reservation->user;
		$hotel = $reservation->hotel;

		$mail_data['header_name'] = 'header_with_title';
		$mail_data['header_title'] = '';
		$mail_data['subject'] = Lang::get('messages.write_review_about',['replace_key_1' => $host_user->first_name],$user->user_language);
		
		$mail_data['user_data'] = $this->getUserData($user);
		$mail_data['host_data'] = $this->getUserData($host_user);
		$mail_data['end_date'] = $reservation->review_end_date;
		$mail_data['review_url'] = resolveRoute('edit_review',['reservation' => $reservation->id]);
		
		$mailable = new \App\Mail\WriteReviewGuest($mail_data);
		$this->sendEmail($mailable,$user->email,$user->user_language);
		
		$mail_data['header_title'] = '';
		$mail_data['subject'] = Lang::get('messages.write_review_about',['replace_key_1' => $user->first_name],$user->user_language);

		$mailable = new \App\Mail\WriteReviewHost($mail_data);
		$result = $this->sendEmail($mailable,$host_user->email,$host_user->user_language);

		return $result;
	}

	/**
	 * Send Notification mail about Read Or Write Review to Host and Guest
	 *
	 * @param  Integer $reservation_id
	 * @param  String $user_type guest | host
	 */
	public function readOrWriteReview($reservation_id,$user_type)
	{
		$reservation = Reservation::with('user','host_user','reviews')->find($reservation_id);

		$mail_data['header_name'] = 'header_with_title';
		$user_relation = ($user_type == 'guest') ? 'user' : 'host_user';
		$other_user_relation = ($user_type == 'guest') ? 'host_user' : 'user';
		$user = $reservation->$user_relation;
		$other_user = $reservation->$other_user_relation;
		$user_review = $reservation->reviews->where('user_from',$other_user->id)->count();
		$message = Message::where(['reservation_id'=> $reservation->id, 'list_type' => 'room'])->first();

        $mail_data['user_type'] = ucfirst($user_type);
		// $mail_data['inbox_url'] = resolveRoute('conversation',['id' => $message->id]);
		$mail_data['review_url'] = resolveRoute('edit_review',['id' => $reservation->id]);
		$mail_data['user_data'] = $this->getUserData($user);
        $mail_data['header_title'] = '';

		if($user_review == 0) {
			$mail_data['subject'] = Lang::get('messages.find_out_what_user_write',['replace_key_1' => $user->first_name],$user->user_language);

			$mailable = new \App\Mail\FindUserReview($mail_data);
			$result = $this->sendEmail($mailable,$other_user->email,$other_user->user_language);
			return $result;
        }

        $review = $reservation->reviews->where('user_from',$user->id)->first();

		$mail_data['subject'] = Lang::get('messages.here_what_user_wrote',['replace_key_1' => $user->first_name],$user->user_language);
		$mail_data['review'] = [
			'public_comment' => $review->public_comment,
			'private_comment' => $review->private_comment,
		];
				
		$mailable = new \App\Mail\ReadUserReview($mail_data);
		
		$result = $this->sendEmail($mailable,$other_user->email,$other_user->user_language);
		return $result;
	}

	/**
	 * Send Invite Notification to Guest
	 *
	 * @param  Integer $user_id
	 */
	public function inviteGuest($email)
	{
        $currency_symbol = session('currency_symbol');
		$user = User::findOrFail(Auth::id());
		$mail_data['user_data'] = $this->getUserData($user);

		$mail_data['subject'] = Lang::get('messages.new_invite',['site_name' => SITE_NAME],$user->user_language);

		$mail_data['new_referral_credit'] = $currency_symbol.round(referral_settings('new_referral_credit'));

		$mail_data['referral_link'] = resolveRoute('invite_referral',['username' => $user->first_name.$user->id]);

		$mail_data['view_profile'] = resolveRoute('view_profile',['id' => $user->id]);

		$mailable = new \App\Mail\InviteGuest($mail_data);

		$result = $this->sendEmail($mailable,$email,$user->user_language);

		return $result;
	}

	/**
	 * Send Invite Notification to Host
	 *
	 * @param  Integer $user_id
	 */
	public function earnings($user_id)
	{
        $currency_symbol = session('currency_symbol');
		$user = User::findOrFail($user_id);
		$mail_data['user_data'] = $this->getUserData($user);

	    $referral_users = ReferralUser::authUser()->get();

	    $available_credit = $referral_users->where('user_id',Auth::id())->sum('user_credited_amount') + $referral_users->where('referral_user_id',Auth::id())->sum('referral_credited_amount');
	    $mail_data['available_credit'] = $currency_symbol.''.numberFormat($available_credit);
		$mail_data['subject'] = Lang::get('messages.sent_referral_amount',[],$user->user_language);

		$mailable = new \App\Mail\Earning($mail_data);

		$result = $this->sendEmail($mailable,$user->email,$user->user_language);

		return $result;
	}

	/**
	 * Send Email when New Listings Created
	 *
	 * @param  Integer $listing_id
	 */
	public function adminResubmitListing($listing_id)
	{
		$hotel = Hotel::with('hotel_address')->find($listing_id);
		$user = $hotel->user;

		$mail_data['name'] = $user->first_name;
		$mail_data['listing_link'] = resolveRoute('room_details',['id' => $hotel->id]);
		$mail_data['listing_name'] = $hotel->name;
		$mail_data['resubmit_reason'] = $hotel->resubmit_reason;
		$mail_data['resubmit_by'] = Auth::guard('admin')->user()->username;
		$mail_data['date'] = $this->currentDateTime;
		$mail_data['subject'] = Lang::get('messages.listing_resubmit',[],$user->user_language);
		$this->sendSms($user,$mail_data['subject']);
		$mailable = new \App\Mail\AdminResubmitListing($mail_data);

		$result = $this->sendEmail($mailable,$user->email,$user->user_language);
		return $result;
	}

	public function generateReport($id)
	{
       $mail_data['result'] = $reservation = Reservation::with('hotel.hotel_address','user','host_user','room_reservations')->findOrFail($id);
       $mail_data['user'] = $user = $reservation->user;
       $mail_data['host_user'] = $reservation->host_user;
       $payouts = Payout::where('reservation_id',$id)->get();
       $mail_data['host_payout'] = $payouts->where('user_type','Host')->first();        
       
       $mail_data['pricing_details'] = $reservation->getPricingForm("Admin");
       $mail_data['subject'] = Lang::get('admin_messages.view_details');
       $mailable = new \App\Mail\GenerateReport($mail_data);

       $result = $this->sendEmailToAdmins($mailable);
       return $result;
	}
}