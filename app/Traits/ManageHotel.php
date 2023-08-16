<?php

/**
 * Trait for Manage Hotel
 *
 * @package     HyraHotel
 * @subpackage  Traits
 * @category    ManageHotel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Traits;

use App\Models\Hotel;
use App\Models\HotelAddress;
use App\Models\HotelPhoto;
use App\Models\HotelCalendar;
use App\Models\RoomType;
use App\Models\AmenityType;
use App\Models\Amenity;
use App\Models\HotelRule;
use App\Models\GuestAccess;
use App\Models\PropertyType;
use App\Models\Country;
use App\Models\BedType;
use App\Models\HotelRoomPhoto;
use App\Models\Reservation;
use Lang;

trait ManageHotel
{
    /**
     * Get All Common Management data related to Manage Hotel
     *
     * @return Array
     */
    protected function commonManagementData()
    {
        $room_types = RoomType::activeOnly()->get();
        $property_types = PropertyType::activeOnly()->get();
        $amenity_types = AmenityType::activeOnly()->whereHas('amenities',function($query) {
            $query->where('list_type','hotel');
        })
        ->with(['amenities' => function($query) {
            return $query->where('list_type','hotel');
        }])->get();
        $hotel_rules = HotelRule::activeOnly()->get();
        $guest_accesses = GuestAccess::activeOnly()->get();
        $bed_types = BedType::activeOnly()->get();

        return compact('room_types','property_types','hotel_rules','guest_accesses','amenity_types','bed_types');
    }

    /**
     * Get All Management data related to Given Hotel
     *
     * @return Array
     */
    protected function hotelManagementData($hotel)
    {
        $selected_amenities = Amenity::whereIn('id', explode(',',$hotel->amenities))->get();
        $selected_amenity_types = AmenityType::activeOnly()->whereHas('amenities',function($query) use($hotel) {
            return $query->where('list_type','hotel')->whereIn('id', explode(',',$hotel->amenities));
        })
        ->with(['amenities' => function($query) use($hotel) {
            return $query->where('list_type','hotel')->whereIn('id', explode(',',$hotel->amenities));
        }])->get();
        $selected_hotel_rules = HotelRule::whereIn('id', explode(',',$hotel->hotel_rules))->get();
        $selected_guest_accesses = GuestAccess::whereIn('id', explode(',',$hotel->guest_accesses))->get();
        
        return compact('hotel','selected_amenities','selected_amenity_types','selected_hotel_rules','selected_guest_accesses');
    }

    /**
     * Save Hotel Data
     *
     * @param String $hotel_id
     * @param Array $hotel_data
     * @return Void
     */
    protected function saveHotelData($hotel_id, $hotel_data = array())
    {
        $hotel = Hotel::find($hotel_id);
        $hotel->setLocale(global_settings('default_language'));
        foreach($hotel_data as $key => $value) {
            $hotel->$key = $value;
        }
        $hotel->save();
    }

    /**
     * Save Hotel Description translation Data
     *
     * @param String $hotel_id
     * @param Array $translations
     * @return Void
     */
    protected function saveTranslationData($hotel_id, $translations)
    {
        if(count($translations)) {
            $hotel = Hotel::find($hotel_id);
            foreach($translations as $locale => $translation) {
                $hotel->setLocale($locale);
                foreach($translation as $key => $value) {
                    $hotel->$key = $value;
                }
                $hotel->save();
            }
        }
    }

    /**
     * Delete Hotel Description translation Data
     *
     * @param String $room_id
     * @param Array $translations
     * @return Void
     */
    protected function deleteTranslationData($hotel_id, $locales)
    {
        $locales = explode(',',$locales);
        if(count($locales)) {
            $hotel = Hotel::find($hotel_id);
            foreach($locales as $locale) {
                $hotel->forgetAllTranslations($locale);
                $hotel->save();
            }
        }
    }

    /**
     * Save Hotel Location Data
     *
     * @param String $hotel_id
     * @param Array $loc_data
     * @return Void
     */
    protected function saveLocationData($hotel_id, $loc_data = array())
    {
        $validate_country = $this->validateCountry($loc_data['country_code']);

        if(!$validate_country['status']) {
            return json_encode($validate_country);
        }

        $hotel_addr = HotelAddress::where('hotel_id', $hotel_id)->first();

        foreach($loc_data as $key => $value) {
            $hotel_addr->$key = $value;
        }

        $hotel_addr->save();
    }

    /**
     * Upload Hotel Hotel Photos with Order
     *
     * @param String $hotel_id
     * @return Void
     */
    protected function updateHotelPhotos($hotel_id,$photos_list)
    {
        $last_photo = HotelPhoto::where('hotel_id',$hotel_id)->latest('order_id')->first();
        $last_order_id = optional($last_photo)->order_id;

        $image_data['add_time'] = true;
        $hotel_photo = new HotelPhoto;
        $hotel_photo->hotel_id = $hotel_id;
        $image_data['target_dir'] = $hotel_photo->getUploadPath();

        $image_handler = resolve('App\Contracts\ImageHandleInterface');
        $error_list = [];
        foreach($photos_list as $index => $photo) {
            $image_size = number_format($photo->getSize() / 1048576, 2);
            if($image_size <= 5) {
                $image_data['name_prefix'] = 'hotel_'.$index;
                $upload_result = $image_handler->upload($photo,$image_data);

                if($upload_result['status']) {
                    $photos = new HotelPhoto;
                    $photos->hotel_id = $hotel_id;
                    $photos->image = $upload_result['file_name'];
                    $photos->upload_driver= $upload_result['upload_driver'];
                    $photos->order_id = ++$last_order_id;
                    $photos->save();
                }
            } else {
                array_push($error_list,$index);
            }
        }

        if (count($error_list) > 0) {
            return $error_list;
        }
        return [];
    }

    /**
     * Delete Given Hotel Photos
     *
     * @param Array $image_ids
     *
     * @return Boolean
     */
    public function deleteHotelPhotos($image_ids)
    {
        if(count($image_ids) == 0) {
            return false;
        }
        $hotel_photos = HotelPhoto::whereIn('id',$image_ids)->get();
        $hotel_photos->each(function($hotel_photo) {
            $handler = $hotel_photo->getImageHandler();
            $hotel_photo->deleteImageFile();
            $hotel_photo->delete();
        });
        return true;
    }

    /**
     * Check and Update Hotel Status
     *
     * @param String $hotel_id
     * @return Void
     */
    protected function updateHotelStepStatus($hotel_id)
    {
        $hotel = Hotel::find($hotel_id);
        if($hotel->completed_percent != 100 && $hotel->status != '') {
            $hotel->status = 'unlisted';
            $hotel->save();
        }
    }

    /**
     * Delete Hotel with all relations
     *
     * @param  Integer  $hotel_id
     * @return Array $return_data
     */
    protected function deleteHotel($hotel_id)
    {
        try {
            $reservation_count = Reservation::where('hotel_id',$hotel_id)->count();
            if($reservation_count > 0) {
                return ['status' => false,'status_message' => Lang::get('admin_messages.this_hotel_has_some_reservation')];
            }
            $hotel = Hotel::where('id',$hotel_id)->first();

            $hotel_photos = HotelPhoto::where('hotel_id',$hotel_id)->get()->pluck('id')->toArray();
            $this->deleteHotelPhotos($hotel_photos);
            $hotel_rooms = $hotel->hotel_rooms;
            $hotel_room_photos = $hotel_rooms->each(function($hotel_room) {
                $hotel_room_photos = $hotel_room->hotel_room_photos->pluck('id')->toArray();
                $this->deleteHotelRoomPhotos($hotel_room_photos);
                $hotel_room->delete();
            });
            $hotel_count = Hotel::where('user_id',$hotel->user_id)->where('id','!=',$hotel_id)->count();
            if($hotel_count == 0) {
                $user = $hotel->user;
                $user->user_type = 'user';
                $user->save();
            }
            Hotel::where('id', intval($hotel_id))->delete();
        }
        catch (\Exception $e) {
            return ['status' => false, 'status_message' => $e->getMessage()];
        }
        return ['status' => true, 'status_message' => ''];
    }

    /**
     * Delete Given Hotel Photos
     *
     * @param Array $image_ids
     *
     * @return Boolean
     */
    public function deleteHotelRoomPhotos($image_ids)
    {
        if(count($image_ids) == 0) {
            return false;
        }
        $room_photos = HotelRoomPhoto::whereIn('id',$image_ids)->get();
        $room_photos->each(function($room_photo) {
            $handler = $room_photo->getImageHandler();
            $image_data['target_dir'] = $room_photo->getUploadPath();
            $image_data['name'] = $room_photo->image;
            $handler->destroy($image_data);

            $room_photo->delete();
        });
        return true;
    }    

    /**
     * Validate Given Country Available
     *
     * @param String $name
     * @return Array $return_data
     */
    protected function validateCountry($name)
    {
        $return_data = array('status' => true, 'status_message' => 'Country Found.');
        $country = resolve("Country")->where('name', $name)->count();

        if(!$country) {
            $return_data = array('status' => false, 'status_message' => 'Country Not Found.');
        }
        return $return_data;
    }

    /**
     * Get Nearest Hotels for given Hotel
     *
     * @param String $latidude
     * @param String $longitude
     * @param String $hotel_id
     * @return Collection $similar_hotels
     */
    protected function getSimilarHotels($latitude, $longitude, $hotel_id)
    {
        if($latitude == '' || $longitude == '') {
            return collect();
        }
        $similar_hotels = Hotel::join('hotel_addresses', function($join) {
            $join->on('hotels.id', '=', 'hotel_addresses.hotel_id');
        })
        ->select(\DB::Raw('*, ( 3959 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) as distance'))
        ->having('distance', '<=', SIMILAR_ROOM_DISTANCE)
        ->where('hotels.id', '!=', $hotel_id)
        ->viewOnly()
        // ->activeUser()
        ->get();

        return $similar_hotels;
    }

    /**
     * Duplicate Given Room
     *
     * @param String $room_id
     * @return App\Models\Room $room
     */
    protected function makeDuplicate($room_id)
    {
        $selected_hotel = Hotel::loadRelations()->findOrFail($room_id);
        $room = $selected_hotel->replicate();
        $room->status = 'In Progress';
        $room->admin_status = global_settings('default_listing_status');
        $room->saveQuietly();
        
        $room_address = $selected_room->room_address->replicate();
        $room_address->room_id = $room->id;
        $room_address->save();

        $room_price = $selected_room->room_price->replicate();
        $room_price->room_id = $room->id;
        $room_price->save();

        $file_path = resolve('App\Models\RoomPhoto')->filePath;
        $old_file_path = public_path($file_path.'/'.$room_id);
        $new_file_path = public_path($file_path.'/'.$room->id);

        if (\File::isDirectory($old_file_path)) {
            \File::copyDirectory($old_file_path, $new_file_path);
        }

        $selected_room->room_photos->each(function($selected_room_photo) use($room) {
            $room_photo = $selected_room_photo->replicate();
            $room_photo->room_id = $room->id;
            $room_photo->save();
        });

        return $room;
    }
}