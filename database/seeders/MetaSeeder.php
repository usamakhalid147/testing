<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('metas')->delete();

		$currentDateTime = date('Y-m-d H:i:s');
		
		$id = 1;
		DB::table('metas')->insert([
			['id' => $id++, 'route_name' => 'home','display_name' => '/', 'title' => '{"en":"Holiday Lets, Homes & Places - {SITE_NAME}"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'hotel_search','display_name' => 'hotel_search', 'title' => '{"en":"Search"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'hotel_details','display_name' => 'hotel_details', 'title' => '{"en":"{LISTING_NAME} in {CITY_NAME}"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'login','display_name' => 'login', 'title' => '{"en":"Login / Signup"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'signup','display_name' => 'signup', 'title' => '{"en":"Signup / Login"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'host.signup','display_name' => 'host_signup', 'title' => '{"en":"Host Signup / Login"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'reset_password','display_name' => 'reset_password', 'title' => '{"en":"Recover Your Account"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'view_profile','display_name' => 'view_profile', 'title' => '{"en":"{USER_NAME} \'s Profile"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'dashboard','display_name' => 'dashboard', 'title' => '{"en":"Dashboard"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'create_listing','display_name' => 'create_listing', 'title' => '{"en":"List Your Space and earn with {SITE_NAME} | Be a host"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'listings','display_name' => 'listings', 'title' => '{"en":"Your Listings"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'listing_home','display_name' => 'listing_home', 'title' => '{"en":"Manage Your Listing"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'manage_listing','display_name' => 'manage_listing', 'title' => '{"en":"Manage Your Listing"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'invite','display_name' => 'invite', 'title' => '{"en":"Refer and Earn"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'invite_referral','display_name' => 'invite_referral', 'title' => '{"en":"Refer and Earn"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'account_settings','display_name' => 'account_settings', 'title' => '{"en":"Account Settings"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'update_account_settings','display_name' => 'account_settings/{PAGE}', 'title' => '{"en":"{PAGE} - {SITE_NAME}"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'add_payout','display_name' => 'add_payout', 'title' => '{"en":"Add Payout Method"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'reviews','display_name' => 'user_reviews', 'title' => '{"en":"User Reviews"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'edit_review','display_name' => 'edit_reviews', 'title' => '{"en":"Edit a Review"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'payment.home','display_name' => 'payment.home', 'title' => '{"en":"Complete Your Booking"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'inbox','display_name' => 'inbox', 'title' => '{"en":"Inbox"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'conversation','display_name' => 'conversation', 'title' => '{"en":"Conversation"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'bookings','display_name' => 'Booking', 'title' => '{"en":"Your Bookings"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'reservations','display_name' => 'reservations', 'title' => '{"en":"Your Reservations"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'view_receipt','display_name' => 'receipt', 'title' => '{"en":"Receipt"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'view_itinerary','display_name' => 'itinerary', 'title' => '{"en":"Itinerary"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'contact_us','display_name' => 'contact_us', 'title' => '{"en":"Contact Us"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'cancellation_policies','display_name' => 'cancellation_policies', 'title' => '{"en":"Cancellation Policies"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'no_internet','display_name' => 'no_internet', 'title' => '{"en":"No Internet Connection"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'account_disabled','display_name' => 'account_disabled', 'title' => '{"en":"Account Disabled"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'blog','display_name' => 'blog', 'title' => '{"en":"Blogs - {SITE_NAME}"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'blog.category','display_name' => 'blog category', 'title' => '{"en":"{SLUG} - {SITE_NAME} Blog"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'blog.article','display_name' => 'blog article', 'title' => '{"en":"{SLUG} - {SITE_NAME} Blog"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'help','display_name' => 'help', 'title' => '{"en":"{SITE_NAME} - Help Center"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'help.category','display_name' => 'help category', 'title' => '{"en":"{SLUG} - {SITE_NAME} Help Center"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'help.article','display_name' => 'help article', 'title' => '{"en":"{SLUG} - {SITE_NAME} Help Center"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'wishlists','display_name' => 'wishlists', 'title' => '{"en":"Saved - {SITE_NAME}"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'wishlist.list','display_name' => 'wishlist list', 'title' => '{"en":"{LIST_NAME} · Saved - {SITE_NAME}"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'host_coupon_codes','display_name' => 'host_coupon_codes', 'title' => '{"en":"Manage Coupon Codes"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'host_coupon_codes.create','display_name' => 'create_host_coupon_codes', 'title' => '{"en":"Manage Coupon Codes"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'host_coupon_codes.edit','display_name' => 'edit_host_coupon_codes', 'title' => '{"en":"Manage Coupon Codes"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => $id++, 'route_name' => 'host.signup','display_name' => 'host_signup', 'title' => '{"en":"Host Signup"}', 'description' => NULL, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
		]);
    }
}
