<?php

namespace App\Http\Controllers;

use App\Services\ControllerService;
use App\Services\HomeService;
use Illuminate\Support\Facades\Session;

class HomeController
{
    public function home(){
        $service = new HomeService();
        $item = $service->home();
        return view('home',
            [
                'categories' => $item->categories,
                'tasks' => $item->tasks,
                'child_categories' => $item->child_categories,
            ]
        );
    }

    public function lang($lang)
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return redirect()->back();
    }

    public function category($id)
    {
        $service = new HomeService();
        $item = $service->category($id);
        return view('task/choosetasks',
            [
                'child_categories' => $item->child_categories,
                'categories' => $item->categories,
                'choosed_category' => $item->choosed_category,
                'idR' => $item->idR,
            ]);
    }
}
