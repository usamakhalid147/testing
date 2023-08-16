<?php

/**
 * Interface that describe Image Upload
 *
 * @package     HyraHotel
 * @subpackage  Contracts
 * @category    Image Handler
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Contracts;

interface ImageHandleInterface
{
    public function upload($image,Array $image_data);
    public function destroy(Array $image_data);
    public function fetch(Array $image_data);
}