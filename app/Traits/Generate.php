<?php

namespace App\Traits;

trait Generate
{
  public static function password($length)
  {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#&%';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
      $rand = rand(0, strlen($characters) - 1);
      $password .= substr($characters, $rand, 1);
    }
    return $password;
  }
  public static function uuid($length)
  {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
      $rand = rand(0, strlen($characters) - 1);
      $id .= substr($characters, $rand, 1);
    }
    return $id;
  }
  public static function uNumber($length)
  {
    $characters = '00123456789';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
      $rand = rand(0, strlen($characters) - 1);
      $id .= substr($characters, $rand, 1);
    }
    return $id;
  }
  public static function referenceCode($length)
  {
    $characters = '00123456789';
    $code = '';
    for ($i = 1; $i <= $length; $i++) {
      $rand = random_int(0, strlen($characters) - 1);
      if (strlen($code) === 3 || strlen($code) === 7) {
        $code  .= '-';
      }
      $code  .= substr($characters, $rand, 1);
    }
    return $code;
  }
  public static function uuid4($data = null): string
  {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }
}
