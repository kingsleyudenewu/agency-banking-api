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
       try {
           return Str::after(PPhoneNumber::make($phone, $countryCode)->formatE164(), "+");
       } catch (\Exception $e) {
           return "";
       }

    }
}
