<?php

namespace App\Koloo;

use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber as PPhoneNumber;



/**
 * Class PhoneNumber
 *
 * @package \App\Koloo
 */
class PhoneNumber
{
    public static function format($phone, $countryCode = 'NG') : string
    {
        $phoneNumberExceptionsNG = ["091"];
        try {
            return Str::after(PPhoneNumber::make($phone, $countryCode)->formatE164(), "+");
        } catch (\Exception $e) {
            $formattedNumber = "";

            switch ($countryCode) {
                case 'NG':
                        if( in_array(substr($phone, 0, 3), $phoneNumberExceptionsNG) ) {
                            $formattedNumber = "234" . substr($phone, 1);
                        
                        break;
            }

            return $formattedNumber;
        }

    }
}
