<?php

namespace App\Http\Controllers;

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
}
