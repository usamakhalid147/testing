<?php

/**
 * Report Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    ReportController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CollectionExport;
use Lang;

class ReportController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['active_menu'] = 'reports';
        $this->view_data['sub_title'] = Lang::get('admin_messages.view_report');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['filter_list'] = array(
            [
                'name' => 'hotels',
                'display_name' => Lang::get('admin_messages.hotels'),
                'title' => ['Property Id','Created On','Property Name','Property star rating','Property type','Property telephone number','Property ext number','Property fax number','Company id','Company name'],
                'columns' => ['property_id','created_on','property_name','property_star_rating','property_type','property_telephone_number','property_ext_number','property_fax_number','company_id','company_name'],
            ],
            [
                'name' => 'reservations',
                'display_name' => Lang::get('admin_messages.total_booking_report'),
                'title' => ['User id', 'User Name', 'Email Address', 'Mobile number', 'Property id', 'Property name', 'Property star rating', 'Property type', 'Property telephone number', 'Property ext number', 'Property fax number', 'Property address', 'Ward', 'City', 'State', 'Country', 'Postal code', 'Property website', 'Property email', 'booking confirmation code', 'booking made on', 'room category', 'check in date', 'check in time', 'check out date', 'check out time', 'adults', 'children', 'room rate per night', 'total room charges', 'number of extra adults', 'total extra adult charges', 'Meal plan', 'discount amount', 'total amount', 'Property tax', 'Property service charge', 'Duhiviet service fee', 'grand total', 'cancellation policy', 'status'],
                'columns' => ['user_id', 'full_name', 'email_address', 'mobile_number', 'property_id', 'property_name', 'property_star_rating', 'property_type', 'property_telephone_number', 'property_ext_number', 'property_fax_number', 'property_address', 'ward', 'city', 'state', 'country', 'postal_code', 'property_website', 'property_email', 'booking_confirmation_code', 'booking_made_on', 'room_category', 'check_in_date', 'check_in_time', 'check_out_date', 'check_out_time', 'adults', 'children', 'room_rate_per_night', 'total_room_charges', 'number_of_extra_adults', 'total_extra_adult_charges', 'meal_plan', 'discount_amount', 'total_amount', 'property_tax', 'property_service_charge', 'duhiviet_service_fee', 'grand_total', 'cancellation_policy', 'status'],
            ],
            [
                'name' => 'guest_cancel_reservations',
                'display_name' => Lang::get('admin_messages.guest_cancel_reservations'),
                'title' => ['User id', 'User Name', 'Email Address', 'Mobile number', 'Property id', 'Property name', 'Property star rating', 'Property type', 'Property telephone number', 'Property ext number', 'Property fax number', 'Property address', 'Ward', 'City', 'State', 'Country', 'Postal code', 'Property website', 'Property email', 'booking confirmation code', 'booking made on', 'room category', 'check in date', 'check in time', 'check out date', 'check out time', 'adults', 'children', 'room rate per night', 'total room charges', 'number of extra adults', 'total extra adult charges', 'Meal plan', 'discount amount', 'total amount', 'Property tax', 'Property service charge', 'Duhiviet service fee', 'grand total', 'cancellation policy', 'status'],
                'columns' => ['user_id', 'full_name', 'email_address', 'mobile_number', 'property_id', 'property_name', 'property_star_rating', 'property_type', 'property_telephone_number', 'property_ext_number', 'property_fax_number', 'property_address', 'ward', 'city', 'state', 'country', 'postal_code', 'property_website', 'property_email', 'booking_confirmation_code', 'booking_made_on', 'room_category', 'check_in_date', 'check_in_time', 'check_out_date', 'check_out_time', 'adults', 'children', 'room_rate_per_night', 'total_room_charges', 'number_of_extra_adults', 'total_extra_adult_charges', 'meal_plan', 'discount_amount', 'total_amount', 'property_tax', 'property_service_charge', 'duhiviet_service_fee', 'grand_total', 'cancellation_policy', 'status'],
            ],
            [
                'name' => 'hotel_cancel_reservations',
                'display_name' => Lang::get('admin_messages.hotel_cancel_reservations'),
                'title' => ['User id', 'User Name', 'Email Address', 'Mobile number', 'Property id', 'Property name', 'Property star rating', 'Property type', 'Property telephone number', 'Property ext number', 'Property fax number', 'Property address', 'Ward', 'City', 'State', 'Country', 'Postal code', 'Property website', 'Property email', 'booking confirmation code', 'booking made on', 'room category', 'check in date', 'check in time', 'check out date', 'check out time', 'adults', 'children', 'room rate per night', 'total room charges', 'number of extra adults', 'total extra adult charges', 'Meal plan', 'discount amount', 'total amount', 'Property tax', 'Property service charge', 'Duhiviet service fee', 'grand total', 'cancellation policy', 'status'],
                'columns' => ['user_id', 'full_name', 'email_address', 'mobile_number', 'property_id', 'property_name', 'property_star_rating', 'property_type', 'property_telephone_number', 'property_ext_number', 'property_fax_number', 'property_address', 'ward', 'city', 'state', 'country', 'postal_code', 'property_website', 'property_email', 'booking_confirmation_code', 'booking_made_on', 'room_category', 'check_in_date', 'check_in_time', 'check_out_date', 'check_out_time', 'adults', 'children', 'room_rate_per_night', 'total_room_charges', 'number_of_extra_adults', 'total_extra_adult_charges', 'meal_plan', 'discount_amount', 'total_amount', 'property_tax', 'property_service_charge', 'duhiviet_service_fee', 'grand_total', 'cancellation_policy', 'status'],
            ],
        );
        return view('host.reports.view',$this->view_data);
    }

    /**
     * Fetch Report Data based on given filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Array
     */
    public function fetchReport(Request $request)
    {
        $category = $request->category;
        $filter_text = Lang::get('admin_messages.'.$category).' '.Lang::get('admin_messages.report');

        if($category == 'hotels') {
            $result = \App\Models\Hotel::authUser();
        }

        if($category == 'reservations') {
            $result = \App\Models\Reservation::with('user','hotel')->authUser();
        }
        
        if($category == 'guest_cancel_reservations') {
            $result = \App\Models\Reservation::with('user','hotel')->authUser()->where('status','Cancelled')->where('cancelled_by','Guest');
        }

        if($category == 'hotel_cancel_reservations') {
            $result = \App\Models\Reservation::with('user','hotel')->authUser()->where('status','Cancelled')->where('cancelled_by','Host');
        }

        if($request->filled("from")) {
            $result->where('created_at', '>=', $request->from);
            $filter_text .= ' '.Lang::get('admin_messages.from').' '.getDateInFormat($request->from);
        }

        if($request->filled("to")) {
            $result->where('created_at', '<=', $request->to.' 23:59:00');
            if($request->filled("from")) {
                $filter_text .= ' '.Lang::get('admin_messages.to').' '.getDateInFormat($request->to);
            }
            else {
                $filter_text .= ' '.Lang::get('admin_messages.upto').' '.getDateInFormat($request->to);
            }
        }
        $category = ($category == 'guest_cancel_reservations' || $category == 'hotel_cancel_reservations') ? 'reservations' : $category;
        $map_function = "map".snakeToCamel($category,true)."Data";
        $result = $this->$map_function($result->get());

        return response()->json([
            'status' => true,
            'filter_text' => $filter_text,
            'status_message' => Lang::get('admin_messages.success'),
            'data' => $result,
        ]);
    }

    /**
     * Export Report Data based on given filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Array
     */
    public function exportReport(Request $request)
    {
        $category = $request->category;
        $filter_text = Lang::get('admin_messages.'.$category).' '.Lang::get('admin_messages.report');

        if($category == 'hotels') {
            $result = \App\Models\Hotel::authUser();
        }

        if($category == 'reservations') {
            $result = \App\Models\Reservation::with('user','hotel')->authUser();
        }
        
        if($category == 'guest_cancel_reservations') {
            $result = \App\Models\Reservation::with('user','hotel')->authUser()->where('status','Cancelled')->where('cancelled_by','Guest');
        }

        if($category == 'hotel_cancel_reservations') {
            $result = \App\Models\Reservation::with('user','hotel')->authUser()->where('status','Cancelled')->where('cancelled_by','Host');
        }

        if($request->filled("from")) {
            $result->where('created_at', '>=', $request->from);
            $filter_text .= ' '.Lang::get('admin_messages.from').' '.getDateInFormat($request->from);
        }

        if($request->filled("to")) {
            $result->where('created_at', '<=', $request->to.' 23:59:00');
            if($request->filled("from")) {
                $filter_text .= ' '.Lang::get('admin_messages.to').' '.getDateInFormat($request->to);
            }
            else {
                $filter_text .= ' '.Lang::get('admin_messages.upto').' '.getDateInFormat($request->to);
            }
        }
        $category = ($category == 'guest_cancel_reservations' || $category == 'hotel_cancel_reservations') ? 'reservations' : $category;
        $map_function = "map".snakeToCamel($category,true)."Data";
        $result = $this->$map_function($result->get());

        return \Excel::download(new CollectionExport($result),$request->category . '-report.csv');
    }

    /**
     * Format Hotels Data
     *
     * @param  \Illuminate\Support\Collection  $hotels
     * @return \Illuminate\Support\Collection
     */
    protected function mapHotelsData($hotels)
    {
        return $hotels->map(function($hotel) {
            $hotel_address = $hotel->hotel_address;
            $user = $hotel->user;
            $company = $user->company;
            return [
                'property_id' => $hotel->id,
                'created_on' => $hotel->created_at->format(DATE_FORMAT.' '.TIME_FORMAT),
                'property_name' => $hotel->name,
                'property_star_rating' => $hotel->star_rating,
                'property_type' => $hotel->property_type_name,
                'property_telephone_number' => $hotel->tele_phone_number,
                'property_ext_number' => $hotel->extension_number,
                'property_fax_number' => $hotel->fax_number,
                'property_address' => $hotel_address->address_line_1,
                'ward' => $hotel_address->address_line_2,
                'city' => $hotel_address->city,
                'state' => $hotel_address->state,
                'country' => $hotel_address->country_name,
                'postal_code' => $hotel_address->postal_code,
                'property_website' => $hotel->website,
                'property_email' => $hotel->contact_email,
                'company_id' => optional($company)->user_id ?? '-',
                'company_name' => optional($company)->company_name ?? '-',
                'company_registration_number' => optional($company)->company_tax_number ?? '-',
                'company_telephone_number' => optional($company)->telephone_number ?? '-',
                'company_fax_number' => optional($company)->company_fax_number ?? '-',
                'company_address' => optional($company)->address_line_1 ?? '-',
                'company_ward' => optional($company)->address_line_2 ?? '-',
                'company_city' => optional($company)->city ?? '-',
                'company_state' => optional($company)->state ?? '-',
                'company_country' => optional($company)->country_name ?? '-',
                'company_postal_code' => optional($company)->postal_code ?? '-',
                'company_website' => optional($company)->company_website ?? '-',
                'company_email' => optional($company)->company_email ?? '-',
                'full_name' => $user->full_name,
                'manager_title' => $user->title,
                'manager_email' => $user->email,
                'manager_mobile_number'=> $user->phone_code.' '.$user->phone_number,
                'manager_city' => $user->city != '' ? $user->city : '-',
                'manager_state' => $user->state != '' ? $user->state : '-',
                'manager_country' => $user->country_name ?? '',
                'date_of_birth' => getDateInFormat($user->user_information->dob),
                'gender' => $user->user_information->gender,
            ];
        });
    }

    /**
     * Format Reservations Data
     *
     * @param  \Illuminate\Support\Collection  $reservations
     * @return \Illuminate\Support\Collection
     */
    protected function mapReservationsData($reservations)
    {
        return $reservations->map(function($reservation) {
            $user = $reservation->user;
            $host = $reservation->host;
            $host = $reservation->host;
            $hotel = $reservation->hotel;
            $hotel_address = $hotel->hotel_address;
            $room_reservations = $reservation->room_reservations;
            $adults = $room_reservations->sum('adults');
            $children = $room_reservations->sum('children');
            $total = $room_reservations->sum('total');
            $extra_adult = 0;
            $extra_adult_charge = 0;
            $room_reservation = $room_reservations->first();
            if($adults > $room_reservation->hotel_room->sum('adults')) {
                $extra_adult = $adults - $room_reservation->hotel_room->sum('adults');
                $extra_adult_charge = "0";
            }

            $reservation_status = $reservation->status;
            if($reservation_status == 'Cancelled') {
                $reservation_status = $reservation->cancelled_by == 'Guest' ? 'Cancel by User' : 'Cancel by Property';
            }
            
            return [
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'email_address' => $user->email,
                'mobile_number' => $user->phone_code.' '.$user->phone_number,
                'property_id' => $hotel->id,
                'property_name' => $hotel->name,
                'property_star_rating' => $hotel->star_rating,
                'property_type' => $hotel->property_type_name,
                'property_telephone_number' => $hotel->tele_phone_number,
                'property_ext_number' => $hotel->extension_number,
                'property_fax_number' => $hotel->fax_number,
                'property_address' => $hotel_address->address_line_1,
                'ward' => $hotel_address->address_line_2,
                'city' => $hotel_address->city,
                'state' => $hotel_address->state,
                'country' => $hotel_address->country_name,
                'postal_code' => $hotel_address->postal_code,
                'property_website' => $hotel->website,
                'property_email' => $hotel->contact_email,
                'booking_confirmation_code' => $reservation->code,
                'booking_made_on' => $reservation->created_at->format(DATE_FORMAT.' '.TIME_FORMAT),
                'room_category' => $hotel->property_type_name,
                'check_in_date' => $reservation->formatted_checkin,
                'check_in_time' => $reservation->getTimingText('checkin_at'),
                'check_out_date' => $reservation->formatted_checkout,
                'check_out_time' => $reservation->getTimingText('checkout_at'),
                'adults' => $adults,
                'children' => $children,
                'room_rate_per_night' => numberFormat($total / $reservation->total_nights),
                'total_room_charges' => $reservation->currency_symbol.$reservation->total,
                'number_of_extra_adults' => $extra_adult,
                'total_extra_adult_charges' => $reservation->currency_symbol.$extra_adult_charge,
                'meal_plan' => $room_reservation->sum('meal_plan'),
                'discount_amount' => $reservation->currency_symbol.$reservation->coupon_price,
                'total_amount' => $reservation->currency_symbol.$reservation->sub_total,
                'property_tax' => $reservation->currency_symbol.$reservation->property_tax,
                'property_service_charge' => $reservation->currency_symbol.$reservation->service_charge,
                'duhiviet_service_fee' => $reservation->currency_symbol.$reservation->service_fee,
                'grand_total' => $reservation->currency_symbol.$reservation->total,
                'cancellation_policy' => $reservation->cancellation_policy,
                'property_policy' => $hotel->hotel_policy,
                'status' => $reservation_status,
            ];
        });
    }
}
