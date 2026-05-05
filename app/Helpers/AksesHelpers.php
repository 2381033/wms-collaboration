<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AksesHelpers
{
    public static function main_menu()
    {
        $main_menu = [];
        // if (Auth::user()) {
        //     $user = Auth::user()->id;

        //     $main_menu = DB::table('sm_menu_user')->join('sm_menu', 'menu.id', '=', 'menu_user.menu_id')
        //     ->join('users', 'users.id','=', 'menu_user.user_id')
        //     ->select('menu.*', 'menu_user.akses', 'menu_user.tambah', 'menu_user.edit', 'menu_user.hapus', 'menu_user.cetak')
        //     ->where('users.id', $user)
        //     ->where('menu_user.akses', 1)
        //     ->where('menu.level_menu', 'main_menu')->get();
        // }

        return $main_menu;
    }

    public static function sub_menu()
    {
        $sub_menu = [];
        // if (Auth::user()) {
        //     $user = Auth::user()->id;

        //     $sub_menu = DB::table('sm_menu_user')->join('sm_menu', 'menu.id', '=', 'menu_user.menu_id')
        //     ->join('users', 'users.id','=', 'menu_user.user_id')
        //     ->select('menu.*', 'menu_user.akses', 'menu_user.tambah', 'menu_user.edit', 'menu_user.hapus', 'menu_user.cetak')
        //     ->where('users.id', $user)
        //     ->where('menu_user.akses', 1)
        //     ->where('menu.level_menu', 'sub_menu')->get();
        // }

        return $sub_menu;
    }

    public static function menu()
    {
        $user = Auth::user()->id;

        $menu = \App\Menu::from('sm_menu as a')
            ->select('a.*', 'b.akses')
            ->join('sm_menu_user as b', 'a.id', 'b.menu_id')
            ->join('users as c', 'b.user_id', 'c.id')
            ->where('b.user_id', $user)
            ->where('a.parent_id', 0)
            ->where('a.active', 'Yes')
            ->orderby("a.id", "asc")
            ->get();

        $tree = '';
        foreach ($menu as $item) {
            $user = Auth::user()->menu->where('id', $item->id)->first();

            if ($user->pivot->akses == 'Yes' && $user->active == 'Yes') {
                if (count($item->children)) {
                    $tree .= '<li class="drop-down"><a>' . $item->name . '</a>';
                } else {
                    $tree .= '<li><a href="' . url("$item->url") . '">' . $item->name . '</a>';
                }

                if (count($item->children)) {
                    $tree .= AksesHelpers::childView($item);
                }
            }
        }
        $tree .= '</li>';

        echo ($tree);
    }

    public static function childView($menu)
    {
        $html = '<ul>';
        foreach ($menu->children as $arr) {
            $user = Auth::user()->menu->where('id', $arr->id)->first();

            if (isset($user)) {
                if ($user->pivot->akses == 'Yes' && $user->active == 'Yes') {
                    if (count($arr->children)) {
                        $html .= '<li class="drop-down"><a>' . $arr->name . '</a>';
                        $html .= AksesHelpers::childView($arr);
                    } else {
                        $html .= '<li><a href="' . url("$arr->url") . '">' . $arr->name . '</a>';
                        $html .= "</li>";
                    }
                }
            }
        }

        $html .= "</ul>";

        return $html;
    }
}
