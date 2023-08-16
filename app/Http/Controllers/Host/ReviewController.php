<?php

/**
 * Review Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    ReviewController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\Host\ReviewsDataTable;
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
        $this->view_data['main_title']  = Lang::get('admin_messages.all_reviews');
        $this->view_data['active_menu'] = 'reviews';
        $this->view_data['sub_title'] = Lang::get('admin_messages.reviews');
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReviewsDataTable $dataTable)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.all_reviews');;
        return $dataTable->render('host.reviews.view',$this->view_data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.guest_review');
        $this->view_data['result'] = Review::findOrFail($id);
        return view('host.reviews.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validate_return = $this->validateRequest($request);

        $review = Review::findOrFail($id);
        $review->public_reply = $request->public_reply;
        $review->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        $redirect_url = route('host.reviews');
        return redirect($redirect_url);
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
            'public_reply'    => 'required',
        );

        $attributes = array(
            'public_reply'  => Lang::get('admin_messages.write_a_reponse'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
