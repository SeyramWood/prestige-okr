<?php

namespace App\Traits;

use App\Contracts\KeyResultInterface;
use App\Models\Objective;
use App\Models\ObjectiveMetric;
use App\Models\TeamUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


/**
 * ObjectiveTrait
 */
trait ObjectiveTrait
{

  public function totalOkrs()
  {

    return Objective::where('user_id', auth()->user()->id)->where('type', 'individual')->count();
  }

  public function totalCompanyOkrs()
  {

    return Objective::where('company_id', auth()->user()->company_id)->where('type', 'organization')->count();
  }

  public function totalTeamOkrs()
  {
    return TeamUser::where('team_users.user_id', Auth::id())
      ->join('team_objectives', 'team_objectives.team_id', 'team_users.team_id')
      ->count();
  }
  public function completedOkrs()
  {
    return Objective::join('objective_metrics', 'objective_metrics.objective_id', 'objectives.id')
      ->where('objectives.company_id', Auth::user()->company_id)
      ->where('objective_metrics.progress', 100)
      ->count();
  }

  public function totalRiskOkrs()
  {

    return ObjectiveMetric::where('progress', '!=<', 'target')->count();
  }


  public function totalNotAtRiskOkrs()
  {

    return ObjectiveMetric::where('progress', '!=', 'target')->count();
  }

  public function updateCheckIn($request, $objectiveMetric, $keyResultMetric, KeyResultInterface $keryResult)
  {
    DB::table('objective_metrics')->where('objective_id', $objectiveMetric)->update(['progress' => $request->objProgress]);
    $keyResultMetric->progress = $request->krProgress;
    $keyResultMetric->status = $request->status;
    $keyResultMetric->save();

    return response()->json(['updated' => true, 'keyResult' => $keryResult->getById($keyResultMetric->key_result_id)]);
  }
}
