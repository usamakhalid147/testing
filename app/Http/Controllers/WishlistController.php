<?php

/**
 * Wishlist Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    WishlistController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistList;
use Illuminate\Http\Request;
use Auth;
use Lang;

class WishlistController extends Controller
{
    /**
     * Map Wishlist Data to display
     *
     * @param \Illuminate\Database\Eloquent\Collection $wishlists
     * @param Mixed $list_id
     * @return \Illuminate\Http\Response
     */
    protected function mapWishlistData($wishlists,$list_id = '')
    {
        return $wishlists->map(function($wishlist) use($list_id) {
            $is_saved = false;
            if($list_id != '') {
                // 
            }
            return [
                'id' => $wishlist->id,
                'user_id' => $wishlist->user_id,
                'name' => $wishlist->name,
                'list_count' => $wishlist->list_count,
                'experience_count' => $wishlist->experience_count,
                'target_link' => resolveRoute('wishlist.list',['id' => $wishlist->id]),
                'thumbnail' => $wishlist->thumbnail ?? '',
                'privacy' => $wishlist->privacy,
                'is_saved' => $is_saved,
            ];
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wishlists.index');
    }

    /**
     * Get All The Wishlist List.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllWishlists(Request $request)
    {
        if(!Auth::check()) {
            $redirect_url = url()->previous();
            session(['url.intended' => $redirect_url]);
            $redirect_url = resolveRoute('login');
            return response()->json([
                'status' => 'redirect',
                'redirect_url' => $redirect_url,
            ]);
        }

        $user_id = Auth::id();
        $user_wishlists = Wishlist::where('user_id',$user_id)->get();
        return response()->json([
            'status' => true,
            'status_message' => Lang::get('messages.listed_successfully'),
            'data' => $this->mapWishlistData($user_wishlists,$request->list_id),
        ]);
    }

    /**
     * Display the Saved wishlist list for given wishlist
     *
     * @param  \App\Models\WishList  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function wishlistList(Request $request)
    {
        $wishlist = Wishlist::findOrFail($request->id);
        view()->share('wishlist_name',$wishlist->name);
        $wishlist_list = WishlistList::where('wishlist_id',$wishlist->id)->get();
        
        $data['wishlist'] = [
            "wishlist_id" => $wishlist->id,
            "saved_name" => $wishlist->name,
            "wishlist_name" => $wishlist->name,
            "wishlist_privacy" => $wishlist->privacy,
        ];

        $hotel_data = \App\Models\Hotel::find($wishlist_list->where('list_type','hotel')->pluck('list_id'));
        $hotels = $hotel_data->map(function($hotel) use ($wishlist_list) {
            $hotel_data = $hotel->only(['id','name','room_type_name','booking_type','cancellation_policy','status','total_rating']);
            $hotel_data['rating'] = floatval($hotel->rating);

            $hotel_data['is_saved'] = true;

            $hotel_data['url'] = resolveRoute('hotel_details',['id' => $hotel->id]);

            $location_data = $hotel->hotel_address->only(['latitude','longitude','city','state','country_name']);

            // Hotel Amanity
            $price_data['amenities'] = \App\Models\Amenity::whereIn('id', explode(',',$hotel->amenities))->limit(5)->get()->map(function($amenity) {
                    return  [
                        'name' => $amenity->name,
                        'image_src' => $amenity->image_src,
                    ];
            });
            
            $nights_text = Lang::get('messages.per_night',['key' => '1']);

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

            $price = $hotel->hotel_rooms->pluck('hotel_room_price')->sortBy('price')->first()->price;

            $price_data['price_text'] = session('currency_symbol').$price.' '.$nights_text;

            $price_data['property_type_name'] = $hotel->property_type_name;

            $price_data['hotel_star_rating'] = (int)$hotel->star_rating;
            $price_data['review_text'] = Lang::get('messages.reviews');
            $price_data['location'] = $hotel->hotel_address->state.', '.$hotel->hotel_address->country_code;

            
            $photos_data['photos_list'] = $hotel->hotel_photos->map(function($photo) {
                return [
                    'id'    => $photo->id,
                    'name'  => $photo->name,
                    'image_src' => $photo->image_src,
                ];
            })->toArray();

            return array_merge($hotel_data,$location_data,$photos_data,$price_data);
        });

        $data['wishlist_lists'] = array(
            'hotels' => $hotels,
            'experiences' => $experiences ?? collect()
        );

        return view('wishlists.list_detail',$data);
    }

    /**
     * Create New Wishlist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createWishlist(Request $request)
    {
        if($request->wishlist_name == '') {
            return response()->json([
                'status' => false,
                'status_message' => Lang::get('messages.wishlist_name_is_required'),
            ]);
        }

        $status_message = Lang::get('messages.wishlist_created_successfully');
        if($request->filled('wishlist_id')) {
            $status_message = Lang::get('messages.wishlist_updated_successfully');
        }

        $wishlist_count = Wishlist::where('user_id',Auth::id())->where('id','!=',$request->wishlist_id)->where('name',$request->wishlist_name)->count();
        if($wishlist_count > 0) {
            return response()->json([
                'status' => false,
                'status_message' => Lang::get('messages.wishlist_name_already_use'),
            ]);
        }

        $wishlist = Wishlist::firstOrNew(['id' => $request->wishlist_id]);
        $wishlist->user_id = Auth::id();
        $wishlist->name = $request->wishlist_name;
        $wishlist->privacy = $request->wishlist_privacy;
        $wishlist->save();
        
        $user_wishlists = Wishlist::where('user_id',Auth::id())->get();
        return response()->json([
            'status' => true,
            'status_text' => Lang::get('messages.success'),
            'status_message' => $status_message,
            'data' => $this->mapWishlistData($user_wishlists,$request->hotel_id),
        ]);
    }

    /**
     * Save the listing to selected Wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveToWishlist(Request $request)
    {
        $wishlist = Wishlist::where('user_id',Auth::id())->find($request->wishlist_id);
        if($wishlist == '') {
            return response()->json([
                'status' => false,
                'status_message' => Lang::get('messages.unauthorized_access'),
            ]);
        }

        $wishlist_list = new WishlistList;
        $wishlist_list->user_id = Auth::id();
        $wishlist_list->wishlist_id = $wishlist->id;
        $wishlist_list->list_type = $request->list_type;
        $wishlist_list->list_id = $request->list_id;
        $wishlist_list->save();
        if($wishlist_list->list_type == 'hotel') {
            $wishlist->list_count = $wishlist->list_count + 1;
            if($wishlist->thumbnail == '') {
                $hotel = \App\Models\Hotel::find($wishlist_list->list_id);
                $wishlist->thumbnail = $hotel->image_src;
            }
        }
        else if($wishlist_list->list_type == 'experience') {
            $wishlist->experience_count = $wishlist->experience_count + 1;
            if($wishlist->thumbnail == '') {
                $experience = \Modules\Experience\Models\Experience::find($wishlist_list->list_id);
                $wishlist->thumbnail = $experience->image_src;
            }
        }
        $wishlist->save();
        
        return response()->json([
            'status' => true,
            'status_text' => Lang::get('messages.success'),
            'status_message' => Lang::get('messages.saved_to').' '.$wishlist->name,
        ]);
    }

    /**
     * remove the listing to selected Wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeFromWishlist(Request $request)
    {
        $wishlist_list = WishlistList::with('wishlist')->where('user_id',Auth::id())->where('list_id',$request->list_id)->first();
        if($wishlist_list == '') {
            return response()->json([
                'status' => false,
                'status_message' => Lang::get('messages.failed'),
            ]);
        }

        $wishlist = $wishlist_list->wishlist;
        if($wishlist_list->list_type == 'hotel') {
            $wishlist->list_count = $wishlist->list_count - 1;
        }
        else if($wishlist_list->list_type == 'experience') {
            $wishlist->experience_count = $wishlist->experience_count - 1;
        }
        $wishlist->save();
        
        $wishlist_list->delete();
        
        return response()->json([
            'status' => true,
            'status_text' => Lang::get('messages.success'),
            'status_message' => Lang::get('messages.removed_from').' '.$wishlist->name,
        ]);
    }

    /**
     * Remove the specified wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyWishlist(Request $request)
    {
        if($request->wishlist_id == '') {
            return response()->json([
                'status' => false,
                'status_message' => Lang::get('messages.invalid_request'),
            ]);
        }
        try {
            Wishlist::where('id',$request->wishlist_id)->delete();
            return response()->json([
                'status' => 'redirect',
                'redirect_url' => resolveRoute('wishlists'),
                'status_message' => Lang::get('messages.success'),
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_message' => $e->getMessage(),
            ]);
        }   
    }
}
