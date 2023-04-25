<div class="lg:col-span-1 col-span-2 sm:w-80 w-72 sm:ml-14 ml-0">
    <div class="mt-16 border p-8 rounded-lg border-gray-300">
        <div>
            <h1 class="font-medium text-2xl">{{__('Исполнитель')}}</h1>
            <p class="text-gray-400">{{__('на SurfAndWork с ')}} {{$created}} {{__('г')}}</p>
        </div>
        <div class="">
            <div class="flex w-full mt-4">
                <div class="flex-initial w-1/4">
                    <i class="fas fa-phone-alt text-white text-2xl bg-yellow-500 py-1 px-2 rounded-lg"></i>
                </div>
                <div class="flex-initial w-3/4">
                    <h2 class="font-medium text-lg">{{__('Телефон')}}</h2>
                    @if($user->is_phone_number_verified)
                        <p>{{__('Подтвержден')}}</p>
                    @else
                        <p>{{__('Не подтвержден')}}</p>
                    @endif
                </div>
            </div>
            <div class="flex w-full mt-4">
                <div class="flex-initial w-1/4">
                    <i class="text-white far fa-envelope text-2xl bg-blue-500 py-1 px-2 rounded-lg"></i>
                </div>
                <div class="flex-initial w-3/4">
                    <h2 class="font-medium text-lg">Email</h2>
                    @if($user->is_email_verified)
                        <p>{{__('Подтвержден')}}</p>
                    @else
                        <p>{{__('Не подтвержден')}}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="mt-8">
        <h1 class="text-3xl font-medium">{{__('Прикрепленные категории')}}</h1>
        <ul>
            @foreach($user_category as $user_cat)
                <li class="mt-2 text-gray-500">
                    <a class="hover:text-red-500 underline underline-offset-4" href="{{route('categories',$user_cat->parent_id ?? 1)}}">
                        {{ $user_cat->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
