<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\KeyResult;
use App\Models\Objective;
use App\Models\KeyResultMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Contracts\KeyResultInterface;
use App\Traits\Notification;

class KeyResultRepository implements KeyResultInterface
{

  use Notification;

  public function create($request)
  {
    try {
      return DB::transaction(function () use ($request) {
        $keyResult = KeyResult::create([
          'objective_id' => $request->objectiveId,
          'user_id' => Auth::id(),
          'metric' => $request->metric,
          'title' => $request->title,
          'tags' => $request->tag,
          'description' => $request->description,
          'type' => $request->type,
          'period' => json_encode((object)$request->period),
          'progress' => json_encode((object)$request->progress),
        ]);

        KeyResultMetric::create([
          'key_result_id' => $keyResult->id,
          'target' => $request->target,
          'start' => $request->starting,
        ]);

        DB::table('objective_metrics')->where('objective_id', $keyResult->objective_id)->increment('target', $request->target);

        return $keyResult;
      });
    } catch (\Exception $e) {
      return false;
    }
  }
  public function getById(int $id, string $type = null)
  {
    return KeyResult::with('user.profile', 'krMetric')->find($id);
  }

  public function update($request, int $id)
  {
    try {
      return DB::transaction(
        function () use ($request, $id) {
          DB::table('key_results')->where('id', $id)->update([
            'metric' => $request->metric,
            'title' => $request->title,
            'tags' => $request->tag,
            'description' => $request->description,
            'type' => $request->type,
            'period' => json_encode((object)$request->period),
            'progress' => json_encode((object)$request->progress),
          ]);
          DB::table('key_result_metrics')->where('key_result_id', $id)->update([
            'target' => $request->target,
            'start' => $request->starting,
          ]);
          if ($request->target < $request->prevTarget) {
            $target = $request->prevTarget - $request->target;
            DB::table('objective_metrics')->where('objective_id', $request->objectiveId)->decrement('target', $target);
          }
          if ($request->target > $request->prevTarget) {
            $target = $request->target - $request->prevTarget;
            DB::table('objective_metrics')->where('objective_id', $request->objectiveId)->increment('target', $target);
          }
          return $this;
        }
      );
    } catch (\Exception $e) {
      return false;
    }
  }
  public function destroy($id, $objectiveId)
  {
    try {
      return DB::transaction(
        function () use ($id, $objectiveId) {

          $krm = DB::table('key_result_metrics')->where('id', $id)->first();
          $objm = DB::table('objective_metrics')->where('objective_id', $objectiveId)->first();

          $newTarget = $objm->target - $krm->target;

          DB::table('objective_metrics')
            ->where('objective_id', $objectiveId)
            ->update([
              'target' => (int)($newTarget),
              'progress' => (int)((($objm->progress / $newTarget) * 100))
            ]);

          DB::table('key_results')->where('id', $id)->delete();

          return true;
        }
      );
    } catch (\Exception $e) {
      return false;
    }
  }
  public function notify(KeyResult $keyResult)
  {
    return $this->modelActivity(
      $keyResult,
      Auth::user(),
      [
        'created' => true,
        'keyResult' => $this->getById($keyResult->id)
      ],
      "New Key Result created !!"
    );
  }
}
