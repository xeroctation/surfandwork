<div class="difficultTask score scores{{$user->id}} w-full m-5 flex mb-10 "
     id="{{$user->id}}">
    <div class=" float-left mr-4">
        <img class="rounded-lg w-24 h-24 border-2 mb-2" src="{{asset("storage/{$user->avatar}")}}" alt="avatar">
        <div class="flex sm:flex-row items-center text-sm">
            <p class="text-black ">{{__('Отзывы:')}}</p>
            <i class="far fa-thumbs-up text-blue-500 ml-1 mb-1"></i>
            <span class="text-gray-800 mr-2 ">{{$user->review_good}}</span>
            <i class="far fa-thumbs-down mt-0.5 text-blue-500"></i>
            <span class="text-gray-800">{{$user->review_bad}}</span>
        </div>
        <div class="flex items-center" id="stars{{$user->id}}">
        </div>
    </div>
    <div class="w-4/5 ">
        <div class="flex sm:flex-row flex-col sm:items-center items-start">
            @if (Auth::check() && Auth::user()->id === $user->id)
                <a href="/profile"
                   class="lg:text-3xl mr-2 text-2xl underline text-blue-500 hover:text-red-500"
                   id="{{$user->id}}">
                    {{$user->name}}
                </a>
            @else
                <a class="user mr-2" href="/performers/{{$user->id}}">
                    <p class="text-2xl underline text-blue-500 performer-page{{$user->id}} hover:text-red-500"
                       id="{{$user->id}}"> {{$user->name}} </p>
                </a>
            @endif
            <div class="flex items-center sm:my-0 my-2">
                @if ($user->is_email_verified && $user->is_phone_number_verified)
                    <div data-tooltip-target="tooltip-animation-verified"
                         class="tooltip-1">
                        <img
                            src="{{asset('images/verify.png')}}"
                            alt="" class="w-10">
                        <div id="tooltip-animation-verified" role="tooltip"
                             class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                            <p class="text-center">
                                {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                            </p>
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                @else
                    <div data-tooltip-target="tooltip-animation-not-verified"
                         class="tooltip-1">
                        <img
                            src="{{asset('images/verify_gray.png') }}"
                            alt="" class="w-10">
                        <div id="tooltip-animation-not-verified" role="tooltip"
                             class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                            <p class="text-center">
                                {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                            </p>
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                @endif
                <div data-tooltip-target="tooltip-animation-many" class="">
                    @if($user->reviews >= 50 && $user->role_id == 2)
                        <img src="{{ asset('images/50.png') }}" alt="" class="w-10">
                    @else
                        <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10">
                    @endif
                    <div id="tooltip-animation-many" role="tooltip"
                         class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                        <p class="text-center">
                            {{__('Более 50 выполненных заданий')}}
                        </p>
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <p class="text-base  leading-0  ">
                {{substr($user->description,0,100)}}
                @if(strlen($user->description) >= 100)
                    ...
                @endif
            </p>
        </div>
    </div>
</div>
