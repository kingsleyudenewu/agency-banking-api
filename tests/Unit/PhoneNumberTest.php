<?php

namespace Tests\Unit;

use App\Koloo\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{

    /** @test */
    public function format_default_phone_number_to_nigeria_number()
    {
        $given  = '08066100671';
        $expected = '2348066100671';

        $res = PhoneNumber::format($given);

        $this->assertEquals($expected, $res);
    }

    /** @test */
    public function format_phone_number_given_country_code()
    {
        $given  = '4045782353‬';
        $expected = '14045782353';

        $res = PhoneNumber::format($given, 'US');

        $this->assertEquals($expected, $res);
    }

    /** @test */
    public function return_empty_string_for_invalid_phone_number()
    {
        $given  = '40  23 243‬';
        $expected = '';

        $res = PhoneNumber::format($given);

        $this->assertEquals($expected, $res);
    }
}
