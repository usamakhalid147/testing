<?php

/**
 * Home Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    HomeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\Payout;
use App\Models\UserPenalty;
use App\Models\Country;
use App\Models\Admin;
use App\Models\LoginSlider;
use Carbon\Carbon;
use Lang;
use Auth;

class HomeController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.dashboard');
        $this->view_data['active_menu'] = 'dashboard';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['sliders'] = LoginSlider::activeOnly()->ordered()->get()->pluck('image_src');
        return view('admin.login',$data);
    }

    /**
     * Authenticate admin user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $rules = array(
            'email' => 'required',
            'password' => 'required',
        );

        $attributes = array(
            'email' => Lang::get('messages.fields.email'),
            'password' => Lang::get('messages.fields.password'),
        );

        $this->validate($request,$rules,[],$attributes);

        $remember = ($request->remember == 'on');

        if (Auth::guard('admin')->attempt($request->only('email','password'),$remember)) {
            $admin = Admin::where('email', $request->email)->first();
            
            if(!$admin->status) {
                Auth::guard('admin')->logout();
                flashMessage('danger',Lang::get('admin_messages.failed_to_login'),Lang::get('admin_messages.blocked_by_admin'));
                return redirect()->route('admin.login');
            }

            $intented_url = session('url.intended');
            $has_admin_url = \Str::contains($intented_url,global_settings('admin_url'));
            if($intented_url != '' && $has_admin_url) {
                return redirect($intented_url);
            }

            return redirect()->route('admin.dashboard');
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed_to_login'),Lang::get('admin_messages.invalid_credentials'));
        }
        return redirect()->route('admin.login');
    }

    /**
     * Log out current admin user.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        session()->forget('url.intended');
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    /**
     * Display a Admin Dashboard with total consolidated data
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        if($request->isMethod("POST")) {
            return $this->getDashboardData($request->year);
        }
        $this->view_data['dashboard_data'] = $this->getDashboardData(date('Y'));

        $users = User::latest()->limit(5)->get();
        $this->view_data['recent_users'] = $users->map(function($user) {
            return [
                'id' => $user->id,
                'profile_picture' => $user->profile_picture_src,
                'name' => $user->first_name,
                'email' => $user->email,
            ];
        });

        $transactions = Transaction::latest()->limit(5)->get();
        $this->view_data['recent_transactions'] = $transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'list_type' => Lang::get('messages.'.$transaction->list_type),
                'type' => Lang::get('messages.'.$transaction->type),
                'reservation_id' => $transaction->reservation_id,
                'link' => $transaction->link,
                'link_text' => $transaction->reservation_id,
                'user_id' => $transaction->user_id,
                'user_name' => $transaction->user->first_name,
                'profile_picture' => $transaction->user->profile_picture_src,
                'amount' => $transaction->currency_symbol.''.$transaction->amount,
                'transaction_id' => $transaction->transaction_id,
                'payment_method' => $transaction->payment_method,
            ];
        });

        return view('admin.dashboard',$this->view_data);
    }

    /**
     * Get Dashboard Data for the Given Year
     *
     * @return Array
     */
    public function getDashboardData($year)
    {
        if($year == '') {
            return ['status' => false, 'status_message' => Lang::get('messages.invalid_request')];
        }

        $data = [
            'status' => true,
            'currency_symbol' => session('currency_symbol'),
        ];

        if(date("Y") == $year) {
            $today = now()->format('Y-m-d');
            $today_users = User::whereDate('created_at',$today)->count();
            $today_hotels = Hotel::todayOnly()->count();
            $today_reservations = Reservation::todayOnly()->count();
        }

        $statistics_data = [
            "users" => [
                "count" => User::count(),
                "new" => $today_users ?? 0,
                "colors" => ['#f1f1f1', '#FF9E27'],
                "value" => 100,
            ],
            "hotels" => [
                "count" => Hotel::count(),
                "new" => $today_hotels ?? 0,
                "colors" => ['#f1f1f1', '#944f75'],
                "value" => 100,
            ],
            "reservations" => [
                "count" => Reservation::count(),
                "new" => $today_reservations ?? 0,
                "colors" => ['#f1f1f1', '#6079a4'],
                "value" => 100,
            ],
        ];
        $data['statistics_data'] = $statistics_data;

        $data['total_transactions'] = numberFormat(Transaction::listTypeBased('hotel')->incomeOnly()->get()->sum('amount'));
        $data['paid_out'] = numberFormat(Transaction::listTypeBased('hotel')->typeBased('payout')->get()->sum('amount'));
        $data['refund'] = numberFormat(Transaction::listTypeBased('hotel')->typeBased('refund')->get()->sum('amount'));

        $dateObj = \Carbon\Carbon::createFromFormat('Y',$year);
        $data['line_chart'] = $this->getChartData($dateObj->startOfYear()->format('Y-m-d'),$dateObj->endOfYear()->format('Y-m-d'));

        $data['geo_data'] = Country::join('hotel_addresses', function($join) {
                $join->on('countries.name', '=', 'hotel_addresses.country_code');
            })
            ->groupBy('hotel_addresses.country_code')
            ->select(DB::raw('count(hotel_addresses.hotel_id) as hotel_count, hotel_addresses.country_code,countries.full_name as country_name'))
            ->get();

        $reservations = Reservation::where('status','Accepted')->get();
        $penalties = UserPenalty::get();

        $service_fee = numberFormat($reservations->sum('service_fee'));
        $host_fee = numberFormat($reservations->sum('host_fee'));
        $total = numberFormat($penalties->sum('total'));

        $data['pie_chart'] = array(
            'labels' => ["Service Fee", "Host Fee", "Penalty"],
            'data' => [
                $service_fee,
                $host_fee,
                $total,
            ]
        );
        
        $data['admin_earnings'] = numberFormat($service_fee + $host_fee + $total);
        return $data;
    }

    /**
     * Get Dashboard Chart Data for the Given Range
     *
     * @return Array
     */
    protected function getChartData($start, $end)
    {
        $start = $start." 00:00:00";
        $end = $end." 23:59:59";

        $transactions = Transaction::listTypeBased('hotel')->whereBetween('created_at',[$start,$end])->get();
        
        $chart_array = $labels = [];
        for($month = 1; $month <= 12; $month++) {
            $dateObj = \Carbon\Carbon::createFromFormat('m',$month);
            $labels[] = $dateObj->format('M');
            $start = $dateObj->startOfMonth()->format('Y-m-d').' 00:00:00';
            $end = $dateObj->endOfMonth()->format('Y-m-d').' 23:59:59';

            $chart_array[] = $transactions->whereBetween('created_at', [$start,$end])->sum('amount');
        }
        $line_chart = array(
            'labels' => $labels,
            'amount' => $chart_array
        );

        return $line_chart;
    }

    /**
     * Upload Image to Server
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return ['status' => false];
        }

        $image_handler = resolve('App\Contracts\ImageHandleInterface');
        $image_data['name_prefix'] = 'media_'.rand(100,999);
        $image_data['add_time'] = true;
        $image_data['target_dir'] = "/images/uploads";
        
        $upload_result = $image_handler->upload($request->file('file'),$image_data);
        
        return $upload_result;
    }
}
