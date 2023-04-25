<?php


namespace App\Services;

use App\Item\PerformerUserItem;
use JsonException;
use App\Http\Resources\NotificationResource;
use App\Item\PerformerServiceItem;
use App\Models\{Review, Task, User, UserCategory, UserView, Category};
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use League\Flysystem\WhitespacePathNormalizer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


class PerformersService
{

    /**
     *
     * Function  service
     * This function gives the list of performers
     * @link https://surfandwork/performers
     * @param $authId
     * @param $search
     * @param string|null $lang
     * @return  PerformerServiceItem
     */
    public function service($authId, $search, ?string $lang = 'uz'): PerformerServiceItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new PerformerServiceItem();
        $item->tasks = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'DESC')
            ->get();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->categories2 = collect($category)->where('parent_id', '!=', null)->all();
        if ((int)setting('admin.PerformerSelfVisible', 1) !== 1) {
            $item->users = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->where('name', 'LIKE', "%{$search}%")
                ->WhereNot('id', $authId)
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        } else {
            $item->users = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->where('name', 'LIKE', "%{$search}%")
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        }

        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        return $item;
    }

    /**
     * Function  performer
     * Returns single performer data by id
     * @link https://surfandwork/performers/id
     * @param $user
     * @param $authId
     * @return  PerformerUserItem
     */
    public function performer($user, $authId): PerformerUserItem
    {
        $item = new PerformerUserItem();
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        $item->review_rating = $user->review_rating;
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->task_count = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get();
        $user_categories = UserCategory::query()->where('user_id', $user->id)->pluck('category_id')->toArray();
        $item->user_category = Category::query()->whereIn('id', $user_categories)->get();
        $value = Carbon::parse($user->created_at)->locale((new CustomService)->getLocale());
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $item->created = "$day  {$value->year}";
        return $item;
    }

    /**
     * user profilining ko'rishlar soni
     * @param $user
     * @return UserView|bool
     */
    public function setView($user): UserView|bool
    {
        if (auth()->check()) {
            $user->performer_views()->where('user_id', auth()->user()->id)->first();

            if (!$user->performer_views()->where('user_id', auth()->user()->id)->first()) {
                $view = new UserView();
                $view->user_id = auth()->user()->id;
                $view->performer_id = $user->id;
                $view->save();
                return $view;
            }
        }
        return false;
    }

}
