<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Admin;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;

/**
 * CreateAccountTrait
 */
trait CreateAccountTrait
{

  use Generate;

  public function createAdminUser($request)
  {
    $header = $request->header('step');
    switch ($header) {
      case 'detail':
        $request->validate([
          'companyName' => 'required|string',
          'companyEmail' => 'required|string|email:filter|unique:users,email'
        ]);
        return Redirect::route('register')->with('stepCompleted', true);
      case 'finish':
        $request->validate([
          'username' => 'required|string|min:3,max:10|unique:users,username',
          'password' => ['required', Password::min(8)->mixedCase()]
        ]);
        try {
          DB::transaction(function () use ($request) {
            $company = Company::create([
              'uuid' => Generate::uuid4(),
              'logo' => 'default.png',
              'name' => $request->companyName,
              'email' => $request->companyEmail,
              'type' => "customer",
            ]);

            $user = User::create([
              'company_id' => $company->id,
              'role_id' => Role::where('name', 'admin')->first()->id,
              'username' => $request->username,
              'password' => Hash::make($request->password),
              'email' => $request->companyEmail,
            ]);
            Profile::create([
              'user_id' => $user->id,
            ]);
            $company = CompanyAccount::create([
              'company_id' => $company->id,
              'creator' => $user->id,
              'expired_at' => date('Y-m-d H:i:s', strtotime(now() . ' + 14 days')),
            ]);
          });
          if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->remember ?? false)) {
            $user = User::where('username', $request->username)->first();
            $user->last_active = now();
            $user->status = 1;
            $user->save();
            return Redirect::route('dashboard');
          } else {
            return Redirect::route('register')->withErrors(['loginError' => 'Your credentials don not match our records.']);
          }
        } catch (\Throwable $th) {

          return Redirect::route('register')
            ->withErrors(['accountError' => "We couldn't create your account, please try again."]);
        }
    }
  }
}
