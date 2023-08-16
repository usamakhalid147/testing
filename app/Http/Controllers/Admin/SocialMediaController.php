<?php

/**
 * Social Media Links Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    SocialMediaController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Lang;

class SocialMediaController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_social_media_links');
        $this->view_data['sub_title']   = Lang::get('admin_messages.social_media_links');
        $this->view_data['active_menu'] = 'social_media_links';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['social_media_links'] = resolve("SocialMediaLink");
        return view('admin.social_media_links.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validateRequest($request);

        $social_media_links = resolve("SocialMediaLink");
        $social_media_links->each(function($media) use ($request) {
            $media->value = $request[$media->name ?? ''];
            $media->save();
        });

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.social_media_links');
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
        $social_media_links = resolve("SocialMediaLink");
        
        $rules = $social_media_links->mapWithKeys(function($media) {
            $rule = [$media->name => 'nullable|url'];
            if($media->name == 'whatsapp') {
                $rule = [$media->name => 'nullable|numeric'];
            }
            return $rule;
        })->toArray();

        $this->validate($request_data,$rules);
    }
}
