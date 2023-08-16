<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
			GlobalSettingSeeder::class,
			CredentialSeeder::class,
			FeesSeeder::class,
			ReferralSettingSeeder::class,
			MetaSeeder::class,
			CountrySeeder::class,
			CurrencySeeder::class,
			LanguageSeeder::class,
			TimezoneSeeder::class,
			LaravelEntrustSeeder::class,
			SliderSeeder::class,
			SocialMediaLinkSeeder::class,
			PreFooterSeeder::class,
		]);

		$this->call([
			PropertyTypeSeeder::class,
			RoomTypeSeeder::class,
			AmenitySeeder::class,
			BedTypeSeeder::class,
			GuestAccessSeeder::class,
			HotelRuleSeeder::class,
			FeaturedCitySeeder::class,
			StaticPageSeeder::class,
			BlogSeeder::class,
			HelpSeeder::class,
			CitySeeder::class,
			MealPlanSeeder::class,
		]);

		/*PackageCommentStart
		$this->call([
			UserSeeder::class,
			HotelSeeder::class,
			ReservationSeeder::class,
		]);
		PackageCommentEnd*/
		
    }
}
