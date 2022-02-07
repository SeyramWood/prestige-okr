<?php

namespace App\Http\Controllers;

use App\Models\TeamUser;
use App\Traits\AdminTrait;
use Illuminate\Http\Request;

class TeamUserController extends Controller
{
    use AdminTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($request->people as $k => $people) {
            foreach ($request->team as $k => $team) {
                $this->assignTeam($people, $team);
            }
        }
        return response()->json(['created' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TeamUser  $teamUser
     * @return \Illuminate\Http\Response
     */
    public function show(TeamUser $teamUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TeamUser  $teamUser
     * @return \Illuminate\Http\Response
     */
    public function edit(TeamUser $teamUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TeamUser  $teamUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeamUser $teamUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TeamUser  $teamUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamUser $teamUser)
    {
        //
    }
}
