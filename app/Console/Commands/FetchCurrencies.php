<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Currency;

class FetchCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch currencies rates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currencies = Currency::all();
        $apikey = config('app.CURRENCY_DATA_API_KEY');

        foreach ($currencies as $currency) {
            if ($currency['code'] == 'VND') {
                continue;
            }
            $id = $currency['id'];
            $curl = curl_init();
            $url = "https://api.apilayer.com/currency_data/convert?to={$currency['code']}&from=VND&amount=1";

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: text/plain",
                    "apikey: $apikey"
                ),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true)['result'];

            // Check if the result is in scientific notation
            if (preg_match('/^[+-]?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?$/', $result)) {
                // Convert scientific notation to normal form
                $result = number_format((float)$result, 8, '.', '');
            }

            // Update the rate column for the specific currency row in the database
            $currency = Currency::find($id);
            $currency->rate = $result;
            $currency->save();
        }


    }
}
