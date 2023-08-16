<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CronController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\CalendarController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Development Test Urls
/*Route::group(['prefix' => 'dev'], function () {
	Route::get('mail', function() {
		return resolveAndSendNotification("welcomeMail",10001);
	});
	Route::get('show__l--log', function() {
		$contents = \File::get(storage_path('logs/laravel.log'));
        echo '<pre>'.$contents.'</pre>';
	});
	Route::get('clear__l--log', function() {
		file_put_contents(storage_path('logs/laravel.log'),'');
	});
});*/

Route::group(['prefix' => 'cron'], function () {
	Route::get('update-paypal-payout', [CronController::class,'updatePaypalPayouts']);
	Route::get('update-currency-rate', [CronController::class,'updateCurrencyRate']);
	Route::get('review-remainder', [CronController::class,'reviewRemainder']);
	Route::get('sync-calendars', [CronController::class,'syncCalendars']);
	Route::get('update-user-status', [CronController::class,'updateUserStatus']);
	Route::get('auto-payout', [CronController::class,'autoPayout']);
	Route::get('referral-credit', [CronController::class,'referralCredit']);
	Route::get('send-admin-report', [CronController::class,'sendAdminReport']);
});

Route::get('/', [HomeController::class,'index'])->name('home')->middleware('checkInstalled');
Route::post('get-home-data', [HomeController::class,'getHomeData'])->name('get_home_data');
Route::post('search-hotel-data', [HomeController::class,'searchHotel'])->name('search_hotels');
Route::get('sitemap.xml', [HomeController::class,'siteMapGenerator'])->name('site_map_generator');
Route::get('complete-onepay-payment/{hotel_id}', [PaymentController::class,'completeOnePayPayment'])->name('complete_onepay_payment');

Route::get('refer-and-earn', [ReferralController::class,'invite'])->name('invite');
Route::get('c/{username}', [ReferralController::class,'inviteReferral'])->name('invite_referral');
Route::post('get-referral', [ReferralController::class,'getReferrals'])->name('get_referral');
Route::post('invite-user', [ReferralController::class,'inviteUser'])->name('invite_user');

Route::get('blogs', [HomeController::class,'blog'])->name('blog');
Route::get('blog/topic/{slug}', [HomeController::class,'blogCategory'])->name('blog.category');
Route::get('blog/article/{id}/{slug}', [HomeController::class,'blogArticle'])->name('blog.article');

Route::get('user/help', [HomeController::class,'help'])->name('help');
Route::post('help-results', [HomeController::class,'helpResult'])->name('help_search_result');

Route::get('home/cancellation-policies', [HomeController::class,'cancellationPolicies'])->name('cancellation_policies');
Route::match(['GET', 'POST'], 'contact-us', [HomeController::class,'contactUs'])->name('contact_us');

Route::group(['middleware' => ['xss_protection', 'guest']], function () {
	Route::view('login', 'login')->name('login');
	Route::view('signup', 'signup')->name('signup');
	Route::post('authenticate', [UserController::class,'authenticate'])->name('authenticate');
	Route::match(['GET','POST'],'reset-password', [UserController::class,'resetPassword'])->name('reset_password');
	Route::post('set-password', [UserController::class,'setNewPassword'])->name('set_password');
	Route::post('create_user', [UserController::class,'createUser'])->name('create_user');
	Route::match(['GET','POST'],'complete-social-signup', [UserController::class,'completeSocialSignup'])->name('complete_social_signup');
	Route::match(['GET','POST'],'complete-verification', [UserController::class,'completeVerification'])->name('complete_verification');
});

Route::post('update-user-default', [HomeController::class,'updateUserDefaults'])->name('update_user_default');
Route::post('authenticate-mobile', [UserController::class,'authenticateMobile'])->name('authenticate_mobile');
Route::get('verify-email', [UserController::class,'verifyUserEmail'])->name('verify_email')->middleware('signed');

Route::get('users/show/{id}', [UserController::class,'viewProfile'])->name('view_profile');
Route::get('search', [SearchController::class,'index'])->name('search');
Route::get('search/hotel', [SearchController::class,'hotelSearch'])->name('hotel_search');
Route::match(['GET','POST'],'search/hotel-search-results', [SearchController::class,'searchResults'])->name('hotel_search_result');
Route::get('hotel/{id}', [HotelController::class,'hotelDetails'])->name('hotel_details');
Route::post('check_availability',[HotelController::class,'checkAvailability'])->name('check_availability');
Route::post('price-calculation', [HotelController::class,'reservePriceCalculation'])->name('reserve_calculation');
Route::match(['GET','POST'],'confirm-reserve', [HotelController::class,'confirmReserve'])->name('confirm_reserve');

