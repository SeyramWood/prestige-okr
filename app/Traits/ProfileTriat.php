<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Profile;
use App\Models\User;
use App\Rules\MatchOldPassword;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Routing\Loader\Configurator\Traits\AddTrait;

/**
 * ProfileTriat
 */
trait ProfileTriat
{
  use AdminTrait, HelperTrait;

  public function saveUserProfile($request)
  {
    $request->validate([
      'firstName' => 'required|string',
      'lastName' => 'required|string',
    ]);
    Profile::where('user_id', Auth::id())->update([
      'first_name' => $request->firstName,
      'last_name' => $request->lastName,
    ]);
    return response()->json(['updated' => true, 'profile' => $this->getProfile()]);
  }
  public function saveUserAvatar($request)
  {
    $request->validate([
      'avatar' => 'required|file|mimes:png,jpg',
    ]);
    Profile::where('user_id', Auth::id())->update([
      'avatar' => $this->processAvatar($request, Auth::user()->profile)
    ]);
    return response()->json(['updated' => true, 'profile' => $this->getProfile()]);
  }
  public function saveCompanyProfile($request)
  {
    if (Auth::user()->can('create', Team::class)) {
      $request->validate([
        'name' => 'required|string',
      ]);
      Company::where('id', Auth::user()->company_id)->update([
        'name' => $request->name
      ]);
      return response()->json(['updated' => true, 'company' => $this->getCompany()]);
    }
    return response()->json(['updated' => false, 'message' => "You are not allowed to update Company information"]);
  }

  public function saveCompanyLogo($request)
  {
    if (Auth::user()->can('create', Team::class)) {
      $request->validate([
        'logo' => 'required|file|mimes:png,jpg',
      ]);
      Company::where('id', Auth::user()->company_id)->update([
        'logo' => $this->processCompanyLogo($request, Auth::user()->company)
      ]);
      return response()->json(['updated' => true, 'company' => $this->getCompany()]);
    }
    return response()->json(['updated' => false, 'message' => "You are not allowed to update Company information"]);
  }

  public function saveUserPassword($request)
  {
    $request->validate([
      'current_password' => ['required', new MatchOldPassword],
      'password' => 'required|confirmed|min:8',
    ]);
    User::find(Auth::user()->id)->update([
      "password" => Hash::make($request->password)
    ]);
    return response()->json(['updated' => true]);
  }
}
