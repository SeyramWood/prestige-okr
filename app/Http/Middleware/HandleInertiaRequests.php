<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use App\Traits\AdminTrait;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    use AdminTrait;
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {

        return array_merge(parent::share($request), [
            'flash' => [
                'stepCompleted'  => $request->session()->get('stepCompleted') ?? null
            ],
            'authProfile' =>  $this->getProfile() ?? null,
            'companyInfo' =>  $this->getCompany() ?? null,
            'role' => $request->user()->role->name ?? null,
            'accountType' => $request->user()->company->type ?? null
        ]);
    }
}
