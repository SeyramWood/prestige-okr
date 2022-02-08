<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\CreateAccountTrait;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvitePeopleNotification;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{
    use CreateAccountTrait;

    public function store(Request $request)
    {
        return $this->createAdminUser($request);
    }

    public function sendInvite(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);
        $recipients = explode(' ', $request->email);
        $mgs = count($recipients) > 1 ?? 's';
        try {
            foreach ($recipients as $recipient) {
                $url = URL::signedRoute('invite', ['email' => $recipient, 'admin' => Auth::id()]);
                Notification::route('mail', $recipient)
                    ->notify(new InvitePeopleNotification($url));
                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['intivation' => true, 'to' => $recipient])
                    ->log("" . Auth::user()->username . " send invite to {$recipient}");
            }
            return Redirect::route('people')->with(['notification' => Activity::all()->last()]);
        } catch (\Throwable $th) {
            return Redirect::route('people')->withErrors([
                "invite" => "Ooops! We couldn't send your invite{$mgs}, please refresh and try again"
            ]);
        }
    }
    public function updateUserRole(User $user, $role)
    {
        $new_role = Role::whereNotIn('id', [$role])->first();
        $user->role_id = $new_role->id;
        $user->save();
        return response()->json(['updated' => true, 'role' => $new_role]);
    }
    public function updateCompanyAccountStatus(CompanyAccount $companyAccount, $status)
    {
        $companyAccount->status = $status;
        $companyAccount->save();
        return response()->json(['updated' => true]);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return response()->json(['deleted' => true]);
    }
    public function deleteUsers($users)
    {
        User::whereIn('id', explode(',', $users))->delete();
        return response()->json(['deleted' => true]);
    }
    public function deleteCompanyAccount(Company $company)
    {
        $company->delete();
        return response()->json(['deleted' => true]);
    }
    public function deleteCompanyAccounts($companies)
    {
        Company::whereIn('id', explode(',', $companies))->delete();
        return response()->json(['deleted' => true]);
    }
}