Route::post('all_wishlists', [WishlistController::class,'getAllWishlists'])->name('all_wishlists');

Route::group(['middleware' => ['xss_protection', 'auth']], function () {
	Route::get('logout', [UserController::class,'logout'])->name('logout');
	Route::get('user/dashboard', [UserController::class,'index'])->name('dashboard');

	Route::get('users/reviews', [ReviewController::class,'UserReviews'])->name('reviews');
	Route::get('edit_review/{id}', [ReviewController::class,'editReview'])->name('edit_review');
	Route::post('update_review', [ReviewController::class,'updateReview'])->name('update_review');
	
	Route::post('update_profile', [UserController::class,'updateProfile'])->name('update_profile');
	Route::post('upload_user_document', [UserController::class,'uploadUserDocument'])->name('upload_user_document');
	Route::post('update_profile_picture', [UserController::class,'updatePhoto'])->name('update_profile_picture');
	Route::post('remove_profile_picture', [UserController::class,'removePhoto'])->name('remove_profile_picture');
	Route::group(['prefix' => 'user'], function () {
		Route::get('/', [UserController::class,'accountSettings'])->name('account_settings');
		Route::get('{page}', [UserController::class,'updateAccountSettings'])->name('update_account_settings')->where(['page' => 'personal-information|profile-photos|login-and-security|site-setting|transactions']);
		Route::get('disconnect', [UserController::class,'disconnectSocialAccount'])->name('disconnect_social_account');
		Route::post('transaction_history', [UserController::class,'transactionHistory'])->name('transaction_history');
		Route::post('number-verification', [UserController::class,'numberVerification'])->name('number_verification');
	});

	Route::match(['get', 'post'], 'payments/book/{hotel_id}', [PaymentController::class,'index'])->name('payment.home');
	Route::post('complete-payment', [PaymentController::class,'completePayment'])->name('payment.complete');
	Route::post('validate-coupon', [PaymentController::class,'validateCoupon'])->name('payment.validate_coupon');
	
	Route::match(['GET','POST'],'become-a-host/create', [HotelController::class,'becomeHost'])->name('create_listing');

	Route::get('inbox', [InboxController::class,'index'])->name('inbox');
	Route::post('message-list', [InboxController::class,'messageList'])->name('message_list');
	Route::post('inbox-action', [InboxController::class,'updateMessageStatus'])->name('inbox_action');
	Route::get('conversation/{id}', [InboxController::class,'inboxConversation'])->name('conversation');
	Route::post('send-message', [InboxController::class,'sendMessage'])->name('send_message');
	Route::post('update-read-status', [InboxController::class,'updateReadStatus'])->name('update_read_status');
	Route::post('send-special-offer', [InboxController::class,'sendSpecialOffer'])->name('send_special_offer');
	Route::post('contact-host', [InboxController::class,'contactHost'])->name('contact_host');
	Route::post('share-itinerary', [InboxController::class,'shareItinerary'])->name('share_itinerary');

	Route::get('user/bookings', [ReservationController::class,'bookings'])->name('bookings');
	Route::get('reservations', [ReservationController::class,'reservations'])->name('reservations');
	Route::post('get-reservations', [ReservationController::class,'getReservations'])->name('get_reservations');
	Route::post('cancel-reservation', [ReservationController::class,'cancelReservation'])->name('cancel_reservation');
	Route::post('request-action', [ReservationController::class,'requestAction'])->name('request_action');
	Route::get('bookings/receipt/{code}', [ReservationController::class,'viewReceipt'])->name('view_receipt');
	Route::get('download/receipt/{code}', [ReservationController::class,'downloadReceipt'])->name('download_receipt');
	Route::get('bookings/itinerary/{code}', [ReservationController::class,'viewItinerary'])->name('view_itinerary');
	Route::post('share-itinerary', [ReservationController::class,'shareItinerary'])->name('share_itinerary');
	
	Route::get('user/wishlists', [WishlistController::class,'index'])->name('wishlists');
	Route::get('wishlists/{id}', [WishlistController::class,'wishlistList'])->name('wishlist.list');
	Route::post('create_wishlist', [WishlistController::class,'createWishlist'])->name('wishlist.create');
	Route::post('save-to-wishlist', [WishlistController::class,'saveToWishlist'])->name('wishlist.save');
	Route::post('remove-wishlist', [WishlistController::class,'removeFromWishlist'])->name('wishlist.remove');
	Route::post('destroy-wishlist', [WishlistController::class,'destroyWishlist'])->name('wishlist.destroy');
	
});

Route::get('{slug}', [HomeController::class,'staticPages'])->name('static_page');
Route::post('baocao/gov', [HomeController::class,'governmentApi'])->name('government_api');
