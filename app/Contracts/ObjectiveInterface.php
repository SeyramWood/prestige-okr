<?php

namespace App\Contracts;

use App\Models\Objective;
use Illuminate\Http\Request;

interface ObjectiveInterface
{

  public function create(Request $request);
  public function getById(int $id, string $type = null);
  public function getAll(string $type = null);
  public function update(Request $request, int $id);
  public function destroy(int $id);
  public function notify(Objective $objective);
}
