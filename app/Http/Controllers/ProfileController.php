<?php

namespace App\Http\Controllers;

use App\Traits\ProfileTriat;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ProfileTriat;

    public function updateUserProfile(Request $request)
    {
        return $this->saveUserProfile($request);
    }
    public function updateUserAvatar(Request $request)
    {
        return $this->saveUserAvatar($request);
    }
    public function updateUserPassword(Request $request)
    {
        return $this->saveUserPassword($request);
    }
    public function updateCompanyProfile(Request $request)
    {
        return $this->saveCompanyProfile($request);
    }
    public function updateCompanyLogo(Request $request)
    {
        return $this->saveCompanyLogo($request);
    }
}
