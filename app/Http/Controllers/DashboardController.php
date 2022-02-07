<?php

namespace App\Http\Controllers;

use App\Traits\AdminTrait;
use App\Traits\ObjectiveTrait;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ObjectiveInterface;


class DashboardController extends Controller
{

    use AdminTrait, ObjectiveTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(ObjectiveInterface $objective)
    {
        return inertia('Dashboard', [
            'organizationObjectives' => fn () =>  $objective->getAll('organization'),
            'teams' =>  fn () => $this->getTeams(),
            'totalCompanyOkrs' => fn () => $this->totalCompanyOkrs(),
            'totalOkrs' =>  fn () => $this->totalOkrs(),
            'totalTeamOkrs' =>  fn () => $this->totalTeamOkrs(),
            'completedOkrs' =>  fn () => $this->completedOkrs(),
            'totalRiskOkrs' =>  fn () => $this->totalRiskOkrs(),
            'totalNotAtRiskOkrs' =>  fn () => $this->totalNotAtRiskOkrs(),
            'notifications' => fn () => $this->getNotifications(),
        ]);
    }

    public function myOkr(ObjectiveInterface $objective)
    {
        return inertia('MyOkr', [
            'userObjectives' =>   fn () => $objective->getAll(),
            'teams' =>  fn () => $this->getTeams(),
            'totalOkrs' =>  fn () => $this->totalOkrs(),
            'totalCompanyOkrs' => fn () => $this->totalCompanyOkrs(),
            'totalRiskOkrs' => fn () => $this->totalRiskOkrs(),
            'totalNotAtRiskOkrs' =>  fn () => $this->totalNotAtRiskOkrs(),
        ]);
    }

    public function people()
    {
        return inertia('People', [
            'people' =>  fn () => $this->getPeople(),
            'teams' =>  fn () => $this->getTeams()
        ]);
    }
    public function companies()
    {
        if (Auth::user()->company->type || (!Auth::user()->company->type && Auth::user()->role->name !== 'admin')) {
            abort(401, 'You are not authourized to access this page');
        }
        return inertia('Companies', [
            'companies' =>  fn () => $this->getAllCompanies(),
        ]);
    }
    public function settings()
    {
        return inertia('Settings/Settings', [
            //
        ]);
    }
    public function teams($team = null, ObjectiveInterface $objective)
    {
        return inertia('Teams', [
            'myTeams' => fn () => $this->myTeams(),
            'teams' => fn () => $this->getTeams(),
            'teamObjectives'  => fn () => $team ? $objective->getById($team, 'team') : $objective->getAll('team'),
        ]);
    }

    public function manageTeam()
    {
        return inertia('Settings/ManageTeam', [
            'teams' =>  fn () => $this->getTeams()
        ]);
    }
    public function profile()
    {
        return inertia('Settings/Profile', [
            'companyInfo' => fn () => $this->getCompany()
        ]);
    }



    public function notifications()
    {
        return response()->json(['notif' => true, 'notifications' => $this->getNotifications()]);
    }
}
