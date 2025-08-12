<!-- Navbar -->
@if (Route::has('login'))
    <nav x-data="{ open: false }"
        class="bg-base-200 shadow-sm dark:bg-gray-800 border-b border-black/20 dark:border-gray-700">
        <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8 ">
            <div class="flex justify-between items-center h-16">

                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-application-logo class="h-8 w-auto" />
                    </a>
                </div>
                <div>
                    @auth
                        <x-nav-link href="{{ route('public.books.index') }}" :active="request()->routeIs('public.books.index')">
                            {{ __('Livros') }}
                        </x-nav-link>
                    @endauth
                </div>
                <div class="flex flex-col md:flex-row items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline btn-sm md:btn-md">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm md:btn-md">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm md:btn-md">Register</a>
                        @endif
                    @endauth
                </div>

            </div>
        </div>
    </nav>
@endif