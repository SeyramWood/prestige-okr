<?php

namespace App\Http\Controllers;

use App\Contracts\KeyResultInterface;
use App\Contracts\ObjectiveInterface;
use App\Http\Requests\ObjectiveRequest;
use App\Models\KeyResultMetric;
use Illuminate\Http\Request;
use App\Traits\ObjectiveTrait;

class ObjectiveController extends Controller
{
    use ObjectiveTrait;

    private $objective;

    public function __construct(ObjectiveInterface $objective)
    {
        $this->objective = $objective;
    }

    public function addObjective(ObjectiveRequest $request)
    {
        if ($obj = $this->objective->create($request)) {
            return response()->json([
                'created' => true,
                'objective' => $this->objective->getById($obj->id),
                'notification' => $this->objective->notify($obj)
            ]);
        }
        return response()->json(
            [
                'insertionFailed' => 'Objective could not be created, please refresh and try again.'
            ],
            422
        );
    }

    public function editObjective(ObjectiveRequest $request, $id)
    {
        if ($obj = $this->objective->update($request, $id)) {
            return response()->json([
                'updated' => true,
                'objective' => $obj->getById($id)
            ]);
        }
        return response()->json(
            [
                'updateFailed' => 'Objective could not be updated, please refresh and try again.'
            ],
            422
        );
    }

    public function deleteObjective($objective)
    {
        if ($this->objective->destroy($objective)) {
            return response()->json(['deleted' => true]);
        }
        return response()->json(
            [
                'deletionFailed' => 'Objective could not be deleted, please refresh and try again.'
            ],
            422
        );
    }

    public function saveCheckIn(Request $request, $objectiveMetric,  $keyResultMetric,  KeyResultInterface $keryResult)
    {
        return $this->updateCheckIn($request, $objectiveMetric, KeyResultMetric::where('key_result_id', $keyResultMetric)->first(), $keryResult);
    }
}
