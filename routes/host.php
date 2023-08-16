<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Host\{ 
    HomeController,
    RoleController,
    UserController,
    HotelController,
    RoomController,
    ReportController,
    PayoutController,
    ReservationController,
    ReviewController,
    HostCouponCodeController,
    InboxController,
    CalendarController
};

/*
|--------------------------------------------------------------------------
| Hotel Host Routes
|--------------------------------------------------------------------------
|
| Here is where you can register host routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "host" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'guest:host'], function () {
    Route::post('authenticate', [HomeController::class,'authenticate'])->name('authenticate');
    Route::get('login', [HomeController::class,'login'])->name('login');
    Route::view('signup', 'host.signup')->name('signup');
    Route::post('create-host', [HomeController::class,'create'])->name('create_host');
    Route::post('create-host-validation', [HomeController::class,'createHostValidation'])->name('create_host_validation');
    Route::post('authenticate', [HomeController::class,'authenticate'])->name('authenticate');
    Route::match(['GET','POST'],'reset-password', [HomeController::class,'resetPassword'])->name('reset_password');
    Route::post('set-password', [HomeController::class,'setNewPassword'])->name('set_password');
});

Route::group(['middleware' => ['auth:host']], function () {
    Route::delete('/management/profile/image', [HomeController::class,'deleteProfileImage'])->name('delete_profile_image');
    Route::delete('/management/company/image', [HomeController::class,'deleteCompanyImage'])->name('delete_company_image');
    Route::delete('/management/agent/profile/image/{id}', [HomeController::class,'deleteAgentProfileImage'])->name('delete_agent_profile_image');

    Route::get('logout', [HomeController::class,'logout'])->name('logout');
    Route::get('/', function() {
        return redirect()->route('host.dashboard');
    });
    Route::get('management-profile', [HomeController::class,'editProfile'])->name('edit')->middleware('permission:view-edit_profile,host');
    Route::match(['PUT','PATCH'],'{id}', [HomeController::class,'updateProfile'])->name('update')->middleware('permission:update-edit_profile,host');
    Route::get('company-profile', [HomeController::class,'editCompany'])->name('edit_company')->middleware('permission:view-edit_company,host');
    Route::post('update-company', [HomeController::class,'updateCompany'])->name('update_company')->middleware('permission:update-edit_company,host');
    Route::match(['GET','POST'],'dashboard', [HomeController::class,'index'])->name('dashboard');

    // Manage Roles and Permission Routes
    Route::group(['prefix' => 'roles-and-permission'], function () {
        // dd(Auth::user());
        Route::get('/', [RoleController::class,'index'])->name('roles')->middleware('permission:view-host_roles,host');
        Route::get('create', [RoleController::class,'create'])->name('roles.create')->middleware('permission:create-host_roles,host');
        Route::post('/', [RoleController::class,'store'])->name('roles.store')->middleware('permission:create-host_roles,host');
        Route::get('{id}/modify', [RoleController::class,'edit'])->name('roles.edit')->middleware('permission:update-host_roles,host');
        Route::match(['PUT','PATCH'],'{id}', [RoleController::class,'update'])->name('roles.update')->middleware('permission:update-host_roles,host');
        Route::delete('{id}', [RoleController::class,'destroy'])->name('roles.delete')->middleware('permission:delete-host_roles,host');
    });

    // Manage Users Routes
    Route::group(['prefix' => 'agents'], function () {
        Route::get('/', [UserController::class,'index'])->name('users')->middleware('permission:view-host_users,host');
        Route::get('create', [UserController::class,'create'])->name('users.create')->middleware('permission:create-host_users,host');
        Route::post('/', [UserController::class,'store'])->name('users.store')->middleware('permission:create-host_users,host');
        Route::get('{id}/modify', [UserController::class,'edit'])->name('users.edit')->middleware('permission:update-host_users,host');
        Route::match(['PUT','PATCH'],'{id}', [UserController::class,'update'])->name('users.update')->middleware('permission:update-host_users,host');
        Route::delete('{id}', [UserController::class,'destroy'])->name('users.delete')->middleware('permission:delete-host_users,host');
        Route::get('{id}', [UserController::class,'login'])->name('users.login')->middleware('permission:update-host_users,host');
    });

    // Manage Reservation
    Route::group(['prefix' => 'reservations', 'middleware' => 'permission:view-host_reservations,host'], function () {
        Route::get('/{type?}', [ReservationController::class,'index'])->name('reservations');
        Route::get('{id}/view', [ReservationController::class,'show'])->name('reservations.show');
        Route::get('{id}/report', [ReservationController::class,'report'])->name('reservations.report');
        Route::post('{id}/cancel',[ReservationController::class,'cancelReservation'])->name('reservations.cancel');
    });

    // Manage Payouts
    Route::group(['prefix' => 'payouts', 'middleware' => 'permission:view-host_payouts,host'], function () {
        Route::get('/{type?}', [ReservationController::class,'payoutIndex'])->name('payouts');
    });

    // Manage Hotels Routes
    Route::group(['prefix' => 'property-profile'], function () {
        Route::delete('/delete/hotel/property/logo/{id}', [HotelController::class,'delete_hotel_propety_logo'])->name('delete_hotel_propety_logo');
        Route::get('/', [HotelController::class,'index'])->name('hotels')->middleware('permission:view-host_hotels,host');
        Route::get('create', [HotelController::class,'create'])->name('hotels.create')->middleware('permission:create-host_hotels,host');
        Route::post('/', [HotelController::class,'store'])->name('hotels.store')->middleware('permission:create-host_hotels,host');
        Route::get('{id}/modify', [HotelController::class,'edit'])->name('hotels.edit')->middleware('permission:update-host_hotels,host');
        Route::match(['PUT', 'PATCH'],'{id}', [HotelController::class,'update'])->name('hotels.update')->middleware('permission:update-host_hotels,host');
        Route::delete('{id}', [HotelController::class,'destroy'])->name('hotels.delete')->middleware('permission:delete-host_hotels,host');
        Route::post('update_status', [HotelController::class,'updateHotelOptions'])->name('hotels.update_options')->middleware('permission:update-host_hotels,host');
        Route::post('update_photo_order',[HotelController::class,'updatePhotoOrder'])->name('hotels.update_photo_order')->middleware('permission:update-host_hotels,host');
        Route::post('get_price_view',[HotelController::class,'getPriceView'])->name('hotels.get_price_view')->middleware('permission:view-host_hotels,host');
    });

    Route::group(['prefix' => 'rooms-management'], function () {
        Route::get('/{id?}', [RoomController::class,'index'])->name('rooms')->middleware('permission:view-host_rooms,host');
        Route::get('{id}/create', [RoomController::class,'create'])->name('rooms.create')->middleware('permission:create-host_rooms,host');
        Route::post('/', [RoomController::class,'store'])->name('rooms.store')->middleware('permission:create-host_rooms,host');
        Route::get('{id}/modify', [RoomController::class,'edit'])->name('rooms.edit')->middleware('permission:update-host_rooms,host');
        Route::match(['PUT','PATCH'],'{id}', [RoomController::class,'update'])->name('rooms.update')->middleware('permission:update-host_rooms,host');
        Route::delete('{id}', [RoomController::class,'destroy'])->name('rooms.delete')->middleware('permission:delete-host_rooms,host');
        Route::post('admin_status', [RoomController::class,'updateRoomOptions'])->name('rooms.update_options')->middleware('permission:update-host_rooms,host');
        Route::post('update_photo_order',[RoomController::class,'updatePhotoOrder'])->name('rooms.update_room_photo_order')->middleware('permission:update-host_rooms,host');;

        Route::match(['GET','POST'],'calendar/get_events', [CalendarController::class,'getCalendarData'])->name('rooms.get_calendar_data');
        Route::post('update_event', [CalendarController::class,'updateCalendarEvent'])->name('rooms.update_calendar_event')->middleware('permission:update-host_rooms,host');
        Route::get('{room_id}/sync-now', [CalendarController::class,'syncNow'])->name('rooms.sync_calendar')->middleware('permission:update-host_rooms,host');
        Route::post('import-calendar', [CalendarController::class,'importCalendar'])->name('rooms.import_calendar')->middleware('permission:update-host_rooms,host');
        Route::post('remove', [CalendarController::class,'removeCalendar'])->name('rooms.remove_calendar')->middleware('permission:update-host_rooms,host');
    });
    
    // Manage Owner Coupon Code Routes
    Route::group(['prefix' => 'property-discount'], function () {
        Route::get('/', [HostCouponCodeController::class,'index'])->name('coupon_codes')->middleware('permission:view-host_coupon_codes,host');
        Route::get('create', [HostCouponCodeController::class,'create'])->name('coupon_codes.create')->middleware('permission:create-host_coupon_codes,host');
        Route::post('/', [HostCouponCodeController::class,'store'])->name('coupon_codes.store')->middleware('permission:create-host_coupon_codes,host');
        Route::get('{id}/modify', [HostCouponCodeController::class,'edit'])->name('coupon_codes.edit')->middleware('permission:update-host_coupon_codes,host');
        Route::match(['PUT','PATCH'],'{id}', [HostCouponCodeController::class,'update'])->name('coupon_codes.update')->middleware('permission:update-host_coupon_codes,host');
        Route::delete('{id}', [HostCouponCodeController::class,'destroy'])->name('coupon_codes.delete')->middleware('permission:delete-host_coupon_codes,host');
    });

    // Manage Reports Routes
    Route::group(['prefix' => 'reports', 'middleware' => 'permission:view-host_reports,host'], function () {
        Route::get('/', [ReportController::class,'index'])->name('reports');
        Route::post('fetch', [ReportController::class,'fetchReport'])->name('reports.fetch');
        Route::post('export', [ReportController::class,'exportReport'])->name('reports.export');
    });

    // Manage Payout Preference Routes
    Route::group(['prefix' => 'payout-method'], function () {
        Route::get('/', [PayoutController::class,'index'])->name('payout_methods')->middleware('permission:view-payout_methods,host');
        Route::get('create',[PayoutController::class,'create'])->name('payout_methods.create')->middleware('permission:create-payout_methods,host');
        Route::post('store',[PayoutController::class,'store'])->name('payout_methods.store')->middleware('permission:create-payout_methods,host');
        Route::get('update/{id}',[PayoutController::class,'update'])->name('payout_methods.update')->middleware('permission:update-payout_methods,host');
        Route::get('{id}',[PayoutController::class,'destroy'])->name('payout_methods.delete')->middleware('permission:delete-payout_methods,host');
        Route::get('stripe-account/response/{id}',[PayoutController::class,'getResponseStripeExpress'])->name('get_response_stripe_express')->middleware('permission:view-payout_methods,host');
    });

    // Manage Reviews Routes
    Route::group(['prefix' => 'reviews', 'middleware' => 'permission:view-host_reviews,host'], function () {
        Route::get('/', [ReviewController::class,'index'])->name('reviews');
        Route::get('{id}/modify', [ReviewController::class,'edit'])->name('reviews.edit');
        Route::match(['PUT','PATCH'],'{id}', [ReviewController::class,'update'])->name('reviews.update');
    });

    // Manage Messages Routes
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', [InboxController::class,'index'])->name('messages')->middleware('permission:view-inbox,host');
        Route::get('{id}/conversation', [InboxController::class,'conversation'])->name('messages.edit')->middleware('permission:view-inbox,host');
        Route::post('{id}', [InboxController::class,'sendMessage'])->name('messages.update')->middleware('permission:update-inbox,host');
    });

});
