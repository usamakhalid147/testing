<?php

/**
 * Listen All Events on Hotel Model
 *
 * @package     HyraHotel
 * @subpackage  Observers
 * @category    HotelObserver
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Observers;

use App\Models\Hotel;
use App\Models\HotelAddress;
use App\Models\HotelRoom;
use App\Models\HotelRoomPrice;
use App\Models\User;

class HotelObserver
{
    /**
     * Listen to the Hotel created event.
     *
     * @param  Hotel  $Hotel
     * @return void
     */
    public function created(Hotel $hotel)
    {
        $country_code = User::where('id',$hotel->user_id)->first()->country_code ?? view()->shared('default_country_code') ?? 'US';
        $country = resolve("Country")->where('name',$country_code)->first();
        if($country == '') {
            $country_code = 'US';
        }

        $hotel_address = new HotelAddress;
        $hotel_address->hotel_id = $hotel->id;
        $hotel_address->country_code = $country_code;
        $hotel_address->save();
    }

    /**
     * Listen to the Hotel updating event.
     *
     * @param  Hotel  $hotel
     * @return void
     */
    public function updating(Hotel $hotel)
    {
        if($hotel->isDirty('status')) {
            if((($hotel->getOriginal('status') == "in_progress" || $hotel->status == 'pending') && $hotel->completed_percent) && $hotel->admin_status == 'pending') {
                resolveAndSendNotification("awaitingForApproval",$hotel->id);
            }
            if(in_array($hotel->status, ['listed', 'unlisted'])) {
                resolveAndSendNotification("listingStatusUpdated",$hotel->id,$hotel->status);
            }
        }
        if($hotel->isDirty('admin_status') && $hotel->admin_status == 'approved') {
            resolveAndSendNotification("listingApproved",$hotel->id);
        }

        if($hotel->admin_status == 'resubmit') {
            resolveAndSendNotification("adminResubmitListing",$hotel->id);
        }
    }

    /**
     * Listen to the Hotel updated event.
     *
     * @param  Hotel  $hotel
     * @return void
     */
    public function updated(Hotel $hotel)
    {
       if($hotel->completed_percent != 100 && $hotel->status == 'listed') {
            $hotel->status = 'unlisted';
            $hotel->save();
        }
        if($hotel->admin_status == 'approved') {
            $user = User::find($hotel->user_id);
            if($user->is_host == 0) {
                $user->is_host = 1;
                $user->save();
            }
        }
    }
}