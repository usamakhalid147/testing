<?php

/**
 * Hotel Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    HotelController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\HotelPhoto;
use App\Models\HotelRoomCalendar;
use App\Models\WishlistList;
use App\Models\Reservation;
use App\Models\HostCouponCode;
use App\Models\ImportedCalendar;
use App\Models\Amenity;
use App\Models\Currency;
use App\Traits\ManageHotel;
use Str;
use DB;
use Lang;
use Auth;
use Validator;
use Carbon\Carbon;

class HotelController extends Controller
{
    use ManageHotel;

    /**
     * Display Listing details page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hotelDetails(Request $request)
    {
        $hotel = Hotel::loadRelations()->verified()->findOrFail($request->id);
        if(empty($hotel)) {
            abort(404);
        }
        $notice_days = $hotel->notice_days ?? 0;
        $data = array_merge($this->commonManagementData(),$this->hotelManagementData($hotel));

        if($request->checkin == '') {
            $request['checkin'] = now()->addDays($notice_days)->format('Y-m-d');
        }
        if($request->checkout == '') {
            $request['checkout'] = now()->addDays($notice_days)->addDay()->format('Y-m-d');
        }

        [$checkin, $checkout] = checkInValidDate($request->checkin,$request->checkout);

        $today = Carbon::today();
        $data['checkin'] = $checkin->format('Y-m-d');
        $data['checkout'] = $checkout->format('Y-m-d');

        $data['adults'] = $request->adults ?? 1;
        $data['children'] = $request->children ?? 0;
        $data['rooms'] = $request->rooms ?? 1;
        $data['max_guests'] =view()->shared('max_guests');

        $hotel_address = $hotel->hotel_address;
        $data['similar_hotels'] = $this->getSimilarHotels($hotel_address->latitude, $hotel_address->longitude, $hotel->id);
        
        $data['cancellation_policies'] = [];
        foreach($hotel->hotel_rooms->where('status','Listed') as $room){
            if(count($room->cancellation_policies) > 0 ){
                 $data['cancellation_policies'][] = [
                    'room_name' => $room->name,
                    'policies' =>  $room->cancellation_policies,
                 ];
            }
        }

        $data['coupon'] = HostCouponCode::where('user_id',$hotel->user_id)->first();
        $data['reviews'] = Review::where('hotel_id',$hotel->id)->get();
        
        $wishlist_count = 0;
        if(Auth::check()) {
            $wishlist_count = WishlistList::checkUserAndListing($hotel->id,'hotel')->count();
        }
        $data['is_saved'] = $wishlist_count > 0;

        $share_url = resolveRoute('hotel_details',['id' => $hotel->id,'unique_share_id' => Auth::id()]);
        $subject = Lang::get('messages.checkout_this_hotel_on_site',['replace_key_1' => global_settings('site_name')]);
        $share_text = Lang::get('messages.checkout_this_hotel').': '.str_replace('&',' ',$hotel->name).' : '.$share_url;
        $data['share_data'] = [
            ['id' => '1', 'name' => 'email', 'header_title' => Lang::get('messages.email'), 'title' => $subject, 'icon' => 'icon icon-email', 'link' => 'mailto:?subject='.$subject.'&body='.$share_text],
            ['id' => '2', 'name' => 'facebook', 'header_title' => Lang::get('messages.facebook'), 'title' => $subject, 'icon' => 'icon icon-facebook', 'link' => "https://www.facebook.com/sharer/sharer.php?u=".$share_url],
            ['id' => '3', 'name' => 'twitter', 'header_title' => Lang::get('messages.twitter'), 'title' => $subject, 'icon' => 'icon icon-twitter', 'link' => "http://twitter.com/intent/tweet?text=".$share_text],
            ['id' => '4', 'name' => 'pinterest', 'header_title' => Lang::get('messages.pinterest'), 'title' => $subject, 'icon' => 'icon icon-pinterest', 'link' => "http://pinterest.com/pin/create/button/?url=".$share_url."&media=".$hotel->image_src."&description=".$share_text],
            ['id' => '5', 'name' => 'whatsapp', 'header_title' => Lang::get('messages.whatsapp'), 'title' => $subject, 'icon' => 'icon icon-whatsapp', 'link' => "https://api.whatsapp.com/send?text=".$share_text],
        ];

        $data['title']  = $hotel->name.' in '.$hotel_address->city.' - '.global_settings('site_name');
        
        return view('hotels.hotel_details',$data);
    }

    /**
     * Check Available Rooms
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAvailability(Request $request) 
    {           
        $booking_days = getDays($request->checkin, $request->checkout);
        array_pop($booking_days);
        $startDate = current($booking_days);
        $endDate = end($booking_days);
        $total_nights = count($booking_days);
        $adults = $request->adults > 0 ? $request->adults : 1;
        $children = $request->children > -1 ? $request->children : 0;
        $total_rooms = $request->rooms > 0 ? $request->rooms : 1;
        $selected_plans = explode(',',$request->selected_plans);
        $selected_beds = explode(',',$request->selected_beds);
        $error_message = '';
        $error_type = '';
        $currency_rate = resolve('Currency')->where('code',session('currency'))->first()->rate;

        $hotel = Hotel::loadRelations()->findOrFail($request->hotel_id);

        // NOTICE DAYS, MIN LOS, MAX LOS
        $start_date = Carbon::createFromFormat('Y-m-d', $startDate);
        $today = Carbon::today();
        $diff_in_days = $start_date->diffInDays($today);
        if(!empty($hotel->notice_days) && $hotel->notice_days > $diff_in_days) {
            $error_message = Lang::get('messages.notice_day_message',['count' => $hotel->notice_days]);
            $error_type = 'notice_days';
        }
        else if(!empty($hotel->min_los) && $hotel->min_los > $total_nights) {
            $error_message = Lang::get('messages.min_stay_message',['count' => $hotel->min_los]).' '.($hotel->min_los > 1 ? Lang::get('messages.days') : Lang::get('messages.day'));
            $error_type = 'min_los';
        }
        else if(!empty($hotel->max_los) && $hotel->max_los < $total_nights) {
            $error_message = Lang::get('messages.max_stay_message',['count' => $hotel->max_los]).' '.($hotel->min_los > 1 ? Lang::get('messages.days') : Lang::get('messages.day'));
            $error_type = 'max_los';
        }
        if(!empty($error_message)) {
            return response()->json([
                'all_rooms' => [],
                'selected_rooms' => [],
                'total_nights' => $total_nights,
                'fees' => [],
                'error_type' => $error_type,
                'error_message' => $error_message
            ]);
        }

        $availableRooms = collect();
        $hotel_rooms = $hotel->hotel_rooms->where('status','Listed')->map(function($room,$key) use($total_nights,$startDate,$endDate,$hotel, $currency_rate,&$availableRooms,$selected_plans,$selected_beds) {

            $room_calendar_available = DB::table('hotel_room_calendars')
                        ->whereBetween('reserve_date',[$startDate,$endDate])
                        ->where('hotel_id',$room->hotel_id)->where('room_id',$room->id)
                        ->whereStatus('not_available')
                        ->count();

            // Restrict Not Available Rooms                        
            if(!empty($room_calendar_available)) {
                return true;
            }

            $room_calendar_number = DB::table('hotel_room_calendars')
                                ->selectRaw('MIN(number) as calendar_number')
                                ->whereBetween('reserve_date',[$startDate,$endDate])
                                ->where('hotel_id',$room->hotel_id)->where('room_id',$room->id)
                                ->whereNotNull('number')
                                ->whereStatus('available')
                                ->first();

            $room_calendar_price =  DB::table('hotel_room_calendars')
                                ->join('currencies', 'currencies.code', '=', 'hotel_room_calendars.currency_code')
                                ->selectRaw("(SELECT ROUND((price / currencies.rate) * ".$currency_rate.")) as total_price")
                                ->selectRaw("(SELECT SUM(total_price)) as calendar_price")
                                ->selectRaw('COUNT(*) as calendar_price_data')
                                ->whereBetween('reserve_date',[$startDate,$endDate])
                                ->where('hotel_id',$room->hotel_id)->where('room_id',$room->id)
                                ->whereNotNull('price')
                                ->where('hotel_room_calendars.status','available')
                                ->first();

            $hotel_room_price = $room->hotel_room_price;
            $hotel_room_price_rules = $room->hotel_room_price_rules;
            $meal_plans = $hotel_room_price_rules->where('type','meal');
            // dd($selected_beds);
            $meal_plans = $meal_plans->map(function($plan) use($selected_plans) {
                return [
                    'id' => $plan->id,
                    'hotel_id' => $plan->hotel_id,
                    'room_id' => $plan->room_id,
                    'type_id' => $plan->type_id,
                    'type' => $plan->type,
                    'name' => $plan->name,
                    'price' => round($plan->price),
                    'is_selected' => in_array($plan->id,$selected_plans),
                ];
            });
            $meal_plan_price = $meal_plans->where('is_selected',true)->sum('price') ?? 0;
            $bed_types = $hotel_room_price_rules->where('type','bed');
            $bed_types = $bed_types->map(function($bed) use($selected_beds) {
                return [
                    'id' => $bed->id,
                    'hotel_id' => $bed->hotel_id,
                    'room_id' => $bed->room_id,
                    'type_id' => $bed->type_id,
                    'type' => $bed->type,
                    'name' => $bed->name,
                    'price' => round($bed->price),
                    'size' => $bed->size,
                    'guest_type' => $bed->guest_type,
                    'is_selected' => in_array($bed->id,$selected_beds),
                ];
            });
            $meal_plan_price = $meal_plans->where('is_selected',true)->sum('price') ?? 0;
            $bed_price = $bed_types->where('is_selected',true)->sum('price') ?? 0;

            // Get Available Number
            $tmp_number = $room->number - $room_calendar_number->calendar_number;
            if($tmp_number < 1) {
                return false;
            }

            // Get Price
            $room_data = $total_nights - ($room_calendar_price->calendar_price_data ?? 0);
            $room_price = $hotel_room_price->price * $room_data;
            $price = $room_price + ($room_calendar_price->calendar_price ?? 0);
            $adult_price = numberFormat($total_nights * $hotel_room_price->adult_price);
            $child_price = numberFormat($total_nights * $hotel_room_price->children_price);

            $currency_symbol = $hotel_room_price->currency_symbol;
            $applied_promotions = $room->getRoomPromotions($startDate, $total_nights);

            $original_price = $price;
            if(!empty($applied_promotions)) {
                foreach($applied_promotions as $key => $promo) {
                    if($promo['value_type'] == 'percentage') {
                        $value = $price * ($promo['value'] / 100);
                    }
                    else {
                        $value = $promo['value'];
                    }
                    $price -= $value;
                    $applied_promotions[$key]['amount'] = numberFormat($value);
                }
            }

            $amenity_types = \App\Models\AmenityType::activeOnly()->whereHas('amenities',function($query) use($room) {
                    return $query->where('list_type','room')->whereIn('id', explode(',',$room->amenities));
                })
                ->with(['amenities' => function($query) use($room) {
                    return $query->where('list_type','room')->whereIn('id', explode(',',$room->amenities));
                }])->get();

            $amenity_types = $amenity_types->map(function($amenity_type) {
                $amenities = $amenity_type->amenities->map(function($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'description' => $amenity->description,
                        'image_src' => $amenity->image_src,
                    ];
                });
                return [
                    'id' => $amenity_type->id,
                    'name' => $amenity_type->name,
                    'amenities' => $amenities,
                ];
            });

            $availableRooms[] = [
                'id'            => $room->id,
                'hotel_id'      => $room->hotel_id,
                'name'          => $room->name,
                'description'   => $room->description,
                'room_type'     => $room->room_type_name,
                'tmp_number'    => $tmp_number,
                'price'         => $price,
                'original_price'=> $original_price,
                'room_size'     => $room->room_size,
                'number'        => $room->number,
                'adults'        => $room->adults,
                'max_adults'    => $room->max_adults,
                'children'      => $room->children,
                'max_children'  => $room->max_children,
                'room_size_text'=> $room->room_size.' '.$room->room_size_type,
                'bed_text'      => $room->beds.' '.$room->bed_type_name,
                'adult_text'=> $room->adults == $room->max_adults ? $room->adults : $room->adults.'-'.$room->max_adults,
                'children_text'=> $room->children == $room->max_children ? $room->children : $room->children.'-'.$room->max_children,
                'selected_count'=> 0,
                'total_price'   => 0,
                'total_adults'  => 0,
                'total_children'=> 0,
                'is_selected'   => false,
                'total_nights'  => $total_nights,
                'price_text'    => $currency_symbol.$price,
                'adult_price'   => $adult_price,
                'adult_price_text' => $currency_symbol.' '.number_format($adult_price),
                'child_price'   => $child_price,
                'child_price_text' => $currency_symbol.' '.number_format($child_price),
                'add_rooms'     => [],
                'number_text'   => $tmp_number > 1 ? $tmp_number.' '.Lang::get('messages.rooms') : $tmp_number.' '.Lang::get('messages.room'),
                'amenity_types'     => $amenity_types,
                // 'amenities' => [],
                'total_nights_text'     => $total_nights > 1 ? Lang::get('messages.per_nights',['key' => $total_nights]) : Lang::get('messages.per_night',['key' => $total_nights]),
                'currency_symbol'    => session('currency_symbol'),
                'hotel_room_photos'  => $room->hotel_room_photos,
                // 'hotel_room_photos'  => [],
                'meal_plans'         => $meal_plans ?? [],
                'meal_plan_price'    => $meal_plan_price ?? 0,
                'bed_price'          => $bed_price ?? 0,
                'bed_types'          => $bed_types->values() ?? [],
                'selected_beds'      => [],
                'selected_plans'     => [],
                'applied_promotions' => $applied_promotions,
            ];
        });
    
        $adult_numbers = [];
        $children_numbers = [];
        $selectedRooms = collect();
        if($availableRooms->count() > 0) {
            $comboRooms = $this->getComboRooms($availableRooms);
            $recommendInfo = $this->getRecommendedRooms($comboRooms,$adults,$children,$total_rooms);
            $availableRooms = $availableRooms->map(function($r) use($recommendInfo,&$selectedRooms,$comboRooms){
                $r['combo_rooms'] = $comboRooms->where('id',$r['id']);
                $r_rooms = $recommendInfo['rooms']->where('id',$r['id']);                
                if($r_rooms->count() > 0) {
                    $temp = $r;
                    $temp['selected_count'] = $r_rooms->count();
                    $temp['total_price'] = $r_rooms->sum('price') + $r['bed_price'] + $r['meal_plan_price'];
                    $temp['meal_plan_price'] = $r['meal_plan_price'];
                    $temp['bed_price'] = $r['bed_price'];
                    $adult_numbers = 0;
                    $children_numbers = 0;
                    foreach($r_rooms as $key => $r_room) {
                        $adult_numbers += $recommendInfo['adult_numbers'][$key];
                        $children_numbers += $recommendInfo['children_numbers'][$key];
                        $temp['add_rooms'][] = [
                            'adults' => $recommendInfo['adult_numbers'][$key],
                            'children' => $recommendInfo['children_numbers'][$key],
                            'price' => $r_room['price'],
                        ];
                    }
                    $temp['total_adults'] = $adult_numbers;
                    $temp['total_children'] = $children_numbers;
                    $temp['meal_plans'] = $r['meal_plans'] ?? [];
                    $temp['bed_types'] = $r['bed_types']->toArray() ?? [];
                    $selectedRooms[] = $temp;
                    $r['is_selected'] = true;
                }
                return $r;
            });
        }
        else {
            $error_message = Lang::get('messages.soldout_message');
            $error_type = 'soldout';
        }

        $fees[] = [
            'min_service_fee' => currencyConvert(fees("min_service_fee"),global_settings('default_currency')),
            'fee_type' => fees("service_fee_type"),
            'value' => fees("service_fee"),
            'fee' => currencyConvert(fees("service_fee"),global_settings('default_currency')),
            'name' => Lang::get('messages.service_fee'),
            'service_charge_type' => $hotel->service_charge_type,
            'service_charge' => $hotel->service_charge,
            'property_tax_type' => $hotel->property_tax_type,
            'property_tax' => $hotel->property_tax,
        ];

        return response()->json([
            'all_rooms' => $availableRooms,
            'selected_rooms' => $selectedRooms,
            'total_nights' => $total_nights,
            'fees' => $fees,
            'error_type' => $error_type,
            'error_message' => $error_message
        ]);
    }

    /**
     * Generate Combo Rooms
     * 
     */
    protected function getComboRooms($rooms) 
    {
        $comboRooms = collect();
        $rooms->map(function($room) use(&$comboRooms){
            $base_price = $room['price'];
            $adult_price = $room['adult_price'];
            $child_price = $room['child_price'];
            $adults = $room['adults'];
            $max_adults = $room['max_adults'];
            $children = $room['children'];
            $max_children = $room['max_children'];
            for($i=1;$i<=$max_adults;$i++) {
                for($j=0;$j<=$max_children;$j++) {
                    if($adults >= $i && $children >= $j) {
                        $tmp_price = $base_price;
                    }
                    else {
                        $extra_adult = ($i - $adults) > 0 ? ($i - $adults) : 0;
                        $extra_child = ($j - $children) > 0 ? ($j - $children) : 0;
                        $tmp_price = $base_price + ($extra_adult * $adult_price) + ($extra_child * $child_price);
                    }
                    $comboRooms[] = [
                        'id' => $room['id'],
                        'hotel_id' => $room['hotel_id'],
                        'adults' => $i,
                        'children' => $j,
                        'max_adults' => $room['max_adults'],
                        'max_children' => $room['max_children'],
                        'tmp_number' => $room['tmp_number'],
                        'price' => $tmp_price,
                    ];
                }
            }
        });
        return $comboRooms;
    }

    /**
     * Get Recommonded Rooms for Hotel
     * 
     * @param array $rooms
     */ 
    protected function getRecommendedRooms($comboRooms,$adults,$children,$total_rooms)
    {
        $min_adults = $comboRooms->min('adults');
        $max_adults = $comboRooms->max('max_adults');

        $min_children = $comboRooms->min('children');
        $max_children = $comboRooms->max('max_children');

        $recommended_rooms = collect();
        $tmp_room = [];
        if($max_adults >= $adults && $max_children >= $children && $total_rooms == 1) {
            $exact_adult_numbers = [$adults];
            $exact_children_numbers = [$children];
            $recommended_rooms[] = $comboRooms->where('adults','>=',$adults)->where('children','>=',$children)->where('tmp_number','>',0)->sortBy('price')->first();
        }
        else {

            $adult_room = 0;
            $children_room = 0;
            try {
                $adult_room = ceil($adults/$max_adults);
                $children_room = ceil($children/$max_children);
            }
            catch(\DivisionByZeroError $e) {
                // Exeception Catched
            }

            $number = $adult_room > $children_room ? $adult_room : $children_room;
            if($total_rooms > $number) {
                $number = $total_rooms;
            }

            // Recommended
            $adult_numbers = $this->getRoomAdultNumbers($min_adults,$max_adults,$adults,$number);
            $children_numbers = $this->getRoomChildrenNumbers($number,$children);

            // Exact Recommended
            $exact_adult_numbers = $this->getRoomExactAdultNumbers($min_adults,$max_adults,$adults,$number);
            $exact_children_numbers = $this->getRoomChildrenNumbers($number,$children);

            for($i=0;$i<$number;$i++) {
                $tmp_room = $comboRooms->where('adults','>=',$adult_numbers[$i])->where('children','>=',$children_numbers[$i])->where('tmp_number','>',0)->sortBy('price')->first() ?? [];
                if(!empty($tmp_room)) {
                    $recommended_rooms[] = $tmp_room;
                    $comboRooms = $comboRooms->map(function($item) use($tmp_room) {
                        if($item['id'] == $tmp_room['id']) {
                            $item['tmp_number'] -= 1;
                        };
                        return $item;
                    });
                }
            }
        }

        return [
            'rooms' => $recommended_rooms,
            'adult_numbers' => $exact_adult_numbers,
            'children_numbers' => $exact_children_numbers,
        ];
    }

    /**
     * Get Room Guest Number
     * 
     */
    protected function getRoomAdultNumbers($min_occupancy, $max_occupancy, $adults,$number)
    {
        $adult_number = [];
        $combo = [];
        $average_number = (int) (($min_occupancy + $max_occupancy) / 2);
        for ($i = 0; $i < $number; $i++) {
            $adult_number[] = $average_number;
        }
        $pointer = count($adult_number) - 1;
        while ($pointer >= 0) {
            if (array_sum($adult_number) == $adults) {
                return $adult_number;
            }
            if (array_sum($adult_number) < $adults) {
                $adult_number[$pointer] = $adult_number[$pointer] + 1;
            }
            else if (array_sum($adult_number) > $adults) {
                $adult_number[$pointer] = $adult_number[$pointer] - 1;
            }
            if ($adult_number[$pointer] == $max_occupancy || $adult_number[$pointer] == $min_occupancy) {
                $pointer--;
            }

            if (array_sum($adult_number) == $adults) {
                return $adult_number;
            }
        }
        return $adult_number;
    }

    /**
     * Get Room Guest Number
     * 
     */
    protected function getRoomExactAdultNumbers($min_occupancy, $max_occupancy, $adults,$number)
    {
        $adult_number = [];
        $combo = [];
        $average_number = (int) (($min_occupancy + $max_occupancy) / 2);
        for ($i = 0; $i < $number; $i++) {
            $adult_number[] = $average_number;
        }
        $pointer = count($adult_number) - 1;
        while ($pointer >= 0) {
            if (array_sum($adult_number) == $adults) {
                return $adult_number;
            }
            if (array_sum($adult_number) < $adults) {
                $adult_number[$pointer] = $adult_number[$pointer] + 1;
            }
            else if (array_sum($adult_number) > $adults) {
                $adult_number[$pointer] = $adult_number[$pointer] - 1;
            }
            if ($adult_number[$pointer] == $max_occupancy || $adult_number[$pointer] == 1) {
                $pointer--;
            }

            if (array_sum($adult_number) == $adults) {
                return $adult_number;
            }
        }
        return $adult_number;
    }

    /**
     * Get Children Number
     * 
     */
    protected function getRoomChildrenNumbers($size, $children_count)
    {
        if ($size == 0) {
          $children = 0;
        } else {
          $children = ceil($children_count / $size);
        }

        $children_number = [];
        for ($i = 0; $i < $size; $i++) {
          if ($children_count <= 0) {
            $children_number[$i] = 0;
          } else {
            if($children_count >= $children){
                $children_number[$i] = $children;
            }
            else {
                $children_number[$i] = $children_count;   
            }
            $children_count -= $children;
          }
        }
        return $children_number;
    }

    public function becomeHost(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['exit_url'] = resolveRoute('dashboard');
            $data = array_merge($data,$this->commonManagementData());
            return view('hotels.become_host',$data);
        }
        else {

            $rules = array(
                'star_rating'   => 'required',
                'name'          => 'required',
                'description'   => 'required',
            );

            $this->validate($request,$rules,[],[]);

            $user = Auth::user();
            $user->user_type = 'host';
            $user->save();

            $hotel = new Hotel;
            $hotel->admin_commission = global_settings('hotel_admin_commission');
            $hotel->user_id = $user->id;
            $hotel->star_rating = $request->star_rating;
            $hotel->name  = $request->name;
            $hotel->description = $request->description;
            $hotel->save();

            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
            $redirect_url = route('host.hotels.edit',['id' => $hotel->id]);
            return redirect($redirect_url);
        }
    }

    /**
     * Store reservation details in the session and redirect to payment page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array status and redirect url
     */
    public function confirmReserve(Request $request)
    {
        $booking_attempt_id = Str::uuid()->toString();
        $reserve_data = array(
            'user_id' => $request->user_id,
            'hotel_id' => $request->hotel_id,
            'checkin' => $request->checkin,
            'checkin_formatted' => getDateInFormat($request->checkin),
            'checkout' => $request->checkout,
            'checkout_formatted' => getDateInFormat($request->checkout),
            'guests' => $request->guests,
            'coupon_code' => $request->coupon_code ?? '',
            'currency_code' => $request->currency_code,
            'rooms' => $request->rooms,
            'selected_plans' => $request->selected_plans,
            'selected_beds' => $request->selected_beds,
        );
        $reserve_params = array(
            'hotel_id' => $request->hotel_id,
            'booking_attempt_id' => $booking_attempt_id,
        );

        session(['payment.'.$booking_attempt_id => $reserve_data]);

        $redirect_url = resolveRoute('payment.home',$reserve_params);

        $status = 'redirect';

        if(!Auth::check()) {
            session(['url.intended' => $redirect_url]);
            $redirect_url = resolveRoute('login');
        }

        return compact('status','redirect_url');
    }
}
