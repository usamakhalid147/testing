<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturedCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('featured_cities')->delete();
        DB::table('popular_cities')->delete();
		
		$current_date = date('Y-m-d H:i:s');

		DB::table('featured_cities')->insert([
			['city_name' => 'Washington D.C., DC, USA','display_name' => 'Washington DC','latitude' => '38.9071923','longitude' => '-77.0368707', 'place_id' => 'ChIJW-T2Wt7Gt4kRKl2I1CJFUsI', 'image' => 'washington_dc.webp','order_id' => '1','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'San Francisco, CA, USA','display_name' => 'San Francisco','latitude' => '37.7749295','longitude' => '-122.4194155', 'place_id' => 'ChIJIQBpAG2ahYAR_6128GcTUEo', 'image' => 'san_francisco.webp','order_id' => '2','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'Los Angeles, CA, USA','display_name' => 'Los Angelos','latitude' => '48.85661400000001','longitude' => '2.3522219', 'place_id' => '2wlshiNASwI9rMlMQg8tp8upenS31V9KVNDRNQn5', 'image' => 'los_angeles.jpg','order_id' => '3','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'London, UK','display_name' => 'London','latitude' => '51.5073509','longitude' => '-0.1277583', 'place_id' => 'ChIJdd4hrwug2EcRmSrV3Vo6llI', 'image' => 'london.jpg','order_id' => '4','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia','display_name' => 'Malaysia','latitude' => '3.139003','longitude' => '101.686855', 'place_id' => 'ChIJ0-cIvSo2zDERmWzYQPUfLiM', 'image' => 'kuala_lumpur.jpg','order_id' => '5','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'Tokyo, Japan','display_name' => 'Tokyo','latitude' => '35.6803997','longitude' => '139.7690174', 'place_id' => 'ChIJXSModoWLGGARILWiCfeu2M0', 'image' => 'tokyo.jpg','order_id' => '6','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'İstanbul, Turkey','display_name' => 'Istanbul','latitude' => '41.0082376','longitude' => '28.9783589', 'place_id' => 'ChIJawhoAASnyhQR0LABvJj-zOE', 'image' => 'istanbul.jpg','order_id' => '7','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['city_name' => 'Sydney NSW, Australia','display_name' => 'Sydney','latitude' => '-33.8688197','longitude' => '151.2092955', 'place_id' => 'ChIJP3Sa8ziYEmsRUKgyFmh9AQM', 'image' => 'sydney.jpg','order_id' => '8','upload_driver' => '0','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
		]);

		DB::table('popular_cities')->insert([
			['id' => '1','name' => 'New York','address' => 'New York, NY, USA','place_id' => 'ChIJOwg_06VPwokRYv534QaPC8g','latitude' => '40.7127753','longitude' => '-74.0059728','country_code' => 'US','viewport' => '{"south":40.47739906045452,"west":-74.25908991427882,"north":40.91757705070789,"east":-73.70027206817629}','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['id' => '2','name' => 'Dubai','address' => 'Dubai - United Arab Emirates','place_id' => 'ChIJRcbZaklDXz4RYlEphFBu5r0','latitude' => '25.2048493','longitude' => '55.2707828','country_code' => 'AE','viewport' => '{"south":24.79348418590246,"west":54.89045432509004,"north":25.35856066265986,"east":55.56452157241026}','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['id' => '3','name' => 'London','address' => 'London, UK','place_id' => 'ChIJdd4hrwug2EcRmSrV3Vo6llI','latitude' => '51.5073509','longitude' => '-0.1277583','country_code' => 'GB','viewport' => '{"south":51.38494012429096,"west":-0.3514683384218145,"north":51.67234324898703,"east":0.1482710335611201}','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['id' => '4','name' => 'England','address' => 'England, UK','place_id' => 'ChIJ39UebIqp0EcRqI4tMyWV4fQ','latitude' => '52.3555177','longitude' => '-1.1743197','country_code' => 'GB','viewport' => '{"south":49.86474113932771,"west":-6.418545836129257,"north":55.81165980867713,"east":1.762915928543879}','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
			['id' => '5','name' => 'New South Wales','address' => 'New South Wales, Australia','place_id' => 'ChIJDUte93TLDWsRLZ_EIhGvgBc','latitude' => '-31.2532183','longitude' => '146.921099','country_code' => 'AU','viewport' => '{"south":-37.50528013489001,"west":140.9992792520444,"north":-28.15701997886234,"east":159.1054440704646}','status' => '1','created_at' => $current_date,'updated_at' => $current_date],
		]);
		DB::table('popular_localities')->insert([
			['id' => '1','popular_city_id' => '1','name' => 'Manhattan','address' => 'Manhattan, New York, NY, USA','place_id' => 'ChIJYeZuBI9YwokRjMDs_IEyCwo','latitude' => '40.7830603','longitude' => '-73.9712488','country_code' => 'US','status' => '1','created_at' => '2021-10-13 07:58:04','updated_at' => '2021-10-13 08:09:19'],
			['id' => '2','popular_city_id' => '1','name' => 'Brooklyn','address' => 'Brooklyn, NY, USA','place_id' => 'ChIJCSF8lBZEwokRhngABHRcdoI','latitude' => '40.6781784','longitude' => '-73.9441579','country_code' => 'US','status' => '1','created_at' => '2021-10-13 08:57:41','updated_at' => '2021-10-13 08:57:41'],
			['id' => '3','popular_city_id' => '1','name' => 'Time Square','address' => 'Times Square, Manhattan, New York, NY, USA','place_id' => 'ChIJnaBtqVVYwokRaAqg4aX1C4Y','latitude' => '40.7579747','longitude' => '-73.9855426','country_code' => 'US','status' => '1','created_at' => '2021-10-13 08:58:32','updated_at' => '2021-10-13 08:58:32'],
			['id' => '4','popular_city_id' => '2','name' => 'Deira','address' => 'Deira - Dubai - United Arab Emirates','place_id' => 'ChIJk67NN09DXz4RkYS3oWNjdd4','latitude' => '25.2788468','longitude' => '55.3309395','country_code' => 'AE','status' => '1','created_at' => '2021-10-13 08:59:39','updated_at' => '2021-10-13 08:59:39'],
			['id' => '5','popular_city_id' => '2','name' => 'Jumeirah Beach','address' => 'Jumeirah - Dubai - United Arab Emirates','place_id' => 'ChIJ_xyxUBRCXz4RPlErL42mVPY','latitude' => '25.2016428','longitude' => '55.2452567','country_code' => 'AE','status' => '1','created_at' => '2021-10-13 09:00:37','updated_at' => '2021-10-13 09:00:37'],
			['id' => '6','popular_city_id' => '3','name' => 'Hyde Park','address' => 'Hyde Park, London, UK','place_id' => 'ChIJhRoYKUkFdkgRDL20SU9sr9E','latitude' => '51.5072682','longitude' => '-0.1657303','country_code' => 'GB','status' => '1','created_at' => '2021-10-13 09:01:39','updated_at' => '2021-10-13 09:01:39'],
			['id' => '7','popular_city_id' => '3','name' => 'City of London','address' => 'City of London, London, UK','place_id' => 'ChIJX4XfTlUDdkgRwISR0ciFEQo','latitude' => '51.5123443','longitude' => '-0.0909852','country_code' => 'GB','status' => '1','created_at' => '2021-10-13 09:02:29','updated_at' => '2021-10-13 09:02:29'],
			['id' => '8','popular_city_id' => '4','name' => 'Cambridge','address' => 'Cambridge, UK','place_id' => 'ChIJLQEq84ld2EcRIT1eo-Ego2M','latitude' => '52.205337','longitude' => '0.121817','country_code' => 'GB','status' => '1','created_at' => '2021-10-13 09:05:41','updated_at' => '2021-10-13 09:05:41'],
			['id' => '9','popular_city_id' => '4','name' => 'Oxford','address' => 'Oxford, UK','place_id' => 'ChIJrx_ErYAzcUgRAnRUy6jbIMg','latitude' => '51.7520209','longitude' => '-1.2577263','country_code' => 'GB','status' => '1','created_at' => '2021-10-13 09:06:14','updated_at' => '2021-10-13 09:06:14'],
			['id' => '10','popular_city_id' => '5','name' => 'Sydeny Olympic Park','address' => 'Sydney Olympic Park NSW, Australia','place_id' => 'ChIJhZxZzrikEmsRwLoyFmh9AQU','latitude' => '-33.8465088','longitude' => '151.0722137','country_code' => 'AU','status' => '1','created_at' => '2021-10-13 09:10:18','updated_at' => '2021-10-13 09:10:18'],
			['id' => '11','popular_city_id' => '5','name' => 'Sydney Airport (SYD]','address' => 'Sydney Airport (SYD], Sydney NSW, Australia','place_id' => 'ChIJ24MzG_GwEmsRd2VLWl01368','latitude' => '-33.95003440000001','longitude' => '151.1819124','country_code' => 'AU','status' => '1','created_at' => '2021-10-13 09:10:55','updated_at' => '2021-10-13 09:10:55']
		]);
    }
}
