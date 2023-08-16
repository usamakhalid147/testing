<?php

/**
 * Review Observer
 *
 * @package     HyraHotel
 * @subpackage  Observers
 * @category    ReviewObserver
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Observers;

use App\Models\Hotel;
use App\Models\Review;
use App\Models\Reservation;

class ReviewObserver
{
    /**
     * Calculate Average Rating of the hotel
     *
     * @param  Int $hotel_id
     * @return Array rating and total
     */
    public function calculateRating($hotel_id)
    {
    	$reviews = Review::select('user_from','user_to','review_by','rating')->where('hotel_id',$hotel_id)->activeUser()->userTypeBased('guest')->get();
        $total_count = $reviews->count();
        $total_ratings = $reviews->sum('rating');
        if ($total_count == 0) {
            $total_count = 1;
        }
        if ($total_ratings == 0) {
            $total_ratings = 1;
        }
        $rating = numberFormat($total_ratings / $total_count);
        return ['total' => $total_count, 'average' => $rating];
    }

    /**
     * Check review can be update on room
     *
     * @param  Int $reservation_id
     * @return Boolean
     */
    public function reviewNotCompleted($reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);
        $other_review = Review::where('reservation_id',$reservation_id)->where('user_from',$reservation->user_id)->count();
        return ($other_review < 0);
    }

    /**
     * Handle the Review "created" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function created(Review $review)
    {
        if($this->reviewNotCompleted($review->reservation_id)) {
            return false;
        }

    	$hotel = Hotel::find($review->hotel_id);
        $rating = $this->calculateRating($review->hotel_id);
        $hotel->rating = $rating['average'];
        $hotel->total_rating = $rating['total'];
        $hotel->save();
    }

    /**
     * Handle the Review "updated" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function updated(Review $review)
    {
        if($this->reviewNotCompleted($review->reservation_id)) {
            return false;
        }

    	$hotel = Hotel::find($review->hotel_id);
    	$rating = $this->calculateRating($review->hotel_id);
        $hotel->rating = $rating['average'];
        $hotel->total_rating = $rating['total'];
    	$hotel->save();
    }
}
