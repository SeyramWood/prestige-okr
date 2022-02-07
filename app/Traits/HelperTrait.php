<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * HelperTrait
 */
trait HelperTrait
{
  use Generate;

  public function processAvatar($request, $data = null)
  {
    if ($request->has('avatar') && $request->file('avatar') !== null) {
      $image = $request->file('avatar');
      $fileName = Generate::uuid(16) . '.' . $image->extension();
      if ($data) {
        if (Storage::disk('public')->exists("avatar/user/{$data->avatar}")) {
          Storage::disk('public')->delete("avatar/user/{$data->avatar}");
          Storage::putFileAs("public/avatar/user/", $request->file('avatar'), $fileName);
        } else {
          Storage::putFileAs("public/avatar/user/", $request->file('avatar'), $fileName);
        }
      }
      if (!$data) {
        Storage::putFileAs("public/avatar/user/", $request->file('avatar'), $fileName);
      }
      return $fileName;
    } else {
      if ($data && $data->avatar) {
        return $data->avatar;
      }
    }
    return null;
  }
  public function processCompanyLogo($request, $data = null)
  {
    if ($request->has('logo') && $request->file('logo') !== null) {
      $image = $request->file('logo');
      $fileName = Generate::uuid(16) . '.' . $image->extension();
      if ($data) {
        if (Storage::disk('public')->exists("logo/{$data->logo}")) {
          Storage::disk('public')->delete("logo/{$data->logo}");
          Storage::putFileAs("public/logo/", $request->file('logo'), $fileName);
        } else {
          Storage::putFileAs("public/logo/", $request->file('logo'), $fileName);
        }
      }
      if (!$data) {
        Storage::putFileAs("public/logo/", $request->file('logo'), $fileName);
      }
      return $fileName;
    } else {
      if ($data && $data->avatar) {
        return $data->avatar;
      }
    }
    return null;
  }
}
