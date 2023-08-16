<?php

/**
 * Review Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    ReviewController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\ReviewsDataTable;
use App\Models\Review;
use Lang;

class ReviewController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_reviews');
        $this->view_data['active_menu'] = 'reviews';
        $this->view_data['sub_title'] = Lang::get('admin_messages.reviews');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReviewsDataTable $dataTable)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.manage_reviews');;
        
        return $dataTable->render('admin.reviews.view',$this->view_data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_review');
        $this->view_data['result'] = Review::findOrFail($id);
        
        return view('admin.reviews.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->validateRequest($request);

        $review = Review::findOrFail($id);
        $review->public_comment = $request->public_comment;
        $review->rating = $request->rating;
        $review->recommend = $request->recommend;
        $review->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.reviews');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  Illuminate\Http\Request $request_data
     * @param  Int $id
     * @return Array
     */
    protected function validateRequest($request_data, $id = '')
    {
        $rules = array(
            'public_comment' => 'required',
        );

        $attributes = array(
            'public_comment' => Lang::get('admin_messages.public_comment'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
