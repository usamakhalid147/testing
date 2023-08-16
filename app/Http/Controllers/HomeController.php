<?php

/**
 * Home Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    HomeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\DiscountBanner;
use App\Models\FeaturedCity;
use App\Models\StaticPage;
use App\Models\Hotel;
use App\Models\Admin;
use App\Models\PopularCity;
use App\Models\PreFooter;
use App\Models\BlogCategory;
use App\Models\HelpCategory;
use App\Models\Help;
use Validator;
use Lang;
use App\Models\User;
use App\Models\Transaction;
use App\Models\SiteVisitor;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HomeController extends Controller
{
	/**
	 * Format Rooms Data
	 *
	 * @param  \Illuminate\Support\Collection  $rooms
	 * @return \Illuminate\Support\Collection
	 */
	protected function mapHotelsData($hotels)
	{
		return $hotels->map(function($hotel) {
			$hotel_data = $hotel->only(['id','name','description','property_type_name']);
			$hotel_data['total_rating'] = $hotel->total_rating;

			$hotel_data['link'] = $hotel->link;
			$hotel_data['image_src'] = $hotel->image_src;
			$hotel_data['review_stars'] = $hotel->getReviewStars();
			$hotel_data['city'] =$hotel->hotel_address->city;
			$room = $hotel->hotel_rooms->first();
			$hotel_data['price_text'] = $hotel->currency_symbol.$room->hotel_room_price->price;
			$hotel_data['photos_list'] = $hotel->hotel_photos->first();
			return array_merge($hotel_data);
		});
	}

	/**
	 * Get Nearest popular Locations
	 *
	 * @param  String  $latitude
	 * @param  String  $longitude
	 * @return \Illuminate\Support\Collection
	 */
	protected function getNearestLocations($latitude,$longitude)
	{
		return collect();
	}

	/**
	 * Display Home page
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$data['sliders'] = Slider::ordered()->activeOnly()->get();
		$data['discount_banners'] = DiscountBanner::ordered()->activeOnly()->get();
		$data['pre_footers'] = PreFooter::activeOnly()->limit(2)->get();

		$best_hotels = Hotel::with('hotel_rooms.hotel_room_price')->inRandomOrder()->viewOnly()->recommended()->limit(2)->get();
		$data['best_hotels'] = $this->mapHotelsData($best_hotels);
		return view('home',$data);
	}

	/**
	 * Display Home page
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getHomeData(Request $request)
	{
		$recommended_hotels = Hotel::with('hotel_rooms.hotel_room_price')->inRandomOrder()->viewOnly()->recommended()->limit(12)->get();
		$recommended_hotels = $this->mapHotelsData($recommended_hotels);
		
		$featured_cities = FeaturedCity::activeOnly()->ordered()->limit(8)->get()->map(function($featured_city) {
			return [
				'id' => $featured_city->id,
				'search_url' => $featured_city->search_url,
				'image_src' => $featured_city->image_src,
				'display_name' => $featured_city->display_name,
			];
		});

		$nearest_locations = $this->getNearestLocations($request->latitude,$request->longitude);

		return response()->json([
			'status' => true,
			'nearest_locations' => $nearest_locations->values(),
			'recommended_hotels' => $recommended_hotels->values(),
			'featured_cities' => $featured_cities->values(),
			'home_popular_cities' => [],
		]);
	}

	public function searchHotel(Request $request)
	{
		$location = $request->location ?? '';

		if ($location == '') {
			return response()->json([
				'status' => false,
				'status_message' => Lang::get('messages.please_enter_location_or_hotel'),
				'home_popular_cities' => [],
			]);
		}

		$location_query = "%".strtolower($location)."%";
		$locale = session('language') ?? global_settings('default_language');

		$cities = \App\Models\City::where('name','LIKE',$location_query)->orWhere('roman_number','LIKE',$location_query);

		$search_result = collect();
		if ($cities->count() > 0) {
			$search_result = $cities->limit(5)->get()->map(function ($city) {
				$hotel = Hotel::with('hotel_address')->listed()->verified()
				->whereHas('hotel_address',function ($query) use($city) {
					$query->where('city',$city->name);
				})->first();
				$hotel_address = optional($hotel)->hotel_address ?? '';
				return [
					'text' => '',
					'place_id' => '',
					'main_text' => $city->name,
					'sub_text' => $city->name.', '.$city->country,
					'is_hotel' => false,
					'type' => 'city',
					'link' => 'javascript:;',
					'hotel_address_count' =>  0,
				];
			});
		}

		$hotels = Hotel::with('hotel_address')->listed()->verified();
		$hotels->whereRaw('LOWER(name) LIKE "'.$location_query.'"');

		$hotels = $hotels->limit(5)->get()->map(function($hotel) {
			$hotel_count = $hotel->count();
			return [
				'text' => '',
				'place_id' => '',
				'main_text' => $hotel->name,
				'sub_text' => optional($hotel->hotel_address)->city.', '.optional($hotel->hotel_address)->country_code,
				'is_hotel' => true,
				'type' => 'hotel',
				'link' => resolveRoute('hotel_details',['id' => $hotel->id]),
				'hotel_address_count' =>  0,
			];
		});

		$search_result = $search_result->concat($hotels);

		return response()->json([
			'status' => true,
			'home_popular_cities' => $search_result ?? [],
		]);
	}

	/**
	 * Display Cancellation Policy Details
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function cancellationPolicies()
	{
		return view('cancellation_policies');
	}

	/**
	 * Generate Site Map
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function siteMapGenerator()
	{
		$metas = resolve("Meta")->map(function($meta) {
			try {
				return [
					'loc' => resolveRoute($meta->route_name),
					'lastmod' => $meta->updated_at->format("y-m-d\TH:i:s\Z"),
					'priority' => "1.00",
				];				
			}
			catch(\Exception $e) {

			}
		})
		->filter()
		->values();

		return response()->view('sitemap', compact('metas'))
		->header('Content-Type', 'text/xml');
	}

	/**
	 * Update User Default settings
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return json success
	 */
	public function updateUserDefaults(Request $request)
	{
		if($request->type == 'token') {
			if(\Auth::check()) {
				$user = \Auth::user();
				$user->fcm_token = $request->token;
				$user->save();
			}
			return response()->json(['status' => true,'status_message' => "updated successfully"]);
		}
		$user_currency = $request->currency;
		$user_language = $request->language;

		if(\Auth::check()) {
			$user = \Auth::user();
			$user->user_currency = $user_currency;
			$user->user_language = $user_language;
			$user->save();
		}

		$currency = resolve("Currency")->where('code',$user_currency)->first();
		if($request->route_name == 'hotel_search') {
			session(['previous_currency' => session('currency')]);
		}
		
		session(['currency' => $user_currency]);
		session(['currency_symbol' => $currency->symbol]);
		session(['language' => $user_language]);

		if(global_settings('is_locale_based') && $user_language != LOCALE) {
			$url = \Str::of(url()->previous())->replace(url('/'), '')->replaceFirst(LOCALE,$user_language)->prepend(url('/'));
			return response()->json([
				'status' => 'redirect',
				'redirect_url' => $url,
				'status_message' => Lang::get('messages.updated_successfully'),
			]);
		}

		return response()->json([
			'status' => true,
			'status_message' => Lang::get('messages.updated_successfully'),
		]);
	}

	/**
	 * Display Static page
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function staticPages(Request $request)
	{
		$page = StaticPage::where('slug',$request->slug)->firstOrFail();

		if($page->status == 0 && !\Auth::guard('admin')->check()) {
			abort(404);
		}

		$replace_keys = ['SITE_NAME','SITE_URL'];
		$replace_values = [SITE_NAME,url('/')];
		$data['content'] = \Str::of($page->content)->replace($replace_keys,$replace_values);
		$data['title'] = $page->name;
		
		return view('static_page',$data);
	}

	public function governmentApi(Request $request)
	{
		if (!Str::of($request->getContent())->isJson()) {
			return response()->json([
				'error' => true,
				'message' => 'In Valid Json'
			]);
		}
		$payload = json_decode($request->getContent(), true);
		$validator = Validator::make(
			$payload,
			[
				'UserName' => 'required',
				'PassWord' => 'required',
			],
			[
				'UserName' => 'The :attribute is required.',
				'PassWord' => 'The :attribute is required.'
			]
		);
		if ($validator->fails()) {
			return response()->json([
				'error' => true,
				'message' => $validator->messages()->first()
			]);
		}
		if (
			$payload['UserName'] != config('services.goverment_api.username')
			||
			$payload['PassWord'] != config('services.goverment_api.password')
		) {
			return response()->json([
				'error' => true,
				'message' => 'Invalid Username or Password'
			]);
		}
		return response()->json([
			"numberOfVisitors" => User::where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))->count(),
			"totalNumberOfSeller" => $totalNumberOfHosts = User::where('user_type', '=', config('services.goverment_api.gov_api_seller_type'))
                          ->where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))
                          ->count(),
			"numberOfNewSeller" => User::whereDate('created_at',today())->count(),
			"totalNumberOfProduct" => Hotel::where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))->count(),
			"numberOfNewProduct" => Hotel::todayOnly()->count(),
			"totalNumberOfOrders" => Reservation::where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))->count(),
			"totalSuccessfulOrders" => Reservation::where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))
                     ->whereNull('cancelled_at')
                     ->count(),
			"totalFailedOrders" => Reservation::where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))
                     ->whereNotNull('cancelled_at')
                     ->count(),
			"totalTransactionValue" => round(Reservation::where('created_at', '>', Carbon::parse(config('services.goverment_api.gov_api_visitors_start_date')))
                     ->whereNull('cancelled_at')
                     ->sum('total'),0)
		]);
	}

	/**
	 * Display Contact Us page
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function contactUs(Request $request)
	{
		if($request->isMethod("GET")) {
			return view('contact_us');
		}
		
		$rules = [
			'name' 		=> 'required',
			'email' 	=> 'required',
			'feedback' 	=> 'required',
		];

		$attributes = [
			'name'  => Lang::get('messages.name'),
		];

		$validator = Validator::make($request->all(), $rules,[],$attributes);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		if(checkEnabled('ReCaptcha')) {
			$recaptcha_service = resolve('App\Services\ReCaptchaService');
			$captchaValidate = $recaptcha_service->validateReCaptcha($request['g-recaptcha-response']);
			if(!$captchaValidate['status']) {
				$recaptcha_error = [
					'g-recaptcha-response' => Lang::get('messages.please_complete_captcha_to_continue')
				];
				return back()->withErrors($recaptcha_error)->withInput();
			}
		}

		$data = array(
			'email' 	=> $request->email,
			'name' 		=> $request->name,
			'feedback' 	=> $request->feedback,
			'subject'	=> Lang::get('messages.contact_us_from').' '.$request->name,
		);

		resolveAndSendNotification("contactAdmin",$data);

		flashMessage('success',Lang::get('messages.success'),Lang::get('messages.mail_sent_successfully'));
		$redirect_url = resolveRoute('contact_us');
		return redirect($redirect_url);
	}

	protected function mapChildCategories($help_categories)
	{
		return $help_categories->map(function($help_category) {
			$help_category->load('helps');
			$help_category->load('child_categories');
			$has_child = $help_category->child_categories->activeOnly()->count() > 0;
			if($has_child) {
				$child_categories = $this->mapChildCategories($help_category->child_categories);
			}
			$helps = $help_category->helps->activeOnly()->map(function($help) {
				return [
					'id' => $help->id,
					'slug' => $help->slug,
					'title' => $help->title,
					'content' => $help->content,
					'tags' => $help->tags,
					'is_recommended' => $help->is_recommended,
				];
			});
			return [
				'id' => $help_category->id,
				'title' => $help_category->title,
				'description' => $help_category->description,
				'has_child' => $has_child,
				'helps' => $helps,
				'child_categories' => $child_categories ?? [],
			];
		});
	}

	/**
	 * Display Help page
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function help(Request $request)
	{
		$help_categories = resolve('HelpCategory')->activeOnly()->where('category_id',0);
		$data['help_categories'] = $help_categories->map(function($help_category) {
			$help_category->load('helps');
			$help_category->load('child_categories');
			$has_child = $help_category->child_categories->activeOnly()->count() > 0;
			if($has_child) {
				$child_categories = $this->mapChildCategories($help_category->child_categories);
			}
			
			$helps = $help_category->helps->activeOnly()->map(function($help) {
				return [
					'id' => $help->id,
					'slug' => $help->slug,
					'title' => $help->title,
					'content' => $help->content,
					'tags' => $help->tags,
					'is_recommended' => $help->is_recommended,
				];
			});

			return [
				'id' => $help_category->id,
				'title' => $help_category->title,
				'description' => $help_category->description,
				'has_child' => $has_child,
				'helps' => $helps,
				'child_categories' => $child_categories ?? [],
			];
		});

		$data['recommended_helps'] = resolve('Help')->activeOnly()->where('is_recommended','1');

		return view('help.index',$data);
	}

	/**
     * Search for help with given filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json helps data
     */
	public function helpResult(Request $request)
	{
		if ($request->search_text == '' || $request->search_text == null ) {
			return response()->json([
				'status' => false,
				'status_message' => Lang::get('messages.invalid_request'),
			]);
		}

		$search_texts = strtolower($request->search_text);
		$search_texts = explode(' ',$search_texts);
        $locale = global_settings('default_language');
		$query = Help::query();

		foreach ($search_texts as $search_text) {
			$search_text = '%'.$search_text.'%';
			$query->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title,\'$.'.$locale.'\'))) LIKE "'.$search_text.'"');
		}

		$helps = $query->get();

		if($helps->count() == 0) {
			return response()->json([
				'status' => false,
				'status_message' => Lang::get('messages.no_result_found'),
			]);
		}

		return response()->json([
			'status' => true,
			'data' => $helps,
		]);

	}

	/**
	 * Display Help Category page
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function helpCategory(Request $request)
	{
		$data['help_category'] = $help_category = HelpCategory::with('parent_category')->where('slug',$request->slug)->firstOrFail();
		$data['help_categories'] = traverseTree($help_category,'parent_category')->reverse();
		$data['child_categories'] = $data['help_categories']->first()->child_categories;

		return view('help.category',$data);
	}

	/**
	 * Display Help Article
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function helpArticle(Request $request)
	{
		$help_article = resolve('Help')->where('slug',$request->slug)->first();
		if($help_article == '') {
			abort(404);
		}
		$data['help'] = $help_article;
		$help_category = HelpCategory::with('parent_category')->find($help_article->category_id);
		$data['help_category'] = traverseTree($help_category,'parent_category')->reverse();
		$data['child_categories'] = $data['help_category']->first()->child_categories;

		return view('help.article',$data);
	}

	/**
	 * Display Blog page
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function blog(Request $request)
	{
		$blog_categories = resolve('BlogCategory')->activeOnly();
		$blog_articles = resolve('Blog')->activeOnly();
		$data['popular_categories'] = $blog_categories->where('is_popular','1');
		$data['other_categories'] = $blog_categories->where('is_popular','!=','1');
		$data['popular_blogs'] = $blog_articles->where('is_popular','1')->slice(0, 4)->values();
		$data['latest_blogs'] = $blog_articles->sortBy('created_at')->slice(0, 4)->values();

		return view('blog.index',$data);
	}

	/**
	 * Display Blog Category page
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function blogCategory(Request $request)
	{
		$data['blog_category'] = $blog_category = BlogCategory::with('parent_category')->where('slug',$request->slug)->firstOrFail();
		$data['blog_categories'] = traverseTree($blog_category,'parent_category')->reverse();
		$data['child_categories'] = $data['blog_categories']->first()->child_categories;

		return view('blog.category',$data);
	}

	/**
	 * Display Blog Article
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function blogArticle(Request $request)
	{
		$blog_article = resolve('Blog')->where('slug',$request->slug)->first();
		if($blog_article == '') {
			abort(404);
		}
		$data['blog'] = $blog_article;
		$blog_category = BlogCategory::with('parent_category')->find($blog_article->category_id);
		$data['blog_category'] = traverseTree($blog_category,'parent_category')->reverse();
		$data['child_categories'] = $data['blog_category']->first()->child_categories;

		return view('blog.article',$data);
	}
}
