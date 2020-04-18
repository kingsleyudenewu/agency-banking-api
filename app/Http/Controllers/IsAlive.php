<?php

namespace App\Http\Controllers;


class IsAlive extends Controller
{
    public function check()
    {
        return [
            'status' => 'ok',
            'message' => 'service is up'
        ];
    }
}
