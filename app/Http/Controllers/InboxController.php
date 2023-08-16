<?php

/**
 * Inbox Controller
 *
 * @package     Hyra
 * @subpackage  Controllers
 * @category    InboxController
 * @author      Cron24 Technologies
 * @version     1.4
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageConversation;
use App\Models\SpecialOffer;
use App\Models\Reservation;
use Auth;
use Lang;
use DB;

class InboxController extends Controller
{
	/**
     * Constructor
     *
     */
	function __construct()
	{
		$this->paginate_limit = 5;
	}

	/**
	 * Get Formatted Message Conversation data
	 *
	 * @param  \App\Models\Message $messages
	 * @param  String $user_type
	 * @return Illuminate\Support\Collection $messages
	 */
	public function mapConversationData($conversations)
	{
		return $conversations->map(function($message) {
			$message_data = $message->only(['id','message_id','user_from','user_to','message','message_type','read','sent_at','header_notification_text']);
			$user_data = [
				'user_name' => $message->from_user->full_name,
				'verification_status' => $message->from_user->verification_status,
				'profile_picture_src' => $message->from_user->profile_picture_src,
				'user_link' => $message->from_user->link,
			];

			$special_offer_data = [];
			if($message->special_offer_id > 0) {
				$special_offer_data = [
					'special_offer_id' => $message->special_offer->id,
					'day_price' => $message->special_offer->day_price,
					'total_days' => $message->special_offer->total_days,
					'price' => $message->special_offer->price,
				];
			}
			return array_merge($message_data,$user_data,$special_offer_data);
		});
	}
	
	/**
	 * Get Formatted Message data
	 *
	 * @param  \App\Models\Message $messages
	 * @param  String $user_type
	 * @return Illuminate\Support\Collection $messages
	 */
	public function mapMessageData($messages,$user_type)
	{
		return $messages->map(function($message) use($user_type) {
			$message_data = $message->only(['id','list_id','list_type','reservation_id','user_id','host_id']);
			$message_data['star'] = $message[$user_type.'_star'];
			$message_data['archive'] = $message[$user_type.'_archive'];
			$message_data['read'] = $message[$user_type.'_read'];
			$message_data['message'] = ($user_type == 'guest') ? $message['host_message'] : $message['guest_message'];
			$message_data['unread_count'] = $message->conversations->where($user_type.'_read','0')->count();

			$reservation = $message->reservation();
			$location = $message->list()->list_address();

			$reservation_data = [
				'status' => $reservation->status,
				'currency_symbol' => $reservation->currency_symbol,
				'total' => $reservation->guestOrHostTotal(),
			];

			$user_relation = ($user_type == 'guest') ? 'host_user' : 'user';
			$user_data = [
				'user_link' => $message->$user_relation->link,
				'user_name' => $message->$user_relation->full_name,
				'since' => Lang::get('messages.member_since',['replace_key_1' => $message->$user_relation->since]),
				'verification_status' => $message->$user_relation->verification_status,
				'profile_picture_src' => $message->$user_relation->profile_picture_src,
			];

			if($reservation->status == 'Accepted') {
				$formatted_address = $location->full_address;
			}
			else {
				$formatted_address = $location->address_line_display;
			}

			$list_data = [
				'formatted_address' => $formatted_address,
				'list_type' => $message->list_type,
			];
			$other_data = [
				'target_link' => resolveRoute('conversation',['id' => $message->id]),
			];
			return array_merge($message_data,$reservation_data,$user_data,$list_data,$other_data);
		});
	}

	/**
     * Display Inbox page
     *
     * @return \Illuminate\Http\Response
     */
	public function index()
	{
		$data['message_text'] = [
			'star' => Lang::get('messages.star'),
			'unstar' => Lang::get('messages.unstar'),
			'archive' => Lang::get('messages.archive'),
			'unarchive' => Lang::get('messages.unarchive'),
		];

		return view('inbox.inbox',$data);
	}

	/**
     * Get the Messages based on given filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $message_data
     */
	public function messageList(Request $request)
	{
		$user_id = Auth::id();
		$filter	= $request->filter;
		$user_type = 'guest';
		$reserve_user_column = 'user_id';

		$result = Message::with('user','host_user','conversations')
			->where(function($query) use($reserve_user_column) {
				$query->where(function($query) use($reserve_user_column) {
					$query->where('list_type','hotel')->whereHas('hotel_reservation', function($query) use($reserve_user_column) {
						$query->where($reserve_user_column,Auth::id());
					});
				});
				/*ExperienceCommentStart*/
				// $query->orWhere(function($query) use($reserve_user_column) {
				// 	$query->where('list_type','experience')->whereHas('experience_reservation', function($query) use($reserve_user_column) {
				// 		$query->where($reserve_user_column,Auth::id());
				// 	});
				// });
				/*ExperienceCommentEnd*/
			})
			->orderByDesc('id');

		if($filter == 'starred') {
			$result = $result->where($user_type.'_star','1');
		}
		else if($filter == 'unread') {
			$result = $result->where($user_type.'_read','0');
		}
		else if($filter == 'reservations') {
			$result = $result->whereHas('hotel_reservation', function($query) {
				$query->whereIn('status',['Accepted','Completed']);
			});
		}
		/*ExperienceCommentStart*/
		else if($filter == 'experience_reservations') {
			$result = $result->whereHas('experience_reservation', function($query) {
				$query->whereIn('status',['Accepted','Completed']);
			});
		}
		/*ExperienceCommentEnd*/

		if($filter == 'archive') {
			$result = $result->where($user_type.'_archive','1');
		}
		else {
			$result = $result->where($user_type.'_archive','0');
		}

		$inbox_result = $result->paginate($this->paginate_limit);
		$message_data = $inbox_result->getCollection();
		$result_data = $this->mapMessageData($message_data,$user_type);
		return response()->json([
			'current_page'		=>  $inbox_result->currentPage(),
			'data'				=>	$result_data,
			'from'				=>  $inbox_result->firstItem(),
			'to'				=>  $inbox_result->lastItem(),
			'total'				=>  $inbox_result->total(),
			'per_page'			=>  $inbox_result->perPage(),
			'last_page'			=>  $inbox_result->lastPage(),
		]);
	}

	/**
     * update Message Status (Star, Archive)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function updateMessageStatus(Request $request)
	{
		if(!in_array($request->type,['star', 'archive'])) {
			return response()->json([
				'status' => false,
				'status_message' => Lang::get('messages.invalid_request'),
			]);
		}

		$type = $request->user_type.'_'.$request->type;

		Message::where('id', $request->message_id)->update([$type => $request->action]);
		
		return response()->json([
			'status' => true,
			'status_message' => Lang::get('messages.updated_successfully'),
		]);
	}

	/**
     * Display Conversation page for selected reservation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function inboxConversation(Request $request)
	{
		$message_id = $request->id;
		$user_id = Auth::id();
		$data['message'] = $message = Message::userBased()->with('conversations')->findOrFail($message_id);
		$conversations = $message->conversations;
		$data['user_type'] = $user_type = 'Guest';
		
		$data['list'] = $message->list();
		$data['list_type'] = $message->list_type;
		$data['reservation'] = $reservation = $message->reservation();
		$data['list_location'] = $data['list']->list_address();

		if($user_type == 'Host') {
			$data['user_details'] = $message->user;
			$this->updateExpiredStatus($data['reservation']);
		}
		if($user_type == 'Guest') {
			$data['user_details'] = $message->host_user;
		}
		$data['user_details']->append('profile_picture_src');
		$data['pricing_data'] = $data['reservation']->getPricingForm($user_type);
		$data['auth_user'] = Auth::user()->append('profile_picture_src');
		$data['messages'] = $this->mapConversationData($conversations);

		if($message->list_type == 'hotel') {
			$pricing_form = [];
			
			$form_data = array('key' => $reservation->currency_symbol.$reservation->day_price.' x '.$reservation->total_days.' '.Lang::choice('messages.listing.night',$reservation->total_days), 'value' => $reservation->currency_symbol.$reservation->total_days_price);
			$pricing_form[] = formatPricingForm($form_data);

			$form_data = array('key' => Lang::get('messages.listing.host_fee'), 'value' => '-'.$reservation->currency_symbol.$reservation->host_fee);
			$pricing_form[] = formatPricingForm($form_data);

			$form_data = array('key' => Lang::get('messages.you_will_earn'), 'value' => $reservation->currency_symbol.$reservation->calcHostPayoutAmount(),'class' => 'fw-bold');
			$pricing_form[] = formatPricingForm($form_data);

			$data['offer_details'] = [
				'id' => $reservation->id,
				'listing' => $reservation->room_id,
				'checkin' => $reservation->checkin,
				'checkout' => $reservation->checkout,
				'guests' => $reservation->guests,
				'currency_code' => $reservation->currency_code,
				'price' => round($reservation->sub_total),
				'show_price_details' => true,
				'pricing_form' => $pricing_form,
			];			
		}

		Message::where('id',$message_id)->update([strtolower($user_type).'_read' =>'1']);
		MessageConversation::where('message_id',$message_id)->where('user_to',Auth::id())->update(['read' =>'1']);
		return view('inbox.conversation',$data);
	}

	/**
     * Send message to other users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $message_data
     */
	public function sendMessage(Request $request)
	{
		$user_column = ($request->user_type == 'Host') ? 'host_id' : 'user_id';

		$message = Message::where($user_column,Auth::id())->find($request->message_id);
		
		if($message == '' || $request->message == '') {
			return response()->json([
				'status' => false,
				'status_message' => Lang::get('messages.invalid_request'),
			]);
		}

		$user_to = ($request->user_type == 'Host') ? $message->user_id : $message->host_id;

		$user_message = removeEmailNumber($request->message);
		$message_type = getMessageType('booking_discuss');

		$message_column = strtolower($request->user_type).'_message';
		$message->$message_column = $user_message;
		$read_column = $request->user_type == 'Host' ? 'guest_read' : 'host_read';
		$message->$read_column = 0;
		$message->save();
		
		$message_conversation = new MessageConversation;
		$message_conversation->message_id = $message->id;
		$message_conversation->user_from = Auth::id();
		$message_conversation->user_to = $user_to;
		$message_conversation->message = $user_message;
		$message_conversation->message_type = $message_type;
		$message_conversation->save();

		resolveAndSendNotification("userConversation",$message_conversation->id);
		
		$message_data = $this->mapConversationData(collect([$message_conversation]));

		if(checkEnabled('Firebase')) {
            $data = [
                'title' => Lang::get('messages.new_message_received_from_user',['replace_key_1' => global_settings('site_name'),'replace_key_2' => $message_conversation->from_user->first_name]),
                'message' => $message_data->first(),
                'inbox_count' => $message_conversation->to_user->inbox_count,
            ];
            
            $firbase_service = resolve("App\Services\FirebaseService");
        	$firbase_service->insertReference("users/".$user_to."/messages/", $data);
        	
        	$notify_data = [
        		'title' => $data['title'],
        		'message' => truncateString($message_conversation->message,30),
        		'data' => [
        			'message_id' => $message->id,
                ],
        	];
            sendNotificationToUser($message_conversation->to_user,$notify_data);
        }
		
		return response()->json([
			'status' => true,
			'data' => $message_data->first(),
		]);
	}

	/**
     * Send Special Offer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $message_data
     */
	public function sendSpecialOffer(Request $request)
	{
		if ($request->type == 'remove_special_offer') {
			$special_offer = SpecialOffer::where('room_id',$request->room_id)->where('reservation_id',$request->id)->where('status','!=','removed')->first();
			if ($special_offer != '') {
				$special_offer->status = 'removed';
				$special_offer->save();
				$reservation = Reservation::findorFail($request->id);
				if ($reservation->status == 'Pre-Approved') {
					$reservation->status = 'Declined';
					$reservation->save();
				}

				$message = Message::where('reservation_id',$request->id)->where('id',$request->message_id)->first();
				$message->special_offer_id = null;
				$message->save();

				$message_conversation = MessageConversation::where('special_offer_id',$special_offer->id)->where('message_id',$request->message_id)->where('message_type','14')->first();
				if ($message_conversation != '') {
					$message_conversation->delete();
				}

			}
    		flashMessage('success',Lang::get('messages.success'),Lang::get('admin_messages.successfully_deleted'));
			return response()->json([
				'status' => 'reload',
			]);
		}

		$message = Message::where('host_id',Auth::id())->find($request->message_id);
		
		if($message == '') {
			return response()->json([
				'error' => true,
				'error_message' => Lang::get('messages.invalid_request'),
			]);
		}

        $min_price = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency'),$request->currency_code));
		$rules = array(
		    'room_id' => 'required',
		    'checkin' => 'required',
		    'checkout' => 'required',
		    'guests' => 'required',
		    'currency_code' => 'required',
		    'price' => 'required|numeric|min:'.$min_price,
		);

		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
		    return response()->json([
				'error' => true,
				'error_message' => $validator->errors()->first(),
			]);
		}

		$reserve_service = resolve('App\Services\ReserveService');
        $price_data = $request->only(['checkin','checkout','guests']);
        $optional_data = $request->only(['price']);

        $price_details = $reserve_service->priceCalculation($request->room_id, $price_data, $optional_data);
        if(!$price_details['is_available']) {
        	return response()->json([
        		'status' => false,
        		'status_text' => Lang::get('messages.failed'),
        		'status_message' => $price_details['status_message'],
        	]);
        }

		$type = 'special_offer';
		$message_type = getMessageType('special_offer');
        $reservation = $message->reservation();

		$special_offer = new SpecialOffer;
		$special_offer->type = $type;
		$special_offer->reservation_id = $message->reservation_id;
		$special_offer->user_id = $message->user_id;
		$special_offer->room_id = $message->list_id;
		$special_offer->checkin = $request->checkin;
		$special_offer->checkout = $request->checkout;
		$special_offer->guests = $request->guests;
		$special_offer->currency_code = $request->currency_code;
		$special_offer->total_days = $price_details['total_days'];
		$special_offer->day_price = $price_details['day_price'];
		$special_offer->price = $request->price;
		$special_offer->save();

        if($reservation->status == 'Pending') {
            $reservation->special_offer_id = $special_offer->id;
        	$reservation->status = 'Pre-Accepted';
        	$reservation->save();
        }
        elseif($reservation->status == 'Inquiry') {
        	$type = 'pre_approval';
			$message_type = getMessageType('request_pre_approved');
            $reservation->special_offer_id = $special_offer->id;
        	$reservation->status = 'Pre-Approved';
        	$reservation->save();
        }

		$user_message = removeEmailNumber($request->message);		
		$message->guest_message = $user_message;
		$message->guest_read = 0;
		$message->special_offer_id = $special_offer->id;
		$message->save();
		
		$message_conversation = new MessageConversation;
		$message_conversation->message_id = $message->id;
		$message_conversation->user_from = Auth::id();
		$message_conversation->user_to = $message->user_id;
		$message_conversation->message = $user_message;
		$message_conversation->message_type = $message_type;
		$message_conversation->special_offer_id = $special_offer->id;
		$message_conversation->save();
	
		$message_data = $this->mapConversationData(collect([$message_conversation]));

		if(checkEnabled('Firebase')) {
            $data = [
                'title' => Lang::get('messages.new_message_received_from_user',['replace_key_1' => global_settings('site_name'),'replace_key_2' => $message_conversation->from_user->first_name]),
                'message' => $message_data->first(),
                'inbox_count' => $message_conversation->to_user->inbox_count,
            ];
            
            $firbase_service = resolve("App\Services\FirebaseService");
        	$firbase_service->insertReference("users/".$user_to."/messages/", $data);
        	
        	$notify_data = [
        		'title' => $data['title'],
        		'message' => truncateString($message_conversation->message,30),
        		'data' => [
        			'message_id' => $message->id,
                ],
        	];
            sendNotificationToUser($message_conversation->to_user,$notify_data);
        }
        resolveAndSendNotification("specialOfferSent",$reservation->id,$special_offer->id);
		
		return response()->json([
			'status' => 'reload',
			'data' => $message_data->first(),
		]);
	}

	/**
     * Contact Host
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $message_data
     */
	public function contactHost(Request $request)
	{
		$rules = array(
		    'room_id' => 'required',
		    'checkin' => 'required',
		    'checkout' => 'required',
		    'guests' => 'required',
		    'user_question' => 'required',
		);

		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
		    return response()->json([
				'status' => false,
				'status_text' => Lang::get('messages.failed'),
				'status_message' => $validator->errors()->first(),
			]);
		}

		$reserve_service = resolve('App\Services\ReserveService');
        $price_data = $request->only(['checkin','checkout','guests']);

        $price_details = $reserve_service->priceCalculation($request->room_id, $price_data);
        if(!$price_details['is_available']) {
        	return response()->json([
        		'status' => false,
        		'status_text' => Lang::get('messages.failed'),
        		'status_message' => $price_details['status_message'],
        	]);
        }

        $room_details = \App\Models\Room::find($request->room_id);

        $reservation = new Reservation;
        $reservation->type = 'inquiry';
        $reservation->room_id = $room_details->id;
        $reservation->host_id = $room_details->user_id;
        $reservation->user_id = Auth::id();
        $reservation->checkin = $price_data["checkin"];
        $reservation->checkin_at = $price_details["checkin_time"];
        $reservation->checkout = $price_data["checkout"];
        $reservation->checkout_at = $price_details["checkout_time"];
        $reservation->guests = $price_data["guests"];
        $reservation->currency_code = $price_details["currency_code"];
        $reservation->day_price = $price_details["day_price"];
        $reservation->total_days = $price_details["total_days"];
        $reservation->total_days_price = $price_details["total_price"];
        $reservation->cleaning_fee = $price_details["cleaning_fee"];
        $reservation->additional_guests = $price_details["additional_guests"];
        $reservation->additional_guest_fee = $price_details["additional_guest_fee"];
        $reservation->sub_total = $price_details["sub_total"];
        $reservation->early_bird_discount = $price_details["early_bird_discount"];
        $reservation->early_bird_discount_price = $price_details["early_bird_discount_price"];
        $reservation->length_of_stay_discount = $price_details["length_of_stay_discount"];
        $reservation->length_of_stay_discount_price = $price_details["length_of_stay_discount_price"];
        $reservation->special_offer_id = $price_details['special_offer_id'] ?? NULL;
        $reservation->coupon_type = $price_details['coupon_type'] ?? NULL;
        $reservation->coupon_code = $price_details["coupon_code"] ?? '';
        $reservation->coupon_price = $price_details["coupon_price"];
        $reservation->service_fee = $price_details["service_fee"];
        $reservation->host_fee = $price_details["host_fee"];
        $reservation->total = $price_details["total"];
        $reservation->security_fee = $price_details["security_fee"];
        $reservation->payment_currency = global_settings('default_currency');
        $reservation->cancellation_policy = $price_details["cancellation_policy"];
        $reservation->status = "Inquiry";
        $reservation->save();

		$user_message = removeEmailNumber($request->user_question);

		$message = Message::firstOrNew(['list_type' => 'room','reservation_id' => $reservation->id]);
        $message->list_type = 'room';
        $message->list_id = $reservation->room_id;
        $message->reservation_id = $reservation->id;
        $message->user_id = $reservation->user_id;
        $message->host_id = $reservation->host_id;
        $message->guest_message = $user_message;
        $message->save();
		
		$message_conversation = new MessageConversation;
		$message_conversation->message_id = $message->id;
		$message_conversation->user_from = Auth::id();
		$message_conversation->user_to = $message->user_id;
		$message_conversation->message = $user_message;
		$message_conversation->message_type = getMessageType('contact_request_sent');
		$message_conversation->save();
	
		$message_data = $this->mapConversationData(collect([$message_conversation]));

		if(checkEnabled('Firebase')) {
            $data = [
                'title' => Lang::get('messages.new_message_received_from_user',['replace_key_1' => global_settings('site_name'),'replace_key_2' => $message_conversation->from_user->first_name]),
                'message' => $message_data->first(),
                'inbox_count' => $message_conversation->to_user->inbox_count,
            ];
            
            $firbase_service = resolve("App\Services\FirebaseService");
        	$firbase_service->insertReference("users/".$user_to."/messages/", $data);
        	
        	$notify_data = [
        		'title' => $data['title'],
        		'message' => truncateString($message_conversation->message,30),
        		'data' => [
        			'message_id' => $message->id,
                ],
        	];
            sendNotificationToUser($message_conversation->to_user,$notify_data);
        }

        resolveAndSendNotification("newInquiryFromGuest",$reservation->id);

        flashMessage('success',Lang::get('messages.success'),Lang::get('messages.listing.contact_request_has_been_sent_to_host',['replace_key_1' => $reservation->host_user->first_name]));
		
		return response()->json([
			'status' => 'reload',
			'data' => $message_data->first(),
		]);
	}

	/**
     * Update Read Status of the message
     *
     * @param  \Illuminate\Http\Request $request
     * @return Boolean
     */
	public function updateReadStatus(Request $request)
    {
    	$message = Message::authUser()->findOrFail($request->message_id);
    	
    	$user_type = getUserType($message->host_id);
    	$read_col = strtolower($user_type).'_read';
    	$message->$read_col = '1';
    	$message->save();

    	MessageConversation::where('message_id',$message->id)->where('user_to',Auth::id())->update(['read' =>'1']);

    	return response()->json([
			'status' => true,
		]);
    }

	/**
     * Check Pending Reservation is Expired or Not
     *
     * @param  \App\Models\Reservation $reservation
     * @return json $reservation
     */
	protected function updateExpiredStatus($reservation)
    {
    	if($reservation->status != 'Pending') {
    		return false;
    	}

    	$expired_at = $reservation->created_at->addDay();
        $diff_mins = now()->diffInMinutes($expired_at,0);

        if($diff_mins > 0) {
        	return false;
        }

		if($reservation->penalty_enabled) {
			$host_cancel_count = Reservation::cancelCount($reservation->host_id)->count();

			if($host_cancel_count >= fees('host_cancel_limit')) {
				$reservation->host_penalty = currencyConvert(fees("cancel_before_days"),global_settings('default_currency'),$reservation->getRawOriginal("currency_code"));
				updateUserPenalty($reservation->host_id,$reservation->currency_code,$reservation->host_penalty);
			}
		}

        $reservation->status = 'Expired';
        $reservation->expired_on = "Host";
        $reservation->save();

        $message = Message::where('reservation_id',$reservation->id)->first();

        $message_conversation = new MessageConversation;
        $message_conversation->message_id = $message->id;
        $message_conversation->user_from = $reservation->host_id;
        $message_conversation->user_to = $reservation->user_id;
        $message_conversation->message = '';
        $message_conversation->message_type = getMessageType('booking_expired');
        $message_conversation->save();

        resolveAndSendNotification("requestExpired",$reservation->id);
    }
    
    /**
     * Share Itinerary
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shareItinerary(Request $request)
    {
        if ($request->email == '' || $request->email == null) {
            return response()->json([
                'status' => false,
                'error_message' => true,
            ]);
        }

        if ($request->list_type == 'room') {
        	$result = resolveAndSendNotification("itineraryShared",$request->all());
        } elseif ($request->list_type == 'experience') {
        	$result = resolveAndSendExperienceNotification("itineraryShared",$request->all());
        }
        return response()->json($result);
    }
}