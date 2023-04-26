@if(!session()->has('not-show'))

    @auth()
        @if((!auth()->user()->is_email_verified || auth()->user()->is_email_verified == 0) && (auth()->user()->email))
            <div x-data="{ showModal : true }" class="">

                <!-- Modal Background -->
                <div x-show="showModal"
                     class="fixed flex items-center justify-center overflow-auto z-100 bg-black bg-opacity-50 left-0 right-0 top-0 bottom-0"
                     style="z-index: 101;!important;"
                     x-transition:enter="transition ease duration-300" x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100" x-transition:leave="transition ease duration-300"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">


                    <!-- Modal -->
                    <div x-show="showModal" class="bg-white rounded-xl shadow-2xl p-6 sm:w-10/12 lg:w-5/12 mx-10"
                         @click.away="showModal = false" x-transition:enter="transition ease duration-100 transform"
                         x-transition:enter-start="opacity-0 scale-90 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease duration-100 transform"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-90 translate-y-1">
                        <div class="mx-auto pl-5 py-5 text-black">
                            <div class="text-right -mt-5 ">
                                <button @click="showModal = !showModal"
                                        class="px-4 py-2 text-sm bg-white rounded-xl border transition-colors duration-150 ease-linear border-gray-200 text-gray-500 focus:outline-none focus:ring-0 font-bold hover:bg-gray-50 focus:bg-indigo-50 focus:text-indigo">
                                    x
                                </button>
                            </div>


                            <div class="text-lg py-5 sm:text-2xl  -mt-3 font-bold">
                                {{__('Подтвердите адрес вашей почты')}}
                            </div>
                            <p class="text-sm sm:text-xl sm:my-8 xl:my-2 text-gray-700 ">
                                {{__('На ваш электронный адрес')}} <strong>{{auth()->user()->email}}</strong>
                                {{__('было отправлено письмо со ссылкой для подтверждения вашей почты на SurfAndWork.')}}
                            </p>
                            <p class="text-sm sm:text-xl my-2 sm:my-8 text-gray-700 ">
                                {{__('Пройдите по ссылке и активируйте вашу электронную почту.')}}
                            </p>

                            <a class='text-sm sm:text-xl text-yellow-500 hover:text-red-600 send-email border-b sent-email border-dotted border-gray-700 cursor-pointer'
                                href="{{route('login.send_email_verification')}}">
                                {{__('Отправить новое письмо для подтверждения почты')}}
                            </a><br>

                        </div>

                    </div>
                </div>
            </div>

        @endif

    @endauth

    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
    <script src="{{ asset('js/components/modal.js') }}"></script>
@endif
