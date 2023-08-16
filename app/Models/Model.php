<?php

/**
 * Common Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    Model
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
	/**
	 * Save Model values to database without Trigger any events
	 *
	 */
	public function saveQuietly(array $options = [])
	{
	    return static::withoutEvents(function () use ($options) {
	        return $this->save($options);
	    });
	}
	
	/**
	 * Scope to return Active Records Only
	 *
	 * @param Query Builder $query
	 * @return Query Builder
	 */
	public function scopeActiveOnly($query)
	{
		return $query->whereStatus(1);
	}

	/**
	 * Scope to Get Today Records Only
	 *
	 * @param Query Builder $query
	 * @return Query Builder
	 */
	public function scopeTodayOnly($query)
	{
		return $query->whereDate('created_at',date('Y-m-d'));
	}

	/**
	 * Scope to return Valid Records Only
	 *
	 * @param Query Builder $query
	 * @return Query Builder
	 */
	public function scopeNotDeleted($query)
	{
		return $query->whereNull('deleted_at');
	}

	/**
     * Get Upload File Path
     *
     * @return string
     */
    public function getUploadPath()
    {
        return $this->filePath;
    }

    /**
     * Get Upload Driver
     *
     * @return string
     */
    public function getUploadDriver()
    {
        return $this->attributes['upload_driver'] ?? '0';
    }

	/**
	 * Get Image Name
	 *
	 * @return String
	 */
	public function getImageName()
	{
		return $this->image;
	}

	/**
	 * Get Image Size
	 *
	 * @return String
	 */
	public function getImageSize()
	{
		if(isset($this->imageSize)) {
			return $this->imageSize;
		}
		return ['height' => '','width' => ''];
	}

	/**
	 * Get Current Image Handler
	 *
	 * @return ImageHandler
	 */
	public function getImageHandler()
	{
		$upload_drivers = view()->shared('upload_drivers');
		$driver = $this->attributes['upload_driver'] ?? '0';
		$handler = resolve('App\Services\ImageHandlers\\'.$upload_drivers[$driver].'ImageHandler');
		return $handler;
	}

	/**
	 * Get the Image Url
	 *
	 * @return String ImageUrl
	 */
	public function getImageSrcAttribute()
	{
		if(!$this->id) {
			return NULL;
		}

		$handler = $this->getImageHandler();
		$image_data['name'] = $this->getImageName();
		$image_data['path'] = $this->getUploadPath();
		$image_data['image_size'] = $this->getImageSize();
		$image_data['version_based'] = false;

		return $handler->fetch($image_data);
	}

	/**
	 * Delete Image From Storage
	 *
	 * @return String ImageUrl
	 */
	public function deleteImageFile()
	{
		$handler = $this->getImageHandler();

		$image_data['name'] = $this->getImageName();
		$image_data['target_dir'] = $this->getUploadPath();

		return $handler->destroy($image_data);
	}

	public function list()
    {
        $list_type = $this->list_type;
        
        return $this->$list_type;
    }
}