<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\CronController@updatePaypalPayouts')->twiceDaily();
        $schedule->call('App\Http\Controllers\CronController@updateCurrencyRate')->daily();
        $schedule->call('App\Http\Controllers\CronController@updateExpiredStatus')->hourly();
        $schedule->call('App\Http\Controllers\CronController@updateUserStatus')->daily();
        $schedule->call('App\Http\Controllers\CronController@reviewRemainder')->daily();
        $schedule->call('App\Http\Controllers\CronController@referralCredit')->daily();
        $schedule->call('App\Http\Controllers\CronController@sendAdminReport')->daily();
        $schedule->command('queue:work --tries=3 --once')->cron('* * * * *');
        
        if(global_settings('auto_payout')) {
            $schedule->call('App\Http\Controllers\CronController@autoPayout')->daily();
        }
        $backup_period = global_settings('backup_period');
        if($backup_period != 'never') {
            $schedule->command('backup:run --only-db')->$backup_period();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
