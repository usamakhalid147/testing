<?php

/**
 * Find User Review
 *
 * @package     HyraHotel
 * @subpackage  Mail
 * @category    FindUserReview
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FindUserReview extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * View File for the Email
     *
     * @var string
     **/
    protected $view_file = 'emails.find_user_review';

    /**
     * View Data for the Email
     *
     * @var string
     **/
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view_file)
                    ->subject($this->data['subject'])
                    ->with($this->data);
    }
}
