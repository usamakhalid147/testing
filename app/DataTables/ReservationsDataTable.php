<?php

/**
 * Reservations Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    ReservationsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Reservation;
use Lang;

class ReservationsDataTable extends DataTable
{
    protected $type;

    /**
     * Set the value for Type
     *
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->addColumn('grand_total',function($query) {
            $total_amount = $query->currency_symbol.' '.$query->total;
            return $total_amount;
        })
        ->addColumn('user_id',function($query){
            return $query->user->id;
        })
        ->addColumn('user_name',function($query){
            return $query->user->first_name.''.$query->user->last_name;
        })
        ->addColumn('property_id',function($query){
            return $query->hotel->id;
        })
        ->addColumn('email_address',function($query){
            return $query->user->email;
        })
        ->addColumn('mobile_number',function($query){
            return $query->user->phone_number;
        })
        ->addColumn('company_id',function($query){
            return optional($query->host_user->company)->id ?? '';
        })
        ->addColumn('company_name',function($query){
            return optional($query->host_user->company)->company_name ?? '';
        })
        ->addColumn('company_tax_number',function($query){
            return optional($query->host_user->company)->company_tax_number ?? '';
        })
        ->addColumn('company_tele_phone_number',function($query){
            return optional($query->host_user->company)->company_tele_phone_number ?? '';
        })
        ->addColumn('company_fax_number',function($query){
            return optional($query->host_user->company)->company_fax_number ?? '';
        })
        ->addColumn('company_address',function($query){
            return optional($query->host_user->company)->address_line_1 ?? '';
        })
        ->addColumn('company_ward',function($query){
            return optional($query->host_user->company)->address_line_2 ?? '';
        })
        ->addColumn('company_city',function($query){
            return optional($query->host_user->company)->city ?? '';
        })
        ->addColumn('company_state',function($query){
            return optional($query->host_user->company)->state ?? '';
        })
        ->addColumn('country',function($query){
            return optional($query->host_user->company)->country_code ?? '';
        })
        ->addColumn('postal_code',function($query){
            return optional($query->host_user->company)->postal_code ?? '';
        })
        ->addColumn('company_website',function($query){
            return optional($query->host_user->company)->company_website ?? '';
        })
        ->addColumn('company_email',function($query){
            return optional($query->host_user->company)->company_email ?? '';
        })
        ->addColumn('property_star_rating',function($query){
            return $query->hotel->star_rating;
        })
        ->addColumn('property_type',function($query){
            return $query->hotel->property_type_name;
        })
        ->addColumn('property_telephone_number',function($query){
            return $query->hotel->tele_phone_number;
        })
        ->addColumn('extension_number',function($query){
            return $query->hotel->extension_number;
        })
        ->addColumn('fax_number',function($query){
            return $query->hotel->fax_number;
        })
        ->addColumn('property_address',function($query){
            return $query->hotel->hotel_address->address_line_1;
        })
        ->addColumn('property_ward',function($query){
            return $query->hotel->hotel_address->address_line_2;
        })
        ->addColumn('property_city',function($query){
            return $query->hotel->hotel_address->city;
        })
        ->addColumn('property_state',function($query){
            return $query->hotel->hotel_address->state;
        })
        ->addColumn('country_code',function($query){
            return $query->hotel->hotel_address->country_code;
        })
        ->addColumn('property_postal_code',function($query){
            return $query->hotel->hotel_address->postal_code;
        })
        ->addColumn('property_website',function($query){
            return $query->hotel->website;
        })
        ->addColumn('property_email',function($query){
            return $query->hotel->email;
        })
        
        ->addColumn('booking_made_on',function($query){
            return getDateInFormat($query->created_at);
        })
        ->addColumn('room_category',function($query){
            return $query->hotel->hotel_rooms->first()->name;

        })
        ->addColumn('room_rate_per_night',function($query){
            return $query->currency_symbol.$query->sub_total/$query->total_nights;
        })
         ->addColumn('sub_total',function($query){
            return $query->currency_symbol.$query->sub_total;
        })
        ->addColumn('adults',function($query){
            return $query->room_reservations->sum('adults');
        })
        ->addColumn('extra_adults',function($query){
            return $query->room_reservations->sum('extra_adults');
        })
        ->addColumn('extra_adults_charges',function($query){
            return $query->currency_symbol.$query->room_reservations->sum('extra_adults_amount');
        })
        ->addColumn('children',function($query){
            return $query->room_reservations->sum('children');
        })
        ->addColumn('extra_childrens',function($query){
            return $query->room_reservations->sum('extra_children');
        })
        ->addColumn('extra_children_charges',function($query){
            return $query->currency_symbol.$query->room_reservations->sum('extra_children_amount');
        })
        ->addColumn('extra_meal',function($query){
            return $query->room_reservations->count('meal_plan');
        })
        ->addColumn('meal_charges',function($query){
            return $query->room_reservations->sum('meal_plan_amount');
        })
        ->addColumn('extra_beds',function($query){
            return $query->room_reservations->count('extra_bed');
        })
        ->addColumn('extra_bed_charges',function($query){
            return $query->room_reservations->sum('extra_bed_amount');
        })
        ->addColumn('offers',function($query){
            return $query->coupon_code;
        })
        ->addColumn('coupon_price',function($query){
            return $query->currency_symbol.$query->coupon_price;
        })
        ->addColumn('payment_method',function($query) {
            if($query->payment_method != '') {
                return Lang::get('admin_messages.'.$query->payment_method);
            }
            return '-';
        })
        ->addColumn('property_tax',function($query){
            return $query->currency_symbol.$query->property_tax;
        })
        ->addColumn('property_service_charge',function($query){
            return $query->currency_symbol.$query->service_charge;
        })
        ->addColumn('service_fee',function($query){
            return $query->currency_symbol.$query->service_fee;
        })
        ->addColumn('total',function($query){
            return $query->currency_symbol.$query->room_reservations->sum('total_price');
        })
       
        ->addColumn('action',function($query) {
            $view = getCurrentUser()->can('view-reservations') ? '<a href="'.route('admin.reservations.show',['id' => $query->id]).'" class="h3"> <i class="fas fa-eye"></i> </a>' : '';
            // $conversation = getCurrentUser()->can('view-reservations') ? '<a href="'.route('admin.reservations.conversation',['reservation' => $query]).'" class="h3"> <i class="fas fa-comment-dots"></i> </a>' : '';
            return $view;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Reservation $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Reservation $model)
    {
        $locale = global_settings('default_language');
        $model = $model
            ->join('hotels', function($join) {
                $join->on('hotels.id', '=', 'reservations.hotel_id');
            })
            ->join('users', function($join) {
                $join->on('users.id', '=', 'reservations.user_id');
            })
            ->join('currencies', function($join) {
                $join->on('currencies.code', '=', 'reservations.currency_code');
            })
            ->join('hotel_addresses',function($join){
                $join->on('hotel_addresses.hotel_id','=','reservations.hotel_id');  
            })
            ->leftjoin('companies',function($join){
                $join->on('companies.user_id','=','reservations.user_id');
            })
            ->leftJoin('users as host_users', function($join) {
                $join->on('host_users.id', '=', 'reservations.host_id');
            })
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(hotels.name, \'$.'.$locale.'\')) as hotel_name, host_users.first_name as host_name, users.first_name as guest_name, reservations.*');

        if($this->type == 'current') {
            $model = $model->where(function($query) {
                $query->where(function($query) {
                    $query->where('reservations.checkin','>=',date('Y-m-d'))->where('reservations.checkout','<=',date('Y-m-d'));
                })
                ->orWhere(function($query) {
                    $query->where('reservations.checkin','<=',date('Y-m-d'))->where('reservations.checkout','>=',date('Y-m-d'));
                });
            })->orderBy('reservations.checkin')->where('reservations.status','Accepted');
        }
        else if($this->type == 'upcoming') {
            $model = $model->where('reservations.checkin','>',date('Y-m-d'))->orderBy('reservations.checkin')->orderBy('reservations.checkin')->where('reservations.status','Accepted');
        }
        else if($this->type == 'completed') {
            $model = $model->where('reservations.checkout','<',date('Y-m-d'))->orderBy('reservations.checkin','DESC')->where('reservations.status','Accepted');
        }
        else if($this->type == 'cancelled') {
            $model = $model->where('reservations.status','Cancelled')->orderBy('reservations.checkin','DESC');
        }
        else {
            $model = $model->orderBy('reservations.id','DESC');
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['exportable' => false])
                    ->orderBy(0)
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            // ['data' => 'id', 'name' => 'reservations.id', 'title' => Lang::get('admin_messages.id')],
            ['data' => 'user_id', 'name' => 'users.id', 'title' => Lang::get('admin_messages.user_id')],
            ['data' => 'user_name','name' => 'users.first_name','title' => Lang::get('admin_messages.full_name')],
            ['data'=>'email_address', 'name'=>'email_address', 'title'=>Lang::get('admin_messages.email_address')],
            ['data'=>'mobile_number','name'=>'mobile_number','title'=>Lang::get('admin_messages.mobile_number')],
            ['data'=>'company_id','name'=>'company_id','title' =>Lang::get('admin_messages.company_id')],
            ['data'=>'company_name','name'=>'company_name','title'=>Lang::get('admin_messages.company_name')],
            ['data'=>'company_tax_number','name'=>'company_tax_number','title'=>Lang::get('admin_messages.company_tax_number')],
            ['data'=>'company_tele_phone_number','name'=>'company_tele_phone_number','title'=>Lang::get('admin_messages.company_tele_phone_number')],
            ['data'=>'company_fax_number','name'=>'company_fax_number','title'=>Lang::get('admin_messages.company_fax_number')],
            ['data'=>'company_address','name'=>'company_address','title'=>Lang::get('admin_messages.company_address')],
            ['data'=>'company_ward','name'=>'company_ward','title'=>Lang::get('admin_messages.ward')],
            ['data'=>'company_city','name'=>'company_city','title'=>Lang::get('admin_messages.cities')],
            ['data'=>'company_state','name'=>'company_state','title'=>Lang::get('admin_messages.province')],
            ['data'=>'country','name'=>'country','title'=>Lang::get('admin_messages.Country')],
            ['data'=>'postal_code','name'=>'postal_code','title'=>Lang::get('admin_messages.postal_code')],
            ['data'=>'company_website','name'=>'company_website','title'=>Lang::get('admin_messages.company_website')],
            ['data'=>'company_email','name'=>'company_email','title'=>Lang::get('admin_messages.company_email')],
            ['data'=>'property_id','name'=>'property_id','title'=>Lang::get('admin_messages.property_id')],
            ['data' => 'hotel_name', 'name' => 'hotels.name', 'title' => Lang::get('admin_messages.hotel_name')],
            ['data'=>'property_star_rating','name'=>'property_star_rating','title'=>Lang::get('admin_messages.property_star_rating')],
            ['data'=>'property_type','name'=>'property_type','title'=>Lang::get('admin_messages.property_type')],
            ['data'=>'property_telephone_number','name'=>'property_telephone_number','title'=>Lang::get('admin_messages.property_telephone_number')],
            ['data'=>'extension_number','name'=>'extension_number','title'=>Lang::get('admin_messages.extension_number')],
            ['data'=>'fax_number','name'=>'fax_number','title'=>Lang::get('admin_messages.fax_number')],
            ['data'=>'property_address','name'=>'property_address','title'=>Lang::get('admin_messages.property_address')],
            ['data'=>'property_ward','name'=>'property_ward','title'=>Lang::get('admin_messages.ward')],
            ['data'=>'property_city','name'=>'property_city','title'=>Lang::get('admin_messages.cities')],
            ['data'=>'property_state','name'=>'property_state','title'=>Lang::get('admin_messages.province')],
            ['data'=>'country_code','name'=>'country_code','title'=>Lang::get('admin_messages.Country')],
            ['data'=>'property_postal_code','name'=>'property_postal_code','title'=>Lang::get('admin_messages.postal_code')],
            ['data'=>'property_website','name'=>'property_website','title'=>Lang::get('admin_messages.property_website')],
            ['data'=>'property_email','name'=>'property_email','title'=>Lang::get('admin_messages.property_email')],
            ['data'=>'code','name'=>'code','title'=>Lang::get('admin_messages.booking_confirmation_code')],
            ['data'=>'booking_made_on','name'=>'booking_made_on','title'=>Lang::get('admin_messages.booking_date')],
            ['data'=>'room_category','name'=>'room_category','title'=>Lang::get('admin_messages.room_category')],
            ['data'=>'checkin','name'=>'checkin_at','title'=>Lang::get('admin_messages.checkin_date')],
            ['data'=>'checkin_at','name'=>'checkin_at','title'=>Lang::get('admin_messages.checkin_time')],
            ['data'=>'checkout','name'=>'checkout','title'=>Lang::get('admin_messages.checkout_date')],
            ['data'=>'checkout_at','name'=>'checkout_at','title'=>Lang::get('admin_messages.checkout_time')],
            ['data'=>'adults','name'=>'adults','title'=>Lang::get('admin_messages.adults')],
            ['data'=>'children','name'=>'children','title'=>Lang::get('admin_messages.childrens')],
            ['data'=>'room_rate_per_night','name'=>'room_rate_per_night','title'=>Lang::get('admin_messages.room_rate_per_night')],
            ['data'=>'total_nights','name'=>'total_nights','title'=>Lang::get('admin_messages.total_room_night')],
            ['data'=>'sub_total','name'=>'sub_total','title'=>Lang::get('admin_messages.room_charges')],
            ['data'=>'extra_adults','name' => 'extra_adults','title' => Lang::get('admin_messages.number_of_extra_adult')],
            ['data'=>'extra_adults_charges','name'=>'extra_adults_charges','title'=>Lang::get('admin_messages.total_extra_adults_charges')],
            ['data'=>'extra_childrens','name' => 'extra_childrens','title' => Lang::get('admin_messages.number_of_extra_children')],
            ['data'=>'extra_children_charges','name'=>'','title' => Lang::get('admin_messages.total_extra_children_charges')],
            ['data'=>'extra_meal','name' => 'extra_meal','title' => Lang::get('admin_messages.number_of_extra_meal')],
            ['data'=>'meal_charges','name' => 'meal_charges','title' => Lang::get('admin_messages.total_meal_charges')],
            ['data'=>'extra_beds','name' => 'extra_beds','title' => Lang::get('admin_messages.number_of_extra_bed')],
            ['data'=>'extra_bed_charges','name' => 'extra_bed_charges','title' => Lang::get('admin_messages.total_extra_bed_charges')],
            ['data'=>'coupon_code','name'=>'coupon_code','title'=>Lang::get('admin_messages.offers')],
            ['data'=>'coupon_price','name'=>'coupon_price','title'=>Lang::get('admin_messages.discount_amount')],
            ['data'=>'payment_method','name'=>'payment_method','title'=>Lang::get('admin_messages.payment_method')],
            ['data' => 'total', 'name' => 'reservations.total', 'title' => Lang::get('admin_messages.total').' '.Lang::get('admin_messages.amount')],
            ['data'=>'property_tax','name'=>'property_tax','title'=>Lang::get('admin_messages.property_tax')],
            ['data'=>'property_service_charge','name'=>'property_service_charge','title'=>Lang::get('admin_messages.property_service_charge')],
            ['data'=>'service_fee','name'=>'service_fee','title'=>Lang::get('admin_messages.service_fees')],
            ['data'=>'grand_total','name' => 'grand_total','title'=>Lang::get('admin_messages.grand_total')],
            ['data' => 'status', 'name' => 'status', 'title' => Lang::get('admin_messages.status')],
        ];
    }

    /**
     * Get builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters()
    {
        return array(
            'dom' => config('datatables-buttons.parameters.dom'),
            'buttons' => config('datatables-buttons.parameters.buttons'),
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'reservations_' . date('YmdHis');
    }
}