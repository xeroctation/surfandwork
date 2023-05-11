<div class="w-full flex flex-col sm:flex-row sm:p-6 p-2">
    <div class="w-full text-center">
        @php
            $walletBalance = App\Services\Profile\ProfileService::walletBalance(auth()->user());
        @endphp
        @auth
            @if($walletBalance >= setting('admin.pullik_otklik'))
                @if($task->user_id !== auth()->id() && $task->status < 3 && !$auth_response)
                    <form class="space-y-6 px-6 lg:px-8 pb-4 sm:pb-6 xl:pb-8" action="{{route("task.response.store", $task->id)}}" method="post">
                        @csrf
                        <header>
                            <h2 class="font-semibold text-3xl mb-4 text-center">{{__('Откликнуться')}}</h2>
                        </header>
                        <main>
                            <span class="text-base">{{__('Описание отклика')}}</span>
                            <textarea required
                                      class="resize-none rounded-md w-full focus:outline-none  focus:border-yellow-500 border border-gray-300 p-4  transition duration-200 mb-4"
                                      type="text" id="form8" rows="4" name="description"></textarea>
                            <p class="text-base">{{__('Сколько вы предлагаете')}}</p>
                            <label>
                                <input type="text" required onkeypress='validate(event)' id="task_price"
                                       class="border border-gray-300 rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 mb-2">UZS
                                <input type="hidden" name="price" id="price">
                                <input type="hidden" name="task_user_id"
                                       class="pays border rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 my-2"
                                       value="{{$task->user_id}}">
                            </label>
                            <hr>
                        </main>
                        <input type="text" class="hidden" id="not_free" name="not_free">
                        <footer
                            class="flex justify-center bg-transparent">
                            <button type="submit"
                                    class=" bg-yellow-500 font-semibold text-white py-3 w-full rounded-md my-4 hover:bg-orange-500 focus:outline-none shadow-lg hover:shadow-none transition-all duration-300">
                                {{__('Далее')}}
                            </button>
                        </footer>

                    </form>
                @endif
            @elseif($walletBalance < setting('admin.pullik_otklik') )
                @if($task->user_id !== auth()->id() && $task->status < 3 && !$auth_response)
                    <a class="open-modal" data-modal="#modal1">
                        <button disabled class="sm:w-4/5 w-full font-sans text-lg pay font-semibold bg-green-500 text-white hover:bg-green-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300">
                            Unavailable
                        </button>
                    </a>
                @endif
            @endif
        @else
            <a href="/login">
                <button class="sm:w-4/5 w-full mx-auto font-sans mt-8 text-lg  font-semibold bg-yellow-500 text-white hover:bg-orange-500 px-10 py-4 rounded-lg">
                    {{__('Откликнуться на это задание')}}
                </button>
            </a>
        @endauth
        @auth
            @if($task->status === 3 && $task->user_id === auth()->user()->id)
                <div class="flex sm:flex-row flex-col w-11/12 mx-auto">
                    <button onclick="toggleModal4()"
                        class="text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-10 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                        type="submit">
                        {{__('Задание выполнено')}}
                    </button>
                    <button onclick="toggleModal45()"
                            class="text-lg font-semibold bg-red-500 text-white hover:bg-red-400 px-5 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                            type="submit">
                        {{__('Задание не выполнено')}}
                    </button>
                </div>
            @endif

            @if($task->status === 4 && $task->performer_id === auth()->user()->id && !$task->performer_review)
                <div class="flex sm:flex-row flex-col w-11/12 mx-auto">
                    <button onclick="toggleModal4()"
                        class="text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-10 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                        type="submit">
                        {{__('Задание выполнено')}}
                    </button>
                    <button onclick="toggleModal45()"
                            class="text-lg font-semibold bg-red-500 text-white hover:bg-red-400 px-5 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                            type="submit">
                        {{__('Задание не выполнено')}}
                    </button>
                </div>
            @endif
        @endauth
    </div>
</div>
