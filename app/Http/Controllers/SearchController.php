<?php

/**
 * Search Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    SearchController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelCalendar;
use App\Models\WishlistList;
use Carbon\Carbon;
use Session;
use Lang;
use DB;

class SearchController extends Controller
{
	/**
     * Redirect to Search based on list type
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$list_type = 'hotel';
		$list_search = $list_type.'_search';
		if(!in_array($list_type,['hotel'])) {
			$list_search = 'hotel_search';
		}
		$params = $request->except(['list_type']);
		$redirect_url = resolveRoute($list_search,$params);

		return redirect($redirect_url);
	}

	/**
     * Display Search page with filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function hotelSearch(Request $request)
	{
		$data['room_types'] = resolve("RoomType")->where('status',1);
		$data['search_latitude'] = 0;
		$data['search_longitude'] = 0;
		$data['search_viewport'] = '';
		$data['amenity_types'] = \App\Models\AmenityType::with('amenities')->activeOnly()->get()->map(function($type) {
			$amenities = \App\Models\Amenity::where('list_type','hotel')->where('amenity_type_id',$type->id)->get();
			return [
				'id' => $type->id,
				'name' => $type->name,
				'amenities' => $amenities,
			];
		});
		$data['hotel_rules'] = \App\Models\HotelRule::activeOnly()->get();
		$data['property_types'] = \App\Models\PropertyType::activeOnly()->get();
		$data['languages'] = \App\Models\Language::activeOnly()->get();
		$room_type = $request->room_type != '' ? explode(',', $request->room_type) : [];
		$amenities = $request->amenities != '' ? explode(',', $request->amenities) : [];
		$property_type = $request->property_type != '' ? explode(',', $request->property_type) : [];
		$hotel_rule = $request->hotel_rule != '' ? explode(',', $request->hotel_rule) : [];
		$language = $request->language != '' ? explode(',', $request->language) : [];
		$max_guests = view()->shared('max_guests');
		$adults = $request->adults > 1 ? $request->adults : 1;
		$children = $request->children > 0 ? $request->children : 0;
		$rooms = $request->rooms > 1 ? $request->rooms : 1;

		$request['checkin'] = $request->checkin ?? Carbon::today()->format('Y-m-d');
        $request['checkout'] = $request->checkout ?? Carbon::tomorrow()->format('Y-m-d');
		[$checkin,$checkout] = checkInValidDate($request->checkin,$request->checkout);

		$data['default_min_price'] = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency')));
		$data['default_max_price'] = round(currencyConvert(global_settings('max_price'),global_settings('default_currency')));
		$min_price = intval($request->min_price);
        $max_price = intval($request->max_price);
        if(!$min_price) {
            $min_price = $data['default_min_price'];
            $max_price = $data['default_max_price'];
        }
        elseif(!empty($previous_currency = Session::get('previous_currency'))) {
        	$min_price = ceil(currencyConvert($min_price,$previous_currency));
			$max_price = round(currencyConvert($max_price,$previous_currency));
			Session::forget('previous_currency');
        } 

		$search_filters = [
			"location" => $request->location ?? '',
			"adults" => $adults,
			"children" => $children,
			"rooms" => $rooms,
			"max_guests" => $max_guests,
			"checkin" => $checkin->format('Y-m-d'),
			"checkout" => $checkout->format('Y-m-d'),
			"star_rating" => [],
			"property_type" => $property_type,
			"amenities" => $amenities,
			"language" => $language,
			"min_price" => $min_price ?? $data['default_min_price'],
			"max_price" => $max_price ?? $data['default_max_price'],
			"cancellation_policy" => $request->cancellation_policy ?? '',
			"toggle_map" => $request->toggle_map ?? false,
			"list_type" => 'hotel',
			"place_id" => $request->place_id ?? '',
			"page" => $request->page ?? "1",
		];
		$data["searchFilter"] = $search_filters;
		if($request->filled('place_id')) {
			$place_details = file_get_contents_curl('https://maps.googleapis.com/maps/api/place/details/json?key='.credentials('map_server_key','googleMap').'&place_id='.$request->place_id.'&fields=address_component,geometry,formatted_address');
			$place_result = json_decode($place_details,true);
			if($place_result['status'] == 'OK' && (isset($place_result['result']) && isset($place_result['result']['geometry']))) {
				$geometry = $place_result['result']['geometry'];
				$data['search_latitude'] = $geometry['location']['lat'];
				$data['search_longitude'] = $geometry['location']['lng'];
				$data['search_viewport'] = $geometry['viewport'];
			}
		}
		
		return view('search',$data);
	}

	/**
     * Search for listing with given filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json listing_details with paginate data
     */
	public function searchResults(Request $request)
	{
		if($request->list_type != 'hotel') {
			return response()->json([
				'status' => 'redirect',
				'redirect_url' => resolveRoute('search',['list_type' => $request->list_type]),
			]);
		}
		
		$user_id = \Auth::id();
		$adults = $request->adults;
		$children = $request->children;
		$total_occupancy = $adults + $children;
		$total_rooms = $request->rooms;
		$location = $request->location;
		$booking_days = getDays($request->checkin, $request->checkout);
        array_pop($booking_days);
        $startDate = current($booking_days);
        $endDate = end($booking_days);
        $total_nights = count($booking_days);

        $currency_rate = resolve('Currency')->where('code', session('currency'))->first()->rate;
		
		$default_min_price = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency')));
		$default_max_price = round(currencyConvert(global_settings('max_price'),global_settings('default_currency')));
		
		$min_price = $request->min_price ?? $default_min_price;
		$max_price = $request->max_price ?? $default_max_price;

        if(empty($location)) {
	        // Location Filter
			$minLat	= -1000;
			$minLng	= -1000;
			$maxLat	= 1000;
			$maxLng	= 1000;
			if($request->filled('map_data') && count($request->map_data) > 0) {
				$minLat = $request->map_data['minLat'];
				$minLng = $request->map_data['minLng'];
				$maxLat = $request->map_data['maxLat'];
				$maxLng = $request->map_data['maxLng'];
			}
			else if($request->filled('place_id')) {
				$place_details = file_get_contents_curl('https://maps.googleapis.com/maps/api/place/details/json?key='.credentials('map_server_key','googleMap').'&place_id='.$request->place_id.'&fields=address_component,geometry,formatted_address');
				$place_result = json_decode($place_details,true);
				if($place_result['status'] == 'OK' && (isset($place_result['result']) && isset($place_result['result']['geometry']))) {
					$geometry = $place_result['result']['geometry'];
					$minLat = $geometry['viewport']['southwest']['lat'] - 0.1;
					$minLng = $geometry['viewport']['southwest']['lng'] - 0.1;
					$maxLat = $geometry['viewport']['northeast']['lat'] + 0.1;
					$maxLng = $geometry['viewport']['northeast']['lng'] + 0.1;
				}
			}
			if($minLng > $maxLng) {
				if($maxLng > 0) {
					$maxLng = $minLng;
					$minLng = "-180";
				}
				else {
					$maxLng = "180";
				}
			}

			$hotel_ids = Hotel::with(['hotel_address' => function($query) use($minLat, $maxLat, $minLng, $maxLng) {
						$query->whereRaw("latitude between $minLat and $maxLat and longitude between $minLng and $maxLng");
					}])
					->verified()
					->listed()
					->pluck('id');
        }
        else {
        	$hotel_ids = Hotel::with('hotel_address')
					->verified()
					->listed()
					->whereHas('hotel_address', function($query) use($location){
						$country = \App\Models\Country::where('full_name',$location)->first();
						$query->where('country_code',optional($country)->name)->orWhere('city',$location);
					})
					->pluck('id');
        }

		$all_hotel_rooms = HotelRoom::with('hotel_room_price')
						->whereIn('hotel_id',$hotel_ids)
						->where('status','Listed')
						->get();

		$availableRooms = collect();
		$all_hotel_rooms->map(function($room) use($startDate, $endDate, $total_nights, $currency_rate, &$availableRooms){
			
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

            // Get Available Number
            $tmp_number = $room->number - $room_calendar_number->calendar_number;

            if($tmp_number < 1) {
            	return false;
            }

            // Get Price
            $room_data = $total_nights - ($room_calendar_price->calendar_price_data ?? 0);
            $room_price = $hotel_room_price->price * $room_data;
            $tmp_price = $room_price + ($room_calendar_price->calendar_price ?? 0);
            $adult_price = numberFormat($total_nights * $hotel_room_price->adult_price);
            $child_price = numberFormat($total_nights * $hotel_room_price->child_price);

			$availableRooms[] = [
				'id' => $room->id,
				'hotel_id' => $room->hotel_id,
				'tmp_number' => $tmp_number,
				'tmp_price' => $tmp_price,
				'adult_price' => $adult_price,
				'child_price' => $child_price,
				'adults' => $room->adults,
				'max_adults' => $room->max_adults,
				'children' => $room->children,
				'max_children' => $room->children,
			];
		});

		$recommended_price_query = "CASE id";
		$recommended_day_price_query = "CASE id";
		if($availableRooms->count() > 0) {
			$availableRooms->groupBy('hotel_id')->map(function($rooms,$hotel_id) use($adults,$children,$total_rooms,$total_nights,$total_occupancy, $min_price, $max_price,&$recommended_price_query, &$recommended_day_price_query){
				$rooms = $rooms->where('tmp_price','>=',$min_price)->values();
				$comboRooms = $this->getComboRooms($rooms);
				if($comboRooms->count() > 0) {

					$min_adults = $comboRooms->min('adults');
			        $max_adults = $comboRooms->max('max_adults');

			        $min_children = $comboRooms->min('children');
			        $max_children = $comboRooms->max('max_children');

					$recommended_rooms = collect();
					$tmp_room = [];
					if($max_adults >= $adults && $max_children >= $children && $total_rooms == 1) {
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

						$adult_numbers = $this->getRoomAdultNumbers($min_adults,$max_adults,$adults,$number);
						$children_numbers = $this->getRoomChildrenNumbers($number,$children);

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
					$price = $recommended_rooms->sum('tmp_price') ?? 0;
			        $recommended_price_query .= "\nwhen ".$hotel_id." then ".$price;
			        $recommended_day_price_query .= "\nwhen ".$hotel_id." then ".($price / $total_nights);
			    }
			});
		}
		else {
			$recommended_price_query .= "\nwhen 0 then 0";
			$recommended_day_price_query .= "\nwhen 0 then 0";
		}
		$recommended_price_query .= "\nEND";
		$recommended_day_price_query .= "\nEND";
		$start_date = Carbon::createFromFormat('Y-m-d', $request->checkin);
		$today = Carbon::today();

		$diff_in_days = $start_date->diffInDays($today);
		$result = Hotel::select('*')
			->selectRaw("(".$recommended_price_query.") as tmp_price")
			->selectRaw("(".$recommended_day_price_query.") as day_price")
			->with('hotel_address','hotel_photos','hotel_rooms')
			->notDeleted()
			->listed()
			->verified()
			->havingRaw("tmp_price is NOT NULL")
			->Where(function($query) use($diff_in_days){
				$query->whereNull('notice_days')
					->orWhere('notice_days','<=',$diff_in_days);
			});
						
		// Property Type Filter
		if($request->filled('property_type') && count($request->property_type)) {
			$result->whereIn('property_type', array_filter($request->property_type));
		}

		// Star Rating Filter
		if($request->filled('star_rating') && count($request->star_rating)) {
			$result->whereIn('star_rating', array_filter($request->star_rating));
		}

		// Amenities Filter
		if($request->filled('amenities') && count($request->amenities)) {
			$amenities = array_filter($request->amenities);
			foreach($amenities as $amenity) {
                $result->whereRaw('find_in_set('.$amenity.', amenities)');
            }
		}
		
		// $result->whereHas('hotel_address', function($query) use($minLat, $maxLat, $minLng, $maxLng) {
		// 	$query->whereRaw("latitude between $minLat and $maxLat and longitude between $minLng and $maxLng");
		// });

		// Price Filter
		if(isset($min_price) && isset($max_price)) {
            $result->selectRaw("(SELECT (".$max_price.")) as max_price")
                ->having('day_price','>=',$min_price)
                ->havingRaw('(max_price = '.$default_max_price.' or day_price <='.$max_price.')');
        }

		// Format Result
		$wishlist_list = collect();
		if(isset($user_id)) {
			$wishlist_list = WishlistList::listTypeBased('hotel')->where('user_id',$user_id)->get();
		}

		$listing_result =  $result->orderByRaw("RAND(1234)")->paginate(10);
		$listing_data = $listing_result->getCollection();

		$top_picks = $result->topPicks()->get();
		$request['wishlist_list'] = $wishlist_list;
		$request['total_nights'] = $total_nights;

		$result_data = $this->mapHotelsData($listing_data, $request->all());
		$top_picks_hotels = $this->mapHotelsData($top_picks, $request->all());
		
		$result_data = $listing_data->map(function($hotel) use ($user_id,$wishlist_list,$request,$min_price,$total_nights){
			$hotel_data = $hotel->only(['id','name','room_type_name','booking_type','cancellation_policy','status','total_rating']);
			$hotel_data['rating'] = round($hotel->rating);
			
			$wishlist_count = 0;
	        if(isset($user_id)) {
	            $wishlist_count = $wishlist_list->where('list_id',$hotel->id)->count();
	        }
			$hotel_data['is_saved'] = $wishlist_count > 0;

			$hotel_data['url'] = resolveRoute('hotel_details',['id' => $hotel->id,'checkin' => $request->checkin,'checkout' => $request->checkout,'adults' => $request->adults,'children' => $request->children, 'rooms' => $request->rooms]);

			$location_data = $hotel->hotel_address->only(['latitude','longitude','city','state','country_name']);

		    // Hotel Amanity
		    $price_data['amenities'] = \App\Models\Amenity::whereIn('id', explode(',',$hotel->amenities))->limit(5)->get()->map(function($amenity) {
		    		return  [
		    			'name' => $amenity->name,
		    			'image_src' => $amenity->image_src,
		    		];
		    });
	        
	        $nights_text = $total_nights > 1 ? Lang::get('messages.per_nights',['key' => $total_nights]) : Lang::get('messages.per_night',['key' => $total_nights]);

	        $hotel_data['checkin_text'] = Lang::get('messages.checkin');
	        $hotel_data['checkout_text'] = Lang::get('messages.checkout');
	        if($hotel->checkin_time == 'flexible') {
	        	$hotel_data['checkin'] = Lang::get('messages.flexible');
	        }
	        else {
	        	$hotel_data['checkin'] = getTimeInFormat($hotel->checkin_time);
	        }

	        if($hotel->checkin_time == 'flexible') {
	        	$hotel_data['checkout'] = Lang::get('messages.flexible');
	        }
	        else {
	        	$hotel_data['checkout'] = getTimeInFormat($hotel->checkout_time);
	        }
			
	        $price_data['price_text'] = session('currency_symbol').' '.number_format($hotel->tmp_price).' '.$nights_text;

			$price_data['property_type_name'] = $hotel->property_type_name;

			$price_data['hotel_star_rating'] = (int)$hotel->star_rating;
			$price_data['review_text'] = Lang::get('messages.reviews');
			$price_data['location'] = $hotel->hotel_address->city.', '.$hotel->hotel_address->country_code;
			$price_data['image_src'] = $hotel->image_src;
			$price_data['review_stars'] = $hotel->getReviewStars();
	        
			$photos_data['photos_list'] = $hotel->hotel_photos->map(function($photo) {
				return [
					'id' 	=> $photo->id,
					'name'	=> $photo->name,
					'image_src'	=> $photo->image_src,
				];
			})->toArray();

			return array_merge($hotel_data,$location_data,$photos_data,$price_data);
		});

		return response()->json([
			'current_page'		=> $listing_result->currentPage(),
			'data'				=> $result_data,
			'top_picks_hotels'=> $top_picks_hotels,
			'from'				=> $listing_result->firstItem(),
			'to'				=> $listing_result->lastItem(),
			'total'				=> $listing_result->total(),
			'per_page'			=> $listing_result->perPage(),
			'last_page'			=> $listing_result->lastPage(),
		]);
	}

	/**
	 * 
	 * Get Hotel Details
	 */
	protected function mapHotelsData($hotels, $filters) 
	{
		return $hotels->map(function($hotel) use ($filters){
			$hotel_data = $hotel->only(['id','name','room_type_name','booking_type','cancellation_policy','status','total_rating']);
			$hotel_data['rating'] = round($hotel->rating);
			
			$wishlist_count = 0;
	        if(isset($user_id)) {
	            $wishlist_count = $filters['wishlist_list']->where('list_id',$hotel->id)->count();
	        }
			$hotel_data['is_saved'] = $wishlist_count > 0;

			$hotel_data['url'] = resolveRoute('hotel_details',['id' => $hotel->id,'checkin' => $filters['checkin'],'checkout' => $filters['checkout'],'adults' => $filters['adults'],'children' => $filters['children'], 'rooms' => $filters['rooms']]);

			$location_data = $hotel->hotel_address->only(['latitude','longitude','city','state','country_name']);

		    // Hotel Amanity
		    $price_data['amenities'] = \App\Models\Amenity::whereIn('id', explode(',',$hotel->amenities))->limit(5)->get()->map(function($amenity) {
		    		return  [
		    			'name' => $amenity->name,
		    			'image_src' => $amenity->image_src,
		    		];
		    });
	        
	        $nights_text = $filters['total_nights'] > 1 ? Lang::get('messages.per_nights',['key' => $filters['total_nights']]) : Lang::get('messages.per_night',['key' => $filters['total_nights']]);

	        $hotel_data['checkin_text'] = Lang::get('messages.checkin');
	        $hotel_data['checkout_text'] = Lang::get('messages.checkout');
	        if($hotel->checkin_time == 'flexible') {
	        	$hotel_data['checkin'] = Lang::get('messages.flexible');
	        }
	        else {
	        	$hotel_data['checkin'] = getTimeInFormat($hotel->checkin_time);
	        }

	        if($hotel->checkin_time == 'flexible') {
	        	$hotel_data['checkout'] = Lang::get('messages.flexible');
	        }
	        else {
	        	$hotel_data['checkout'] = getTimeInFormat($hotel->checkout_time);
	        }

	        $price_data['price_text'] = session('currency_symbol').$hotel->tmp_price.' '.$nights_text;

			$price_data['property_type_name'] = $hotel->property_type_name;

			$price_data['hotel_star_rating'] = (int)$hotel->star_rating;
			$price_data['review_text'] = Lang::get('messages.reviews');
			$price_data['location'] = $hotel->hotel_address->state.', '.$hotel->hotel_address->country_code;
			$price_data['image_src'] = $hotel->image_src;
			$price_data['review_stars'] = $hotel->getReviewStars();
	        
			$photos_data['photos_list'] = $hotel->hotel_photos->map(function($photo) {
				return [
					'id' 	=> $photo->id,
					'name'	=> $photo->name,
					'image_src'	=> $photo->image_src,
				];
			})->toArray();

			return array_merge($hotel_data,$location_data,$photos_data,$price_data);
		});
	}

	/**
	 * Generate Combo Rooms
	 * 
	 */
	protected function getComboRooms($rooms) 
	{
		$comboRooms = collect();
        $rooms->map(function($room) use(&$comboRooms){
            $base_price = $room['tmp_price'];
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
                        'tmp_price' => $tmp_price,
                    ];
                }
            }
        });
        return $comboRooms;
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
}