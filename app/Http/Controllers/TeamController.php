<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Traits\AdminTrait;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use AdminTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->createTeam($request);
    }

    public function assignTeam(Request $request, $user)
    {
        return $this->createAssignTeam($request, $user);
    }
    
    public function assignTeams(Request $request)
    {
        return $this->createAssignTeams($request);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        return $this->updateTeam($request, $team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['deleted' => true]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyMany($teams)
    {
        Team::where('id', explode(',', $teams))->delete();
        return response()->json(['deleted' => true]);
    }
}
