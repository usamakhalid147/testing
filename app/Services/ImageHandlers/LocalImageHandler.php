<?php

/**
 * Image Upload Handler
 *
 * @package     HyraHotel
 * @subpackage  Services\ImageHandler
 * @category    LocalImageHandler
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\ImageHandlers;

use App\Contracts\ImageHandleInterface;
use File;
use Lang;
use Intervention\Image\Facades\Image;

class LocalImageHandler implements ImageHandleInterface
{
	/**
     * Upload image to storage
     *
     */
	public function upload($image, array $image_data)
	{
		$return_data = [
			'status' => false,
			'status_message' => Lang::get('admin_messages.failed_to_upload_image'),
			'upload_driver' => '0'
		];
		if(!isset($image)) {
			return $return_data;
		}
		
		$tmp_name = $image->getPathName();
		$ext = strtolower($image->getClientOriginalExtension());
		$time = (isset($image_data['add_time']) && $image_data['add_time']) ? time() : '';
		$ext = (isset($image_data['ext']) && $image_data['ext']) ? $image_data['ext'] : $ext;
		$name = $image_data['name_prefix'].$time.'.'.$ext;
		if(isset($image_data['target_path'])) {
			$filename = $image_data['target_path'];
		}
		else {
			$filename = dirname($_SERVER['SCRIPT_FILENAME']).$image_data['target_dir'];
		}

		try {
			if (!file_exists($filename)) {
				mkdir($filename, 0777, true);
			}
			$img = Image::make($tmp_name);

			if ($img->filesize() > 2000000) {
				$img->resize(1920, null, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
			}
			if ($img->save($filename.'/'.$name)) {
				$return_data['status'] = true;
				$return_data['status_message'] = Lang::get('admin_messages.image_uploaded_successfully');
			}
		}
		catch(\Exception $e) {
			return [
				'status' => false,
				'status_message' => Lang::get('admin_messages.failed_to_upload_file'),
				'message' => $e->getMessage(),
			];
		}
		if(isset($image_data['target_path'])) {
			$return_data['src'] = rtrim($image_data['target_path']).'/'.trim($name,'/');
		}
		else {
			$return_data['src'] = rtrim(siteUrl(),'/').'/'.trim($image_data['target_dir'],'/').'/'.trim($name,'/');
		}
		$return_data['file_name'] = $name;
		return $return_data;
	}

	/**
     * Delete image from storage
     *
     */
	public function destroy(Array $image_data)
	{
		if(!DELETE_STORAGE) {
			return [
				'status' => true,
				'status_message' => Lang::get('messages.failed'),
			];
		}
		$image_path = public_path($image_data['target_dir']."/".$image_data['name']);
		
		if(File::exists($image_path)) {
			try {
				File::delete($image_path);
				return [
					'status' => true,
					'status_message' => Lang::get('messages.success'),
				];
			}
			catch (\Exception $e) {
				return [
					'status' => false,
					'status_message' => $e->getMessage(),
				];
			}
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
		$src = rtrim(siteUrl(),'/').'/'.trim($image_data['path'],'/').'/'.trim($image_data['name'],'/');

		if(isset($image_data['version_based']) && $image_data['version_based']) {
			$version = view()->shared('version');
			$src .='?v='.$version;
		}
		return $src;
	}
}