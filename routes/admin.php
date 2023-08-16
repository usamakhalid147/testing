<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{ HomeController,
    AdminController,
    RoleController,
    UserController,
    HostController,
    SliderController,
    FeaturedCityController,
    PopularCityController,
	PopularLocalityController,
    PreFooterController,
    CredentialController,
    EmailController,
    GlobalSettingController,
    ThemeSettingController,
    LoginSliderController,
    SocialMediaController,
    MetaController,
    FeeController,
    ReportController,
    TransactionController,
    CouponCodeController,
    CityController,
    CountryController,
    CurrencyController,
    LanguageController,
    BlogCategoryController,
    BlogController,
    HelpCategoryController,
    HelpController,
    StaticPageController,
    StaticPageHeaderController,
    HotelController,
    RoomController,
    CalendarController,
    PropertyTypeController,
    RoomTypeController,
    AmenityTypeController,
    HotelAmenityController,
    RoomAmenityController,
    BedTypeController,
    HotelRuleController,
    MealPlanController,
    GuestAccessController,
    ReviewController,
    ReservationController,
    PayoutController,
    ReferralSettingController,
    TranslationController,
    DiscountBannerController
};

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'guest:admin'], function () {
	Route::get('login', [HomeController::class,'index'])->name('login');
	Route::post('authenticate', [HomeController::class,'authenticate'])->name('authenticate');
});

