<?php

namespace App\Services;

use Inertia\Inertia;
use App\Traits\Generate;
use App\Contracts\PaymentInterface;
use App\Models\Company;
use DateTime;

class PaystackService implements PaymentInterface
{

  public function pay($request)
  {

    $period = $this->caculatePeriod($request['period']);
    $fields = $this->getFields($request, $period);

    $results = json_decode($this->initializeTransaction($fields));

    if ($results && $results->status) {
      return $results->data->authorization_url;
    }
    return false;
  }
  public function storeSubscription($request)
  {
    $company = Company::with('account')->where('uuid', $request->metadata['company_uuid'])->first();
    if ($company->account->type === 'trial' || $company->account->type === 'grace') {
      $company->account->type = 'paid';
    }
    $company->account->status = 'active';
    $company->account->max_num_members = $request->metadata['max_members'];
    $company->account->expired_at = date('Y-m-d H:i:s', strtotime($request->metadata['period']));
    $company->account->save();
    $company->save();
  }

  private function initializeTransaction($fields)
  {
    $url = config('paystack.payment_url');
    $url = "{$url}/transaction/initialize";
    $fields_string = http_build_query($fields);
    //open connection
    $ch = curl_init();
    $key = config('paystack.secrete_key');
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Authorization: Bearer {$key}",
      "Cache-Control: no-cache",
    ));

    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute post
    $result = curl_exec($ch);
    return $result;
  }

  private function getFields($request, $period)
  {
    $account = $request['company']['account'];
    $max_members = 0;

    if ($request['type'] === 'renew') {
      $max_members = $request['maxMembers'];
    }

    if ($request['type'] === 'upgrade') {
      if ($account['type'] === 'trial') {
        $max_members = $request['maxMembers'];
      } else {
        $max_members = $request['maxMembers'] + $account['max_num_members'];
      }
    }

    return [
      'email' => $request->company['email'],
      'amount' => $this->caculateSubscriptionAmount($request->all(), $period),
      'reference' =>  Generate::referenceCode(10),
      'currency' => "GHS",
      'metadata' => [
        'company' => $request->company['name'],
        'period' => $period,
        'max_members' => $max_members,
        'company_uuid' => $request->company['uuid'],
      ]
    ];
  }

  private function caculateSubscriptionAmount(array $request, $period)
  {
    $unitPrice = 20;
    $account = $request['company']['account'];
    if ($request['type'] === 'renew') {
      return (($request['maxMembers'] * $unitPrice) * $request['period']) * 100;
    }
    if ($request['type'] === 'upgrade' && $account['type'] === 'trial') {
      return (($request['maxMembers'] * $unitPrice) * $request['period']) * 100;
    }
    $newPeriod = new DateTime($this->caculatePeriod($request['period']));
    $oldPeriod = new DateTime(date('Y-m-d H:i:s', strtotime($account['expired_at'])));
    $inter = date_diff($newPeriod, $oldPeriod)->format("%a");
    $diffPeriod = (int)$inter / 30;

    $newAmount = (($request['maxMembers'] * $unitPrice) * $request['period']);

    $oldAmount = (($account['max_num_members'] * $unitPrice) * $diffPeriod);

    return ($newAmount + $oldAmount) * 100;
  }
  private function caculatePeriod(int $period): string
  {
    $days = $period * 30;
    return date('Y-m-d H:i:s', strtotime(now() . " + {$days} days"));
  }
}
