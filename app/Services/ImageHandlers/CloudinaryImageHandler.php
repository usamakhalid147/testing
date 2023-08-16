<?php

/**
 * Image Upload Handler
 *
 * @package     HyraHotel
 * @subpackage  Services\ImageHandler
 * @category    CloudinaryImageHandler
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\ImageHandlers;

use App\Contracts\ImageHandleInterface;
use File;
use Lang;
use LaravelCloudinary;

class CloudinaryImageHandler implements ImageHandleInterface
{
	/**
     * Upload image to storage
     *
     */
	protected function uploadToCloudinary($file_path, $resource_type = "image")
	{
		 try {
            if($resource_type == "video") {
                LaravelCloudinary::uploadVideo($file_path);
            }
            else {
                LaravelCloudinary::upload($file_path);
            }

            return [
            	'status' => true,
            	'file_name' => LaravelCloudinary::getPublicId(),
            ];
        }
        catch (\Exception $e) {
            return [
            	'status' => false,
            	'status_message' => $e->getMessage(),
            ];
        }
	}

    /**
     * Upload image to storage
     *
     */
	public function upload($image, Array $image_data)
	{
		$return_data = array(
			'status' => false,
			'status_message' => Lang::get('admin_messages.failed_to_upload_image'),
			'upload_driver' => '1'
		);
		if(!isset($image)) {
			return $return_data;
		}

		$upload_result = $this->uploadToCloudinary($image->getPathName());
		
		if($upload_result['status']) {
			$return_data['status'] = true;
			$return_data['file_name'] = $upload_result['file_name'];
			$return_data['src'] = LaravelCloudinary::show($upload_result['file_name'],[]);
			$return_data['status_message'] = Lang::get('admin_messages.image_uploaded_successfully');
			return $return_data;
		}

		$return_data['status_message'] = $upload_result['status_message'];
		return $return_data;
	}

	/**
     * Delete image from storage
     *
     */
	public function destroy(Array $image_data)
	{
		try {
			if(isset($image_data['name']) && $image_data['name'] != '') {
				LaravelCloudinary::destroy($image_data['name']);
				return [
					'status' => true,
					'status_message' => Lang::get('admin_messages.successfully_deleted'),
				];
			}
		}
		catch (\Exception $e) {
			return [
				'status' => false,
				'status_message' => $e->getMessage(),
			];
		}
		return [
			'status' => false,
			'status_message' => Lang::get('messages.failed'),
		];
	}

	/**
     * Fetch the image based on driver
     *
     */
	public function fetch(Array $image_data)
	{
		if(!isset($image_data['options'])) {
			$image_data['options'] = [];
		}
		if(isset($image_data['image_size'])) {
			$image_data['options'] = array_merge($image_data['options'],$image_data['image_size']);
		}
        return LaravelCloudinary::show($image_data['name'],$image_data['options']);
	}
}