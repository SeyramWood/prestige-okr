<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\Objective;
use Illuminate\Http\Request;
use App\Models\ObjectiveMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ObjectiveInterface;
use App\Traits\Notification;

class ObjectiveRepository implements ObjectiveInterface
{
  use Notification;

  public function create(Request $request)
  {
    try {
      return DB::transaction(function () use ($request) {
        $objective = Objective::create([
          'user_id' => Auth::id(),
          'company_id' => Auth::user()->company_id,
          'title' => $request->title,
          'tags' => $request->tag,
          'description' => $request->description,
          'type' => $request->type,
          'period' => json_encode((object)$request->period),
          'progress' => json_encode((object)$request->progress),
        ]);
        ObjectiveMetric::create([
          'objective_id' => $objective->id,
          'unit' => $request->unit,
        ]);

        if ($request->team) {
          $objective->teamObjectives()->createMany(array_map(
            function ($team) {
              return [
                'team_id' => $team,
              ];
            },
            $request->team
          ));
        }
        return $objective;
      });
    } catch (\Exception $e) {
      return false;
    }
  }
  public function getById($id, $type = null)
  {
    switch ($type) {
      case 'team':
        return $this->getObjectiveByTeam($id);
      default:
        return $this->getObjectiveById($id);
    }
  }
  public function getAll($type = null)
  {
    switch ($type) {
      case 'organization':
        return $this->getOrganizationObjectives();
      case 'team':
        return $this->getTeamObjectives();
      default:
        return $this->getUserObjectives();
    }
  }

  public function update(Request $request, int $id)
  {
    try {
      return DB::transaction(
        function () use ($request, $id) {
          DB::table('objectives')->where('id', $id)->update([
            'title' => $request->title,
            'tags' => $request->tag,
            'description' => $request->description,
            'type' => $request->type,
            'period' => json_encode((object)$request->period),
            'progress' => json_encode((object)$request->progress),
          ]);
          DB::table('objective_metrics')->where('objective_id', $id)->update([
            'unit' => $request->unit,
          ]);
          return $this;
        }
      );
    } catch (\Exception $e) {
      return false;
    }

    return $this;
  }
  public function destroy($id)
  {
    try {
      DB::table('objectives')->where('id', $id)->delete();
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }
  public function notify(Objective $objective)
  {
    return $this->modelActivity(
      $objective,
      Auth::user(),
      [
        'created' => true,
        'objective' => $this->getUserObjectives($objective->id)
      ],
      "New Objective created !!"
    );
  }


  private function getObjectiveByTeam($team)
  {
    return Team::with(
      'objectives',
      'objectives.user.profile',
      'objectives.metric',
      'objectives.keyResults.krMetric',
      'objectives.keyResults.user.profile',

    )
      ->find($team);
  }
  private function getObjectiveById($id)
  {
    return Objective::with(
      'metric',
      'user.profile',
      'keyResults.krMetric',
      'keyResults.user.profile'
    )
      ->find($id);
  }
  private function getUserObjectives()
  {
    return Objective::with(
      'metric',
      'user.profile',
      'keyResults.krMetric',
      'keyResults.user.profile'
    )
      ->where('objectives.type', 'individual')
      ->where('objectives.user_id', Auth::user()->id)
      ->orderByDesc('objectives.id')
      ->get();
  }
  private function getOrganizationObjectives()
  {
    return Objective::with(
      'metric',
      'user.profile',
      'keyResults.krMetric',
      'keyResults.user.profile',
    )
      ->where('objectives.company_id', Auth::user()->company_id)
      ->where('objectives.type', "organization")
      ->orderByDesc('objectives.id')
      ->get();
  }
  private function getTeamObjectives()
  {

    // if (Auth::user()->role->name === 'admin') {
    //   return Objective::with(
    //     'metric',
    //     'user.profile',
    //     'keyResults.krMetric',
    //     'keyResults.user.profile',
    //   )
    //     ->where('objectives.company_id', Auth::user()->company_id)
    //     ->where('objectives.type', "team")
    //     ->where('team_users.user_id', Auth::id())
    //     ->join('team_objectives', 'team_objectives.objective_id', 'objectives.id')
    //     ->join('teams', 'teams.id', 'team_objectives.team_id')
    //     ->join('team_users', 'team_users.user_id', 'team_objectives.team_id')
    //     ->select(
    //       'objectives.*',
    //       'teams.name as team',
    //     )
    //     ->orderByDesc('objectives.id')
    //     ->get();
    // }
    return Objective::with(
      'metric',
      'user.profile',
      'keyResults.krMetric',
      'keyResults.user.profile',
    )
      ->where('objectives.company_id', Auth::user()->company_id)
      ->where('objectives.type', "team")
      ->where('team_users.user_id', Auth::id())
      ->join('team_objectives', 'team_objectives.objective_id', 'objectives.id')
      ->join('teams', 'teams.id', 'team_objectives.team_id')
      ->join('team_users', 'team_users.user_id', 'team_objectives.team_id')
      ->select(
        'objectives.*',
        'teams.name as team',
      )
      ->orderByDesc('objectives.id')
      ->get();
  }
}
