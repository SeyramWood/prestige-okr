<?php

namespace App\Contracts;


use Illuminate\Http\Request;

interface PaymentInterface
{
  public function pay(Request $request);
  public function storeSubscription($request);
}
