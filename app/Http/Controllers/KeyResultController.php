<?php

namespace App\Http\Controllers;

use App\Contracts\KeyResultInterface;
use App\Models\KeyResult;
use Illuminate\Http\Request;
use App\Http\Requests\KeyResultRequest;

class KeyResultController extends Controller
{
    private $keyResult;

    public function __construct(KeyResultInterface $keyResult)
    {
        $this->keyResult = $keyResult;
    }

    public function addKeyResult(KeyResultRequest $request)
    {
        if ($kr = $this->keyResult->create($request)) {
            return response()->json([
                'created' => true,
                'keyResult' => $this->keyResult->getById($kr->id),
                'notification' => $this->keyResult->notify($kr)
            ]);
        }
        return response()->json(
            [
                'insertionFailed' => 'Key Result could not be created, please refresh and try again.'
            ],
            422
        );
    }
    public function editKeyResult(KeyResultRequest $request, $keyResult)
    {
        if ($kr = $this->keyResult->update($request, $keyResult)) {
            return response()->json([
                'updated' => true,
                'keyResult' => $kr->getById($keyResult)
            ]);
        }
        return response()->json(
            [
                'updateFailed' => 'Key Result could not be updated, please refresh and try again.'
            ],
            422
        );
    }

    public function deleteKeyResult(KeyResult $keyResult)
    {
        if ($this->keyResult->destroy($keyResult->id, $keyResult->objective_id)) {
            return response()->json(['deleted' => true]);
        }
        return response()->json(
            [
                'deletionFailed' => 'Key Result could not be deleted, please refresh and try again.'
            ],
            422
        );
    }
}
