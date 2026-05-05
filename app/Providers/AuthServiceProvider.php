<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function ($user) {
            $role = $user->role->role_name;

            if (Str::lower($role) == Str::lower('admin')) {
                return true;
            }

            return false;
        });

        Gate::define('isUser', function ($user) {
            $role = $user->role->role_name;

            if ($role == 'User') {
                return true;
            }

            return false;
        });

        Gate::define('isAccess', function ($user) {
            $role = $user->menu()->where("url", "warehouse/inbound")->first();

            if (Str::lower($role) == Str::lower('admin')) {
                return true;
            }

            return false;
        });

        Gate::define('gate-access', function ($user, $menu_name) {
            $userid = $user->id;
            $menu = DB::table("sm_menu_user as a")
                ->select("a.tambah")
                ->join("sm_menu as b", "a.menu_id", "b.id")
                ->where("b.url", "LIKE", "$menu_name%")
                ->where("a.user_id", $userid)
                ->where("b.active", "Yes")
                ->first();

            if ($menu !== null) {
                if ($menu->tambah === "Yes") {
                    return true;
                }
            }

            return false;
        });

        Gate::define('bypass-scan-outbound', function ($user, $menu_name) {
            $userid = $user->id;
            $menu = DB::table("sm_menu_user as a")
                ->select("a.akses")
                ->join("sm_menu as b", "a.menu_id", "b.id")
                ->where("b.url", "LIKE", "$menu_name%")
                ->where("a.user_id", $userid)
                ->where("b.active", "Yes")
                ->first();
            // dd($menu);
            if (!is_null($menu)) {
                if ($menu->akses == 'Yes') {
                    return true;
                }
            }
            return false;
        });

        Gate::define('bypass-scan-inbound', function ($user, $menu_name) {
            $userid = $user->id;
            $menu = DB::table("sm_menu_user as a")
                ->select("a.akses")
                ->join("sm_menu as b", "a.menu_id", "b.id")
                ->where("b.url", "LIKE", "$menu_name%")
                ->where("a.user_id", $userid)
                ->where("b.active", "Yes")
                ->first();
            // dd($menu);
            if (!is_null($menu)) {
                if ($menu->akses == 'Yes') {
                    return true;
                }
            }
            return false;
        });

        Gate::define('button-confirm-export', function ($user, $menu_name) {
            $userid = $user->id;
            $menu = DB::table("sm_menu_user as a")
                ->select("a.akses")
                ->join("sm_menu as b", "a.menu_id", "b.id")
                ->where("b.url", "LIKE", "$menu_name%")
                ->where("a.user_id", $userid)
                ->where("b.active", "Yes")
                ->first();
            // dd($menu);
            if (!is_null($menu)) {
                if ($menu->akses == 'Yes') {
                    return true;
                }
            }
            return false;
        });

        Gate::define('vm-cost-checking', function ($user, $menu_name) {
            $userid = $user->id;
            $menu = DB::table("sm_menu_user as a")
                ->select("a.akses")
                ->join("sm_menu as b", "a.menu_id", "b.id")
                ->where("b.url", "LIKE", "$menu_name%")
                ->where("a.user_id", $userid)
                ->where("b.active", "Yes")
                ->first();
            // dd($menu);
            if (!is_null($menu)) {
                if ($menu->akses == 'Yes') {
                    return true;
                }
            }
            return false;
        });

        // Gate::define('tambah-gate', function($user, $menu_name){
        //     $id = $user->id;
        //     $menu_id = Menu::where("url", "like", "%$menu_name%")->select('id')->first()->id;
        //     $data = Auth::user()->menu->find($menu_id)->pivot->tambah;

        //     if ($data == 1) {
        //         return true;
        //     }

        //     return false;
        // });

        // Gate::define('edit-gate', function($user, $menu_name){
        //     $id = $user->id;
        //     $menu_id = Menu::where("url", "like", "%$menu_name%")->select('id')->first()->id;
        //     $data = Auth::user()->menu->find($menu_id)->pivot->edit;

        //     if ($data == 1) {
        //         return true;
        //     }

        //     return false;
        // });

        // Gate::define('hapus-gate', function($user, $menu_name){
        //     $id = $user->id;
        //     $menu_id = Menu::where("url", "like", "%$menu_name%")->select('id')->first()->id;
        //     $data = Auth::user()->menu->find($menu_id)->pivot->hapus;

        //     if ($data == 1) {
        //         return true;
        //     }

        //     return false;
        // });

        // Gate::define('cetak-gate', function($user, $menu_name){
        //     $id = $user->id;
        //     $menu_id = Menu::where("url", "like", "%$menu_name%")->select('id')->first()->id;
        //     $data = Auth::user()->menu->find($menu_id)->pivot->cetak;

        //     if ($data == 1) {
        //         return true;
        //     }

        //     return false;
        // });
    }
}
