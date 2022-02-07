<?php

namespace App\Contracts;

use App\Models\Objective;
use Illuminate\Http\Request;

interface FileInterface
{
  public function create(Request $request);
}
