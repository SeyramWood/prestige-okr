<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\User;
use App\Models\Company;
use App\Models\Profile;
use App\Models\Role;
use App\Models\TeamUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Spatie\Activitylog\Models\Activity;

/**
 * AdminTrait
 */
trait AdminTrait
{
  public function getProfile()
  {
    return User::where('users.id', Auth::id())
      ->join('profiles', 'profiles.user_id', '=', 'users.id')
      ->select('users.*', 'profiles.avatar', 'profiles.first_name', 'profiles.last_name')
      ->first();
  }
  public function getPeople()
  {
    return User::where('company_id', Auth::user()->company_id)
      ->join('profiles', 'profiles.user_id', '=', 'users.id')
      ->join('roles', 'roles.id', '=', 'users.role_id')
      ->select(
        'users.*',
        'profiles.avatar',
        'profiles.first_name',
        'profiles.last_name',
        'roles.name as role'
      )
      ->orderByDesc('created_at')
      ->get();
  }
  public function myTeams($id = null)
  {
    if ($id) {
      return User::with('teams')->find($id);
    }
    return User::with(['teams' => function ($query) {
      $query->select('teams.id', 'teams.name', 'teams.description', 'teams.created_at');
    }])
      ->select('users.id')
      ->find(Auth::id());
  }
  public function getTeams($id = null)
  {
    if ($id) {
      return Team::find($id);
    }
    return Team::where('company_id', Auth::user()->company_id)->orderByDesc('created_at')->get();
  }
  public function getCompany($id = null)
  {
    if ($id) {
      return Company::where('id', $id)->first();
    }
    if (Auth::check()) {
      return Company::where('id', Auth::user()->company_id)->first();
    }
    return null;
  }
  public function getAllCompanies($id = null)
  {
    if ($id) {
      return Company::with('account')
        ->where('id', $id)
        ->whereNotNull('type')
        ->first();
    }
    return Company::with('account')->whereNotNull('type')->get();
  }

  public function createTeam($request)
  {
    if (Auth::user()->can('create', Team::class)) {
      $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
      ]);
      $team = Team::create([
        'user_id' => Auth::id(),
        'company_id' => Auth::user()->company_id,
        'name' => $request->name,
        'description' => $request->description,
      ]);
      activity()
        ->performedOn($team)
        ->causedBy(Auth::user())
        ->withProperties(['created' => true, 'team' => $this->getTeams($team->id)])
        ->log("New Team created !!");

      $lastLoggedActivity = Activity::all()->last();
      return response()->json(['created' => true, 'team' => $this->getTeams($team->id), 'notification' => $lastLoggedActivity]);
    }
    return response()->json(['created' => false, 'message' => "You are not allowed to create Team"]);
  }
  public function createAssignTeam($request, $user)
  {
    if (Auth::user()->can('assignUserToTeam', Team::class)) {
      $request->validate([
        'teams' => 'required',
      ]);
      foreach ($request->teams as  $team) {
        TeamUser::firstOrCreate(
          [
            'user_id' => $user,
            'team_id' => $team,
          ],
        );
        activity()
          ->performedOn(Team::find($team))
          ->causedBy(Auth::user())
          ->withProperties(['user' => User::find($user), 'team' => $this->getTeams($team)])
          ->log(User::find($user)->username . " assigned to " . Team::find($team)->name);
        $lastLoggedActivity = Activity::all()->last();
      }
      return Redirect::route('people')->with(['notification' => $lastLoggedActivity]);
    }
    return Redirect::route('people')->withErrors([
      "assign" => "You are not allowed to assign user to team"
    ]);
  }
  public function createAssignTeams($request)
  {
    if (Auth::user()->can('assignUserToTeam', Team::class)) {
      $request->validate([
        'teams' => 'required',
      ]);
      foreach ($request->teams as  $team) {
        foreach ($request->users as $user) {
          TeamUser::firstOrCreate(
            [
              'user_id' => $user,
              'team_id' => $team,
            ],
          );
          activity()
            ->performedOn(Team::find($team))
            ->causedBy(Auth::user())
            ->withProperties(['user' => User::find($user), 'team' => $this->getTeams($team)])
            ->log(User::find($user)->username . " assigned to " . Team::find($team)->name);
          $lastLoggedActivity = Activity::all()->last();
        }
      }
      return Redirect::route('people')->with(['notification' => $lastLoggedActivity]);
    }
    return Redirect::route('people')->withErrors([
      "assign" => "You are not allowed to assign user to team"
    ]);
  }

  public function updateTeam($request, $team)
  {
    $request->validate([
      'name' => 'required|string',
      'description' => 'nullable|string',
    ]);

    $team->name = $request->name;
    $team->description = $request->description;
    $team->save();

    return response()->json(['updated' => true, 'team' => $this->getTeams($team->id)]);
  }
  public function saveInvite($request)
  {
    $request->validate([
      'username' => 'required|string',
      'email' => 'required|string|email|unique:users,email',
      'password' => 'required',
    ]);
    $user = User::create([
      'role_id' => Role::where('name', 'invited')->first()->id,
      'company_id' => User::find($request->admin)->company_id,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);
    Profile::create(['user_id' => $user->id]);
    return Redirect::route('login');
  }
  public function getNotifications()
  {
    return Activity::orderByDesc('created_at')->get();
  }
}
