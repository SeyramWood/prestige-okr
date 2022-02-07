<?php

namespace App\Contracts;

use App\Models\KeyResult;
use Illuminate\Http\Request;

interface KeyResultInterface
{

  public function create(Request $request);
  public function getById(int $id);
  public function update(Request $request, int $id);
  public function destroy(int $id, int $objectiveId);
  public function notify(KeyResult $keyResult);
}
