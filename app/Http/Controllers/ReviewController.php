<?php

/**
 * Review Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    ReviewController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewPhoto;
use App\Models\Reservation;
use Auth;
use Lang;
use Validator;

class ReviewController extends Controller
{
	/**
	* Format Given Reservation Data to display in reviews page
	*
	* @param Collection $reservations
	* @return Collection $reservations
	*/
	protected function formatReservationData($reservations)
    {
    	return $reservations->map(function($reservation) {
         $review_user = ($reservation->user_id == Auth::id()) ? $reservation->host_user : $reservation->user;
         $itinerary_url = ($reservation->user_id == Auth::id()) ? 'view_receipt' : 'view_itinerary';
         return [
          'id' => $reservation->id,
          'review_days' => $reservation->getReviewDays(),
          'user_id' => $review_user->id,
          'first_name' => $review_user->first_name,
          'full_name' => $review_user->full_name,
          'profile_picture' => $review_user->profile_picture_src,
          'itinerary_url'=> resolveRoute($itinerary_url,[$reservation->code]),
      ];
  });
    }
    
    /**
    * Format Given Review Data to display in reviews page
    *
    * @param Collection $reviews
    * @return Collection $reviews
    */
    protected function formatReviewData($reviews)
    {
        return $reviews->map(function($review) {
            $review_user = ($review->user_from == Auth::id()) ? $review->review_user : $review->user;
            return [
                'id' => $review->reservation_id,
                'public_comment'=> $review->public_comment,
                'public_reply'=> $review->public_reply,
                'private_comment'=> $review->private_comment,
                'review_days' => $review->reservation->getReviewDays(),
                'user_id' => $review_user->id,
                'first_name' => $review_user->first_name,
                'full_name' => $review_user->full_name,
                'profile_picture' => $review_user->profile_picture_src,
                'itinerary_url'=> resolveRoute('view_itinerary',[$review->reservation->code]),
            ];
        });
    }

    /**
	* Display user Review Infrmations
	*
	* @return \Illuminate\Http\Response
	*/
    public function UserReviews()
    {
    	$user_id = Auth::id();

    	$reviews = Review::with('reservation','user','review_user')->authUser()->orderByDesc('id')->get();
    	$reviews_about_you = $reviews->where('user_to', $user_id)->sortByDesc('id');
        $past_reviews = $reviews->where('user_from', $user_id)->sortByDesc('id');
        $reviews_about_you = $this->formatReviewData($reviews_about_you);

        $data['reviews_about_you'] = $reviews_about_you->map(function($review) use($reviews) {
            $review['is_hidden'] = false;
            $reviews_count = $reviews->where('reservation_id',$review['id'])->count();
            if($reviews_count < 2) {
                $reservation = $reviews->where('reservation_id',$review['id'])->first()->reservation;
                $review_days = $reservation->getReviewDays();
                if($review_days > 0) {
                    $review['is_hidden'] = true;
                    $review['public_comment'] = $review['private_comment'] = '';                    
                }
            }
            return $review;
        });

        $data['past_reviews'] = $this->formatReviewData($past_reviews);

        $reservations = Reservation::authUser()
        ->afterCheckout()
        ->where('status','Accepted')
        ->get();

        $reviews_to_write = $reservations->filter(function($reservation) use($user_id) {
        	$user_review = $reservation->reviews->where('user_from',$user_id)->count();
            return $reservation->canWriteReview() && $user_review == 0;
        });

        $data['reviews_to_write'] = $this->formatReservationData($reviews_to_write);
        $data['reviews_to_write_count'] = $reviews_to_write->count();

        $reviews_to_write_ids = $reviews_to_write->pluck('id')->toArray();
        $expired_reviews = $reservations->whereNotIn('id',$reviews_to_write_ids)->filter(function($reservation) use($user_id) {
            return ($reservation->reviews->where('user_from',$user_id)->count() == 0);
        });
        $data['expired_reviews'] = $this->formatReservationData($expired_reviews);
		return view('user.reviews',$data);
    }

    /**
    * Display edit Review page
    *
    * @param \Illuminate\Http\Request
    * @return \Illuminate\Http\Response
    */
    public function editReview(Request $request)
    {
        $reservation = Reservation::find($request->id);
        if($reservation->user_id == Auth::id()) {
            $user_type = 'guest';
            $data['review_user'] = $reservation->host_user;
        }
        else if($reservation->host_id == Auth::id()) {
            $user_type = 'host';
            $data['review_user'] = $reservation->user;
        }
        else {
            abort(404);
        }

        if($reservation->status != 'Accepted' || !($reservation->canWriteReview() && $reservation->checkoutCrossed())) {
            abort(404);
        }

        $user_review = $reservation->reviews->where('user_from',Auth::id())->first();
        $data['review_photos'] = [];
        $data['reservation'] = $reservation;
        $data['hotel'] = $reservation->hotel;
        $data['review'] = $review = $user_review ?? new Review;
        $review_photos = \App\Models\ReviewPhoto::where('review_id',$review->id)->get()->map(function($review) {
            return [
                'id' => $review->id,
                'image_src' => $review->image_src,
            ];
        });
        $data['review_photos'] = $review_photos;
        $data['exit_url'] = resolveRoute("reviews");
        $data['action_url'] = resolveRoute('update_review',['id' => $reservation->id]);
        
        return view('user.'.$user_type.'_review', $data);
    }

    /**
    * Display edit Review page
    *
    * @param $id
    * @return \Illuminate\Http\Response
    */
    public function updateReview(Request $request)
    {
        $rules = array(
            'rating' => 'required|numeric|min:1|max:5',
            'public_comment' => 'required|max:1500',
            // 'private_comment' => 'required|max:1500',
            'recommend' => 'required',
        );

        $attributes = array(
            'rating' => Lang::get('messages.rating'),
            'public_comment' => Lang::get('messages.public_comment'),
            // 'private_comment' => Lang::get('messages.private_comment'),
            'recommend' => Lang::get('messages.recommend'),
        );
        $review_photos = ReviewPhoto::where('id',$request->review_id);
        $required = $review_photos->count() == 0 ? 'required' : 'nullable';
        foreach(request()->photos ?? [] as $key => $photo) {
            $rules['photos.'.$key] = $required.'|file|max:5120';
            $messages['photos.'.$key.'.required'] = Lang::get('admin_messages.add_atleast_one_photo');
            $messages['photos.'.$key.'.max'] = "Maximum Upload Size is 5 mb Only.";
        }
        $validator = Validator::make($request->all(), $rules, [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'status_message' => $validator->messages(),
            ]);
        }

        $photos_list = $request->photos ?? [];
        $review_photo_count = ReviewPhoto::where('review_id',$request->review_id)->count();
        $total_photo_count = $review_photo_count + count($photos_list);
        if ($total_photo_count > 5) {
            $photo_error = [
                'photos' => "you can upload only 5 Images",
            ];

            return response()->json([
                'status' => false,
                'status_message' => $photo_error,
            ]);
        }

        $reservation = Reservation::authUser()->findOrFail($request->id);

        if($reservation->user_id == Auth::id()) {
            $user_type = 'guest';
        }
        if($reservation->host_id == Auth::id()) {
            $user_type = 'host';
        }

        $review = Review::firstOrNew(['reservation_id' => $reservation->id,'review_by' => $user_type,'user_from' => Auth::id()]);
        $review->hotel_id = $reservation->hotel_id;
        $review->user_to = ($user_type == 'guest') ? $reservation->host_id : $reservation->user_id;
        $review->rating = $request->rating;
        $review->public_comment = $request->public_comment;
        // $review->private_comment = $request->private_comment;
        $review->recommend = $request->recommend;
        if($reservation->user_id == Auth::id()){
            $review->cleanliness = $request->cleanliness_rating;
            $review->communication = $request->communication_rating;
            $review->accuracy = $request->accuracy_rating;
            $review->check_in = $request->checkin_rating;
            $review->value = $request->value_rating;
            $review->location = $request->location_rating;
        }
        $review->save();

        $removed_photos = explode(',',$request->removed_photos);
        $this->deleteReviewPhotos($removed_photos);

        if(isset($photos_list) && count($photos_list) > 0) {
            $this->updateReviewPhotos($review->id,$photos_list);
        }

        // resolveAndSendNotification("readOrWriteReview",$reservation->id,$user_type);

        flashMessage('success', Lang::get('messages.success'), Lang::get('messages.review_updated_successfully'));
        $redirect_url = resolveRoute('reviews');
        return response()->json([
            'status' => 'redirect',
            'redirect_url' => $redirect_url,
        ]);
    }

    /**
     * Upload Review Review Photos with Order
     *
     * @param String $review_id
     * @param File $photos_list
     *
     * @return Object Upload Result
     */
    protected function updateReviewPhotos($review_id,$photos_list)
    {
        $last_photo = ReviewPhoto::where('review_id',$review_id)->latest('order_id')->first();
        $last_order_id = optional($last_photo)->order_id;

        $image_data['add_time'] = true;
        $review_photo = new ReviewPhoto;
        $review_photo->review_id = $review_id;
        $image_data['target_dir'] = $review_photo->getUploadPath();
        $image_data['compress_size'] = array();

        $image_handler = resolve('App\Contracts\ImageHandleInterface');
        foreach($photos_list as $index => $photo) {
            $image_data['name_prefix'] = 'review_'.$review_id.'_'.$index;
            $upload_result = $image_handler->upload($photo,$image_data);

            if($upload_result['status']) {
                $photos = new ReviewPhoto;
                $photos->review_id = $review_id;
                $photos->image = $upload_result['file_name'];
                $photos->upload_driver= $upload_result['upload_driver'];
                $photos->order_id = ++$last_order_id;
                $photos->save();
            }
        }
    }

    /**
     * Delete Given Review Photos
     *
     * @param Array $image_ids
     *
     * @return Boolean
     */
    public function deleteReviewPhotos($image_ids)
    {
        if(count($image_ids) == 0) {
            return false;
        }
        $revicw_photos = ReviewPhoto::whereIn('id',$image_ids)->get();
        $revicw_photos->each(function($revicw_photo) {
            $handler = $revicw_photo->getImageHandler();
            $image_data['target_dir'] = $revicw_photo->getUploadPath();
            $image_data['name'] = $revicw_photo->image;
            $handler->destroy($image_data);

            $revicw_photo->delete();
        });
        return true;
    }  
}
