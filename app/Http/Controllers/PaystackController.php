<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentInterface;
use Illuminate\Http\Request;

class PaystackController extends Controller
{
    private $paystack;

    public function __construct(PaymentInterface $paystack)
    {
        $this->paystack = $paystack;
    }
    public function upgradeSubscription(Request $request)
    {
        $request->validate([
            'maxMembers' => 'required|numeric|min:1',
            'period' => 'required|numeric',
        ]);

        if ($url = $this->paystack->pay($request)) {
            return response()->json(['url' => $url]);
        };
        return response()->json(['failed' => true]);
    }
    public function saveSubscription(Request $request)
    {


        if ($request->event === 'charge.success') {
            $this->paystack->storeSubscription((object)$request->data);
        }


        return response()->json(['failed' => true]);
    }
}
