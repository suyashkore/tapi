<?php

namespace App\Feature\Shared\Tests\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feature\Shared\Services\SmsSender;


class TestController extends Controller
{
    protected $smsSender;

    public function __construct(SmsSender $smsSender)
    {
        $this->smsSender = $smsSender;
    }

    public function sendSms(Request $request)
    {
        $mobileNumber = $request->input('mobile');
        $message = $request->input('message');

        try {
            $result = $this->smsSender->sendSms($mobileNumber, $message);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }
}