Route::group(['middleware' => 'auth:admin'], function () {
    Route::get('logout', [HomeController::class,'logout'])->name('logout');
    Route::get('/', function() {
        return redirect()->route('admin.dashboard');
    });
    Route::match(['GET','POST'],'dashboard', [HomeController::class,'dashboard'])->name('dashboard');
    Route::post('upload_image', [HomeController::class,'uploadImage'])->name('upload_image');

    // Manage Admin Users Routes
    Route::group(['prefix' => 'agents'], function () {
    	Route::get('/', [AdminController::class,'index'])->name('admin_users')->middleware('permission:view-admin_users');
    	Route::get('create', [AdminController::class,'create'])->name('admin_users.create')->middleware('permission:create-admin_users');
    	Route::post('/', [AdminController::class,'store'])->name('admin_users.store')->middleware('permission:create-admin_users');
    	Route::get('{id}/modify', [AdminController::class,'edit'])->name('admin_users.edit')->middleware('permission:update-admin_users');
    	Route::match(['PUT','PATCH'],'{id}', [AdminController::class,'update'])->name('admin_users.update')->middleware('permission:update-admin_users');
    	Route::delete('{id}', [AdminController::class,'destroy'])->name('admin_users.delete')->middleware('permission:delete-admin_users');
    });

    // Manage Roles and Permission Routes
    Route::group(['prefix' => 'roles-and-permission'], function () {
        Route::get('/', [RoleController::class,'index'])->name('roles')->middleware('permission:view-roles');
        Route::get('create', [RoleController::class,'create'])->name('roles.create')->middleware('permission:create-roles');
        Route::post('/', [RoleController::class,'store'])->name('roles.store')->middleware('permission:create-roles');
        Route::get('{id}/modify', [RoleController::class,'edit'])->name('roles.edit')->middleware('permission:update-roles');
        Route::match(['PUT','PATCH'],'{id}', [RoleController::class,'update'])->name('roles.update')->middleware('permission:update-roles');
        Route::delete('{id}', [RoleController::class,'destroy'])->name('roles.delete')->middleware('permission:delete-roles');
    });

    // Manage Users Routes
    Route::group(['prefix' => 'manage-users'], function () {
        Route::get('/', [UserController::class,'index'])->name('users')->middleware('permission:view-users');
        Route::get('/host', [UserController::class,'hostIndex'])->name('users.host')->middleware('permission:view-users');
        Route::get('create', [UserController::class,'create'])->name('users.create')->middleware('permission:create-users');
        Route::post('/', [UserController::class,'store'])->name('users.store')->middleware('permission:create-users');
        Route::get('{id}/modify', [UserController::class,'edit'])->name('users.edit')->middleware('permission:update-users');
        Route::match(['PUT','PATCH'],'{id}', [UserController::class,'update'])->name('users.update')->middleware('permission:update-users');
        Route::delete('{id}', [UserController::class,'destroy'])->name('users.delete')->middleware('permission:delete-users');
        Route::get('{id}', [UserController::class,'login'])->name('users.login')->middleware('permission:update-users');
    });

    Route::group(['prefix' => 'manage-hoteliers'], function () {
        Route::get('/', [HostController::class,'index'])->name('hosts')->middleware('permission:view-hosts');
        Route::get('create', [HostController::class,'create'])->name('hosts.create')->middleware('permission:create-hosts');
        Route::post('/', [HostController::class,'store'])->name('hosts.store')->middleware('permission:create-hosts');
        Route::get('{id}/modify', [HostController::class,'edit'])->name('hosts.edit')->middleware('permission:update-hosts');
        Route::match(['PUT','PATCH'],'{id}', [HostController::class,'update'])->name('hosts.update')->middleware('permission:update-hosts');
        Route::delete('{id}', [HostController::class,'destroy'])->name('hosts.delete')->middleware('permission:delete-hosts');
        Route::get('{id}', [HostController::class,'login'])->name('hosts.login')->middleware('permission:update-hosts');
    });

    // Manage Sliders Routes
    Route::group(['prefix' => 'slider'], function () {
        Route::get('/', [SliderController::class,'index'])->name('sliders')->middleware('permission:view-sliders');
        Route::get('create', [SliderController::class,'create'])->name('sliders.create')->middleware('permission:create-sliders');
        Route::post('/', [SliderController::class,'store'])->name('sliders.store')->middleware('permission:create-sliders');
        Route::get('{id}/modify', [SliderController::class,'edit'])->name('sliders.edit')->middleware('permission:update-sliders');
        Route::match(['PUT','PATCH'],'{id}', [SliderController::class,'update'])->name('sliders.update')->middleware('permission:update-sliders');
        Route::delete('{id}', [SliderController::class,'destroy'])->name('sliders.delete')->middleware('permission:delete-sliders');
    });

    // Manage Featured Cities Routes
    Route::group(['prefix' => 'featured-cities'], function () {
        Route::get('/', [FeaturedCityController::class,'index'])->name('featured_cities')->middleware('permission:view-featured_cities');
        Route::get('create', [FeaturedCityController::class,'create'])->name('featured_cities.create')->middleware('permission:create-featured_cities');
        Route::post('/', [FeaturedCityController::class,'store'])->name('featured_cities.store')->middleware('permission:create-featured_cities');
        Route::get('{id}/modify', [FeaturedCityController::class,'edit'])->name('featured_cities.edit')->middleware('permission:update-featured_cities');
        Route::match(['PUT','PATCH'],'{id}', [FeaturedCityController::class,'update'])->name('featured_cities.update')->middleware('permission:update-featured_cities');
        Route::delete('{id}', [FeaturedCityController::class,'destroy'])->name('featured_cities.delete')->middleware('permission:delete-featured_cities');
    });

    // Manage Pre Footer Routes
    Route::group(['prefix' => 'pre-footers'], function () {
        Route::get('/', [PreFooterController::class,'index'])->name('pre_footers')->middleware('permission:view-pre_footers');
        Route::get('create', [PreFooterController::class,'create'])->name('pre_footers.create')->middleware('permission:create-pre_footers');
        Route::post('/', [PreFooterController::class,'store'])->name('pre_footers.store')->middleware('permission:create-pre_footers');
        Route::get('{id}/modify', [PreFooterController::class,'edit'])->name('pre_footers.edit')->middleware('permission:update-pre_footers');
        Route::match(['PUT','PATCH'],'{id}', [PreFooterController::class,'update'])->name('pre_footers.update')->middleware('permission:update-pre_footers');
        Route::delete('{id}', [PreFooterController::class,'destroy'])->name('pre_footers.delete')->middleware('permission:delete-pre_footers');
    });

    // Manage Popular Cities Routes
    Route::group(['prefix' => 'popular-cities'], function () {
        Route::get('/', [PopularCityController::class,'index'])->name('popular_cities')->middleware('permission:view-popular_cities');
        Route::get('create', [PopularCityController::class,'create'])->name('popular_cities.create')->middleware('permission:create-popular_cities');
        Route::post('/', [PopularCityController::class,'store'])->name('popular_cities.store')->middleware('permission:create-popular_cities');
        Route::get('{id}/modify', [PopularCityController::class,'edit'])->name('popular_cities.edit')->middleware('permission:update-popular_cities');
        Route::match(['PUT','PATCH'],'{id}', [PopularCityController::class,'update'])->name('popular_cities.update')->middleware('permission:update-popular_cities');
        Route::delete('{id}', [PopularCityController::class,'destroy'])->name('popular_cities.delete')->middleware('permission:delete-popular_cities');
    });

    // Manage Popular Localities Routes
    Route::group(['prefix' => 'popular-localities'], function () {
        Route::get('/', [PopularLocalityController::class,'index'])->name('popular_localities')->middleware('permission:view-popular_localities');
        Route::get('create', [PopularLocalityController::class,'create'])->name('popular_localities.create')->middleware('permission:create-popular_localities');
        Route::post('/', [PopularLocalityController::class,'store'])->name('popular_localities.store')->middleware('permission:create-popular_localities');
        Route::get('{id}/modify', [PopularLocalityController::class,'edit'])->name('popular_localities.edit')->middleware('permission:update-popular_localities');
        Route::match(['PUT','PATCH'],'{id}', [PopularLocalityController::class,'update'])->name('popular_localities.update')->middleware('permission:update-popular_localities');
        Route::delete('{id}', [PopularLocalityController::class,'destroy'])->name('popular_localities.delete')->middleware('permission:delete-popular_localities');
    });

    // Manage API Credentails Routes
    Route::group(['prefix' => 'api-setting'], function () {
        Route::get('/', [CredentialController::class,'index'])->name('api_credentials')->middleware('permission:view-api_credentials');
        Route::match(['PUT','PATCH'],'/', [CredentialController::class,'update'])->name('api_credentials.update')->middleware('permission:update-api_credentials');
    });

    // Manage Payment Gateway Routes
    Route::group(['prefix' => 'payment-setting'], function () {
        Route::get('/', [CredentialController::class,'index'])->name('payment_gateways')->middleware('permission:view-payment_gateways');
        Route::match(['PUT','PATCH'],'/', [CredentialController::class,'paymentUpdate'])->name('payment_gateways.update')->middleware('permission:update-payment_gateways');
    });

    // Manage Email Configuration Routes
    Route::group(['prefix' => 'email-setting'], function () {
        Route::get('/', [EmailController::class,'index'])->name('email_configurations')->middleware('permission:view-email_configurations');
        Route::match(['PUT','PATCH'],'/', [EmailController::class,'update'])->name('email_configurations.update')->middleware('permission:update-email_configurations');
    });

    // Manage Global Settings Routes
    Route::group(['prefix' => 'site-management'], function () {
        Route::get('/', [GlobalSettingController::class,'index'])->name('global_settings')->middleware('permission:view-global_settings');
        Route::match(['PUT','PATCH'],'/', [GlobalSettingController::class,'update'])->name('global_settings.update')->middleware('permission:update-global_settings');
    });

    // Manage Theme Settings Routes
    Route::group(['prefix' => 'theme-settings'], function () {
        Route::get('/', [ThemeSettingController::class,'index'])->name('theme_settings')->middleware('permission:view-theme_settings');
        Route::match(['PUT','PATCH'],'/', [ThemeSettingController::class,'update'])->name('theme_settings.update')->middleware('permission:update-theme_settings');
    });

    // Manage Login Sliders Routes
    Route::group(['prefix' => 'login-page-sliders'], function () {
        Route::get('/', [LoginSliderController::class,'index'])->name('login_sliders')->middleware('permission:view-login_sliders');
        Route::get('create', [LoginSliderController::class,'create'])->name('login_sliders.create')->middleware('permission:create-login_sliders');
        Route::post('/', [LoginSliderController::class,'store'])->name('login_sliders.store')->middleware('permission:create-login_sliders');
        Route::get('{id}/modify', [LoginSliderController::class,'edit'])->name('login_sliders.edit')->middleware('permission:update-login_sliders');
        Route::match(['PUT','PATCH'],'{id}', [LoginSliderController::class,'update'])->name('login_sliders.update')->middleware('permission:update-login_sliders');
        Route::delete('{id}', [LoginSliderController::class,'destroy'])->name('login_sliders.delete')->middleware('permission:delete-login_sliders');
    });

    // Manage Social Media Links Routes
    Route::group(['prefix' => 'social-media'], function () {
        Route::get('/', [SocialMediaController::class,'index'])->name('social_media_links')->middleware('permission:view-social_media_links');
        Route::match(['PUT','PATCH'],'/', [SocialMediaController::class,'update'])->name('social_media_links.update')->middleware('permission:update-social_media_links');
    });

    // Manage Meta Routes
    Route::group(['prefix' => 'meta-settings'], function () {
        Route::get('/', [MetaController::class,'index'])->name('metas')->middleware('permission:view-metas');
        Route::get('{id}/modify', [MetaController::class,'edit'])->name('metas.edit')->middleware('permission:update-metas');
        Route::match(['PUT','PATCH'],'{id}', [MetaController::class,'update'])->name('metas.update')->middleware('permission:update-metas');
    });

    // Manage Fees Routes
    Route::group(['prefix' => 'fees-and-charges'], function () {
        Route::get('/', [FeeController::class,'index'])->name('fees')->middleware('permission:view-fees');
        Route::match(['PUT','PATCH'],'/', [FeeController::class,'update'])->name('fees.update')->middleware('permission:update-fees');
    });

    // Manage Email to Users Routes
    Route::group(['prefix' => 'email-to-users'], function () {
        Route::match(array('GET','POST'),'/', [EmailController::class,'sendMailToUsers'])->name('email_to_users')->middleware('permission:view-email_to_users');
    });

    // Manage Reports Routes
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [ReportController::class,'index'])->name('reports')->middleware('permission:view-reports');
        Route::post('fetch', [ReportController::class,'fetchReport'])->name('reports.fetch')->middleware('permission:view-reports');
        Route::post('export', [ReportController::class,'exportReport'])->name('reports.export')->middleware('permission:view-reports');
    });

    // Manage Transaction Routes
    Route::group(['prefix' => 'transaction-history'], function () {
        Route::get('/', [TransactionController::class,'index'])->name('transactions')->middleware('permission:view-transactions');
    });

    // Manage Coupon Code Routes
    Route::group(['prefix' => 'property-discount-settings'], function () {
        Route::get('/', [CouponCodeController::class,'index'])->name('coupon_codes')->middleware('permission:view-coupon_codes');
        Route::get('create', [CouponCodeController::class,'create'])->name('coupon_codes.create')->middleware('permission:create-coupon_codes');
        Route::post('/', [CouponCodeController::class,'store'])->name('coupon_codes.store')->middleware('permission:create-coupon_codes');
        Route::get('{id}/modify', [CouponCodeController::class,'edit'])->name('coupon_codes.edit')->middleware('permission:update-coupon_codes');
        Route::match(['PUT','PATCH'],'{id}', [CouponCodeController::class,'update'])->name('coupon_codes.update')->middleware('permission:update-coupon_codes');
        Route::delete('{id}', [CouponCodeController::class,'destroy'])->name('coupon_codes.delete')->middleware('permission:delete-coupon_codes');
    });

    // Manage Cities Routes
    Route::group(['prefix' => 'city-settings'], function () {
        Route::get('/', [CityController::class,'index'])->name('cities')->middleware('permission:view-cities');
        Route::get('create', [CityController::class,'create'])->name('cities.create')->middleware('permission:create-cities');
        Route::post('/', [CityController::class,'store'])->name('cities.store')->middleware('permission:create-cities');
        Route::get('{id}/modify', [CityController::class,'edit'])->name('cities.edit')->middleware('permission:update-cities');
        Route::match(['PUT','PATCH'],'{id}', [CityController::class,'update'])->name('cities.update')->middleware('permission:update-cities');
        Route::delete('{id}', [CityController::class,'destroy'])->name('cities.delete')->middleware('permission:delete-cities');
    });

    // Manage Countries Routes
    Route::group(['prefix' => 'country-setting'], function () {
        Route::get('/', [CountryController::class,'index'])->name('countries')->middleware('permission:view-countries');
        Route::get('create', [CountryController::class,'create'])->name('countries.create')->middleware('permission:create-countries');
        Route::post('/', [CountryController::class,'store'])->name('countries.store')->middleware('permission:create-countries');
        Route::get('{id}/modify', [CountryController::class,'edit'])->name('countries.edit')->middleware('permission:update-countries');
        Route::match(['PUT','PATCH'],'{id}', [CountryController::class,'update'])->name('countries.update')->middleware('permission:update-countries');
        Route::delete('{id}', [CountryController::class,'destroy'])->name('countries.delete')->middleware('permission:delete-countries');
    });

    // Manage Currencies Routes
    Route::group(['prefix' => 'currency-setting'], function () {
        Route::get('/', [CurrencyController::class,'index'])->name('currencies')->middleware('permission:view-currencies');
        Route::get('create', [CurrencyController::class,'create'])->name('currencies.create')->middleware('permission:create-currencies');
        Route::post('/', [CurrencyController::class,'store'])->name('currencies.store')->middleware('permission:create-currencies');
        Route::get('{id}/modify', [CurrencyController::class,'edit'])->name('currencies.edit')->middleware('permission:update-currencies');
        Route::match(['PUT','PATCH'],'{id}', [CurrencyController::class,'update'])->name('currencies.update')->middleware('permission:update-currencies');
        Route::delete('{id}', [CurrencyController::class,'destroy'])->name('currencies.delete')->middleware('permission:delete-currencies');
    });

    // Manage Languages Routes
    Route::group(['prefix' => 'languages'], function () {
        Route::get('/', [LanguageController::class,'index'])->name('languages')->middleware('permission:view-languages');
        Route::get('create', [LanguageController::class,'create'])->name('languages.create')->middleware('permission:create-languages');
        Route::post('/', [LanguageController::class,'store'])->name('languages.store')->middleware('permission:create-languages');
        Route::get('{id}/modify', [LanguageController::class,'edit'])->name('languages.edit')->middleware('permission:update-languages');
        Route::match(['PUT','PATCH'],'{id}', [LanguageController::class,'update'])->name('languages.update')->middleware('permission:update-languages');
        Route::delete('{id}', [LanguageController::class,'destroy'])->name('languages.delete')->middleware('permission:delete-languages');
    });

    // Manage Blog Category Routes
    Route::group(['prefix' => 'blog-categories'], function () {
        Route::get('/', [BlogCategoryController::class,'index'])->name('blog_categories')->middleware('permission:view-blog_categories');
        Route::get('create', [BlogCategoryController::class,'create'])->name('blog_categories.create')->middleware('permission:create-blog_categories');
        Route::post('/', [BlogCategoryController::class,'store'])->name('blog_categories.store')->middleware('permission:create-blog_categories');
        Route::get('{id}/modify', [BlogCategoryController::class,'edit'])->name('blog_categories.edit')->middleware('permission:update-blog_categories');
        Route::match(['PUT','PATCH'],'{id}', [BlogCategoryController::class,'update'])->name('blog_categories.update')->middleware('permission:update-blog_categories');
        Route::delete('{id}', [BlogCategoryController::class,'destroy'])->name('blog_categories.delete')->middleware('permission:delete-blog_categories');
    });

    // Manage Blog Routes
    Route::group(['prefix' => 'blogs'], function () {
        Route::get('/', [BlogController::class,'index'])->name('blogs')->middleware('permission:view-blogs');
        Route::get('create', [BlogController::class,'create'])->name('blogs.create')->middleware('permission:create-blogs');
        Route::post('/', [BlogController::class,'store'])->name('blogs.store')->middleware('permission:create-blogs');
        Route::get('{id}/modify', [BlogController::class,'edit'])->name('blogs.edit')->middleware('permission:update-blogs');
        Route::match(['PUT','PATCH'],'{id}', [BlogController::class,'update'])->name('blogs.update')->middleware('permission:update-blogs');
        Route::delete('{id}', [BlogController::class,'destroy'])->name('blogs.delete')->middleware('permission:delete-blogs');
    });

    // Manage Help Category Routes
    Route::group(['prefix' => 'help-categories'], function () {
        Route::get('/', [HelpCategoryController::class,'index'])->name('help_categories')->middleware('permission:view-help_categories');
        Route::get('create', [HelpCategoryController::class,'create'])->name('help_categories.create')->middleware('permission:create-help_categories');
        Route::post('/', [HelpCategoryController::class,'store'])->name('help_categories.store')->middleware('permission:create-help_categories');
        Route::get('{id}/modify', [HelpCategoryController::class,'edit'])->name('help_categories.edit')->middleware('permission:update-help_categories');
        Route::match(['PUT','PATCH'],'{id}', [HelpCategoryController::class,'update'])->name('help_categories.update')->middleware('permission:update-help_categories');
        Route::delete('{id}', [HelpCategoryController::class,'destroy'])->name('help_categories.delete')->middleware('permission:delete-help_categories');
    });

    // Manage Help Routes
    Route::group(['prefix' => 'help'], function () {
        Route::get('/', [HelpController::class,'index'])->name('helps')->middleware('permission:view-helps');
        Route::get('create', [HelpController::class,'create'])->name('helps.create')->middleware('permission:create-helps');
        Route::post('/', [HelpController::class,'store'])->name('helps.store')->middleware('permission:create-helps');
        Route::get('{id}/modify', [HelpController::class,'edit'])->name('helps.edit')->middleware('permission:update-helps');
        Route::match(['PUT','PATCH'],'{id}', [HelpController::class,'update'])->name('helps.update')->middleware('permission:update-helps');
        Route::delete('{id}', [HelpController::class,'destroy'])->name('helps.delete')->middleware('permission:delete-helps');
    });

    // Manage Static Pages Routes
    Route::group(['prefix' => 'static-pages'], function () {
        Route::get('/', [StaticPageController::class,'index'])->name('static_pages')->middleware('permission:view-static_pages');
        Route::get('create', [StaticPageController::class,'create'])->name('static_pages.create')->middleware('permission:create-static_pages');
        Route::post('/', [StaticPageController::class,'store'])->name('static_pages.store')->middleware('permission:create-static_pages');
        Route::get('{id}/modify', [StaticPageController::class,'edit'])->name('static_pages.edit')->middleware('permission:update-static_pages');
        Route::match(['PUT','PATCH'],'{id}', [StaticPageController::class,'update'])->name('static_pages.update')->middleware('permission:update-static_pages');
        Route::delete('{id}', [StaticPageController::class,'destroy'])->name('static_pages.delete')->middleware('permission:delete-static_pages');
    });

    // Manage Static Pages Header Routes
    Route::group(['prefix' => 'static-page-header'], function () {
        Route::get('/', [StaticPageHeaderController::class,'index'])->name('static_page_header')->middleware('permission:view-static_page_header');
        Route::get('/modify', [StaticPageHeaderController::class,'edit'])->name('static_page_header.edit')->middleware('permission:update-static_page_header');
        Route::match(['PUT','PATCH'],'/update', [StaticPageHeaderController::class,'update'])->name('static_page_header.update')->middleware('permission:update-static_page_header');
    });

    // Manage Reservation
    Route::group(['prefix' => 'reservation', 'middleware' => 'permission:view-reservations'], function () {
        Route::get('/{type?}', [ReservationController::class,'index'])->name('reservations');
        Route::get('{id}/view', [ReservationController::class,'show'])->name('reservations.show');
        Route::get('{reservation}/conversation', [ReservationController::class,'conversation'])->name('reservations.conversation');
    });

    // Manage Penalties
    Route::group(['prefix' => 'hotelier-penalties', 'middleware' => 'permission:view-penalties'], function () {
        Route::get('/', [ReservationController::class,'penaltyIndex'])->name('penalties');
        Route::get('{user_penalty}/view', [ReservationController::class,'userPenalty'])->name('penalties.show');
    });

    // Manage Payouts
    Route::group(['prefix' => 'payout', 'middleware' => 'permission:view-payouts'], function () {
        Route::get('/{type?}', [PayoutController::class,'index'])->name('payouts');
        Route::post('process-payout/{id}', [PayoutController::class,'processPayout'])->name('process_payout');
    });

    // Manage Hotels Routes
    Route::group(['prefix' => 'property-management'], function () {
        Route::delete('/delete/hotel/property/logo/{id}', [HotelController::class,'delete_hotel_propety_logo'])->name('delete_hotel_propety_logo');
        Route::get('/', [HotelController::class,'index'])->name('hotels')->middleware('permission:view-hotels');
        Route::get('create', [HotelController::class,'create'])->name('hotels.create')->middleware('permission:create-hotels');
        Route::post('/', [HotelController::class,'store'])->name('hotels.store')->middleware('permission:create-hotels');
        Route::get('{id}/modify', [HotelController::class,'edit'])->name('hotels.edit')->middleware('permission:update-hotels');
        Route::match(['PUT','PATCH'],'{id}', [HotelController::class,'update'])->name('hotels.update')->middleware('permission:update-hotels');
        Route::delete('{id}', [HotelController::class,'destroy'])->name('hotels.delete')->middleware('permission:delete-hotels');
        Route::post('admin_status', [HotelController::class,'updateHotelOptions'])->name('hotels.update_options');
        Route::post('update_photo_order/{id}', [HotelController::class,'updatePhotoOrder'])->name('hotels.update_photo_order')->middleware('permission:update-hotels');

        Route::get('{id}/rooms', [HotelController::class,'manageRooms'])->name('hotels.rooms');
    });

	Route::group(['prefix' => 'rooms'], function () {
        Route::get('/{id?}', [RoomController::class,'index'])->name('rooms')->middleware('permission:view-rooms');
        Route::get('{id}/create', [RoomController::class,'create'])->name('rooms.create')->middleware('permission:create-rooms');
        Route::post('/', [RoomController::class,'store'])->name('rooms.store')->middleware('permission:create-rooms');
        Route::get('{id}/modify', [RoomController::class,'edit'])->name('rooms.edit')->middleware('permission:update-rooms');
        Route::match(['PUT', 'PATCH'],'{id}', [RoomController::class,'update'])->name('rooms.update')->middleware('permission:update-rooms');
        Route::delete('{id}', [RoomController::class,'destroy'])->name('rooms.delete')->middleware('permission:delete-rooms');
        Route::post('admin_status', [RoomController::class,'updateRoomOptions'])->name('rooms.update_options');
        Route::post('update_photo_order',[RoomController::class,'updatePhotoOrder'])->name('rooms.update_room_photo_order')->middleware('permission:update-rooms');

        Route::match(['GET','POST'],'calendar/get_events', [CalendarController::class,'getCalendarData'])->name('rooms.get_calendar_data');
        Route::post('update_event', [CalendarController::class,'updateCalendarEvent'])->name('rooms.update_calendar_event')->middleware('permission:update-rooms');
        Route::get('{room_id}/sync-now', [CalendarController::class,'syncNow'])->name('rooms.sync_calendar')->middleware('permission:update-rooms');
        Route::post('import-calendar', [CalendarController::class,'importCalendar'])->name('rooms.import_calendar')->middleware('permission:update-rooms');
        Route::post('remove', [CalendarController::class,'removeCalendar'])->name('rooms.remove_calendar')->middleware('permission:update-rooms');
    });

    // Manage Property Types Routes
    Route::group(['prefix' => 'property-type'], function () {
        Route::get('/', [PropertyTypeController::class,'index'])->name('property_types')->middleware('permission:view-property_types');
        Route::get('create', [PropertyTypeController::class,'create'])->name('property_types.create')->middleware('permission:create-property_types');
        Route::post('/', [PropertyTypeController::class,'store'])->name('property_types.store')->middleware('permission:create-property_types');
        Route::get('{id}/modify', [PropertyTypeController::class,'edit'])->name('property_types.edit')->middleware('permission:update-property_types');
        Route::match(['PUT','PATCH'],'{id}', [PropertyTypeController::class,'update'])->name('property_types.update')->middleware('permission:update-property_types');
        Route::delete('{id}', [PropertyTypeController::class,'destroy'])->name('property_types.delete')->middleware('permission:delete-property_types');
    });

    // Manage Room Types Routes
    Route::group(['prefix' => 'room-type'], function () {
        Route::get('/', [RoomTypeController::class,'index'])->name('room_types')->middleware('permission:view-room_types');
        Route::get('create', [RoomTypeController::class,'create'])->name('room_types.create')->middleware('permission:create-room_types');
        Route::post('/', [RoomTypeController::class,'store'])->name('room_types.store')->middleware('permission:create-room_types');
        Route::get('{id}/modify', [RoomTypeController::class,'edit'])->name('room_types.edit')->middleware('permission:update-room_types');
        Route::match(['PUT','PATCH'],'{id}', [RoomTypeController::class,'update'])->name('room_types.update')->middleware('permission:update-room_types');
        Route::delete('{id}', [RoomTypeController::class,'destroy'])->name('room_types.delete')->middleware('permission:delete-room_types');
    });

    // Manage Amenity Types Routes
    Route::group(['prefix' => 'facilities-and-services'], function () {
        Route::get('/', [AmenityTypeController::class,'index'])->name('amenity_types')->middleware('permission:view-amenity_types');
        Route::get('create', [AmenityTypeController::class,'create'])->name('amenity_types.create')->middleware('permission:create-amenity_types');
        Route::post('/', [AmenityTypeController::class,'store'])->name('amenity_types.store')->middleware('permission:create-amenity_types');
        Route::get('{id}/modify', [AmenityTypeController::class,'edit'])->name('amenity_types.edit')->middleware('permission:update-amenity_types');
        Route::match(['PUT','PATCH'],'{id}', [AmenityTypeController::class,'update'])->name('amenity_types.update')->middleware('permission:update-amenity_types');
        Route::delete('{id}', [AmenityTypeController::class,'destroy'])->name('amenity_types.delete')->middleware('permission:delete-amenity_types');
    });

    // Manage Hotel Amenities Routes
    Route::group(['prefix' => 'property-facilities-and-services'], function () {
        Route::get('/', [HotelAmenityController::class,'index'])->name('hotel_amenities')->middleware('permission:view-hotel_amenities');
        Route::get('create', [HotelAmenityController::class,'create'])->name('hotel_amenities.create')->middleware('permission:create-hotel_amenities');
        Route::post('/', [HotelAmenityController::class,'store'])->name('hotel_amenities.store')->middleware('permission:create-hotel_amenities');
        Route::get('{id}/modify', [HotelAmenityController::class,'edit'])->name('hotel_amenities.edit')->middleware('permission:update-hotel_amenities');
        Route::match(['PUT','PATCH'],'{id}', [HotelAmenityController::class,'update'])->name('hotel_amenities.update')->middleware('permission:update-hotel_amenities');
        Route::delete('{id}', [HotelAmenityController::class,'destroy'])->name('hotel_amenities.delete')->middleware('permission:delete-hotel_amenities');
    });

    // Manage Room Amenities Routes
    Route::group(['prefix' => 'room-facilities-and-services'], function () {
        Route::get('/', [RoomAmenityController::class,'index'])->name('room_amenities')->middleware('permission:view-room_amenities');
        Route::get('create', [RoomAmenityController::class,'create'])->name('room_amenities.create')->middleware('permission:create-room_amenities');
        Route::post('/', [RoomAmenityController::class,'store'])->name('room_amenities.store')->middleware('permission:create-room_amenities');
        Route::get('{id}/modify', [RoomAmenityController::class,'edit'])->name('room_amenities.edit')->middleware('permission:update-room_amenities');
        Route::match(['PUT','PATCH'],'{id}', [RoomAmenityController::class,'update'])->name('room_amenities.update')->middleware('permission:update-room_amenities');
        Route::delete('{id}', [RoomAmenityController::class,'destroy'])->name('room_amenities.delete')->middleware('permission:delete-room_amenities');
    });

    // Manage Bed Types Routes
    Route::group(['prefix' => 'bed-type'], function () {
        Route::get('/', [BedTypeController::class,'index'])->name('bed_types')->middleware('permission:view-bed_types');
        Route::get('create', [BedTypeController::class,'create'])->name('bed_types.create')->middleware('permission:create-bed_types');
        Route::post('/', [BedTypeController::class,'store'])->name('bed_types.store')->middleware('permission:create-bed_types');
        Route::get('{id}/modify', [BedTypeController::class,'edit'])->name('bed_types.edit')->middleware('permission:update-bed_types');
        Route::match(['PUT','PATCH'],'{id}', [BedTypeController::class,'update'])->name('bed_types.update')->middleware('permission:update-bed_types');
        Route::delete('{id}', [BedTypeController::class,'destroy'])->name('bed_types.delete')->middleware('permission:delete-bed_types');
    });

    // Manage Hotel Rules Routes
    Route::group(['prefix' => 'property-rules-and-regulations'], function () {
        Route::get('/', [HotelRuleController::class,'index'])->name('hotel_rules')->middleware('permission:view-hotel_rules');
        Route::get('create', [HotelRuleController::class,'create'])->name('hotel_rules.create')->middleware('permission:create-hotel_rules');
        Route::post('/', [HotelRuleController::class,'store'])->name('hotel_rules.store')->middleware('permission:create-hotel_rules');
        Route::get('{id}/modify', [HotelRuleController::class,'edit'])->name('hotel_rules.edit')->middleware('permission:update-hotel_rules');
        Route::match(['PUT','PATCH'],'{id}', [HotelRuleController::class,'update'])->name('hotel_rules.update')->middleware('permission:update-hotel_rules');
        Route::delete('{id}', [HotelRuleController::class,'destroy'])->name('hotel_rules.delete')->middleware('permission:delete-hotel_rules');
    });

    // Manage Meal Plan Routes
    Route::group(['prefix' => 'property-meal-plan'], function () {
        Route::get('/', [MealPlanController::class,'index'])->name('meal_plans')->middleware('permission:view-meal_plans');
        Route::get('create', [MealPlanController::class,'create'])->name('meal_plans.create')->middleware('permission:create-meal_plans');
        Route::post('/', [MealPlanController::class,'store'])->name('meal_plans.store')->middleware('permission:create-meal_plans');
        Route::get('{id}/modify', [MealPlanController::class,'edit'])->name('meal_plans.edit')->middleware('permission:update-meal_plans');
        Route::match(['PUT','PATCH'],'{id}', [MealPlanController::class,'update'])->name('meal_plans.update')->middleware('permission:update-meal_plans');
        Route::delete('{id}', [MealPlanController::class,'destroy'])->name('meal_plans.delete')->middleware('permission:delete-meal_plans');
    });

    // Manage Guest Accesses Routes
    Route::group(['prefix' => 'guest-access'], function () {
        Route::get('/', [GuestAccessController::class,'index'])->name('guest_accesses')->middleware('permission:view-guest_accesses');
        Route::get('create', [GuestAccessController::class,'create'])->name('guest_accesses.create')->middleware('permission:create-guest_accesses');
        Route::post('/', [GuestAccessController::class,'store'])->name('guest_accesses.store')->middleware('permission:create-guest_accesses');
        Route::get('{id}/modify', [GuestAccessController::class,'edit'])->name('guest_accesses.edit')->middleware('permission:update-guest_accesses');
        Route::match(['PUT','PATCH'],'{id}', [GuestAccessController::class,'update'])->name('guest_accesses.update')->middleware('permission:update-guest_accesses');
        Route::delete('{id}', [GuestAccessController::class,'destroy'])->name('guest_accesses.delete')->middleware('permission:delete-guest_accesses');
    });

    // Manage Review Routes
    Route::group(['prefix' => 'reviews'], function () {
        Route::get('/', [ReviewController::class,'index'])->name('reviews')->middleware('permission:view-reviews');
        Route::get('{id}/modify', [ReviewController::class,'edit'])->name('reviews.edit')->middleware('permission:update-reviews');
        Route::match(['PUT','PATCH'],'{id}', [ReviewController::class,'update'])->name('reviews.update')->middleware('permission:update-reviews');
    });

    // Manage Referrals Routes
    Route::group(['prefix' => 'referral-settings'], function () {
        Route::get('/', [ReferralSettingController::class,'index'])->name('referral_settings')->middleware('permission:view-referral_settings');
        Route::match(['PUT','PATCH'],'/', [ReferralSettingController::class,'update'])->name('referral_settings.update')->middleware('permission:update-referral_settings');
    });

    // Manage Translation Routes
    Route::group(['prefix' => 'translations'], function () {
        Route::match(['GET','POST'], '/', [TranslationController::class,'index'])->name('translations')->middleware('permission:view-translations');
        Route::post('/update', [TranslationController::class,'update'])->name('update_translations')->middleware('permission:update-translations');
    });

    // Manage Login Sliders Routes
    Route::group(['prefix' => 'homepage-banner'], function () {
        Route::get('/', [DiscountBannerController::class,'index'])->name('discount_banners')->middleware('permission:view-discount_banners');
        Route::get('{id}/modify', [DiscountBannerController::class,'edit'])->name('discount_banners.edit')->middleware('permission:update-discount_banners');
        Route::match(['PUT','PATCH'],'{id}', [DiscountBannerController::class,'update'])->name('discount_banners.update')->middleware('permission:update-discount_banners');
        Route::delete('{id}', [DiscountBannerController::class,'destroy'])->name('discount_banners.delete')->middleware('permission:delete-discount_banners');
    });
});
