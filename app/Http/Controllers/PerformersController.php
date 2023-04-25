<?php

namespace App\Http\Controllers;

use App\Services\PerformersService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class PerformersController extends Controller
{

    protected PerformersService $performerService;

    public function __construct()
    {
        $this->performerService = new PerformersService();
    }
    public function service(Request $request): Factory|View|Application
    {
        $lang = Session::get('lang');
        $search = $request->input('search');
        $authId = Auth::id();
        $item = $this->performerService->service($authId,$search,$lang);

       return view('performers/performers',
           [
                'users' => $item->users,
                'tasks' => $item->tasks,
                'top_users' => $item->top_users,
                'categories' => $item->categories,
                'categories2' => $item->categories2,
           ]);
    }

    public function performer(User $user): Factory|View|Application
    {
        (new PerformersService)->setView($user);
        $authId = Auth::id();
        $item = $this->performerService->performer($user, $authId);

        return view('performers/executors-courier',
            [
                'top_users' => $item->top_users,
                'user' => $user,
                'portfolios' => $item->portfolios,
                'goodReviews' => $item->goodReviews,
                'badReviews' => $item->badReviews,
                'review_good' => $item->review_good,
                'review_bad' =>$item->review_bad,
                'review_rating' => $item->review_rating,
                'task_count' => $item->task_count,
                'created' => $item->created,
                'user_category'=>$item->user_category
            ]);
    }

}
