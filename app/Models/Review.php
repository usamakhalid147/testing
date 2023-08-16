<?php

/**
 * Review Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    ReviewModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Default values for attributes
     * @var  array an array with attribute as key and default as value
     */
    protected $attributes = [
        'recommend' => 1,
        'rating' => 5,
    ];

    /**
	 * Scope to Get Records Based on current login user
	 *
	 */
    public function scopeAuthUser($query)
    {
        $user_id = getCurrentUserId();
        if(isHost()) {
            $user_id = getHostId();
        }
    	return $query->where(function($query) use($user_id) {
    		$query->where('user_from',$user_id)->orwhere('user_to',$user_id);
    	});
    }

    /**
     * Scope to Get Records Based on userType
     *
     */
    public function scopeUserTypeBased($query, $type)
    {
        return $query->where('review_by',$type);
    }

    /**
     * Scope to Get Active User Reviews Only
     *
     */
    public function scopeActiveUser($query)
    {
        return $query->whereHas('user',function($query) {
            $query->activeOnly();
        });
    }

    /**
     * Join With Guest User Table
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_from');
    }


    /**
     * Join With Hotel Table
     *
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Join With Host User Table
     *
     */
    public function review_user()
    {
        return $this->belongsTo(User::class, 'user_to');
    }

    /**
     * Join With Reservation Table
     *
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Join With Review Photo Table
     *
     */
    public function review_photos()
    {
        return $this->hasMany(ReviewPhoto::class,'review_id','id');
    }

	/**
     * Generate Review Stars HTML
     *
     */
    public function getReviewStars()
    {
        $empty = 5 - $this->rating;
        $result = '';
        for($i=1;$i<=$this->rating;$i++){
            $result = $result.'<i class="icon icon-star me-1"></i>';
        }
        for($i=1;$i<=$empty;$i++){
            $result = $result.'<i class="icon icon-star-empty me-1"></i>';
        }
        return $result;
    }
}
