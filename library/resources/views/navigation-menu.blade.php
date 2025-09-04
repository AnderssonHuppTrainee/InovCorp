<nav x-data="{ open: false }"
    class="bg-base-200 shadow-sm dark:bg-gray-800 border-b border-black/20 dark:border-gray-700">
    <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8 ">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-application-logo class="h-8 w-auto" />
                </a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden md:flex md:space-x-2 lg:space-x-4">
                @auth
                    @if(auth()->user()->isAdmin())
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                            {{ __('Utilizadores') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('books.index') }}" :active="request()->routeIs('books.index')">
                            {{ __('Livros') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('authors.index') }}" :active="request()->routeIs('authors.index')">
                            {{ __('Autores') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('publishers.index') }}" :active="request()->routeIs('publishers.index')">
                            {{ __('Editoras') }}
                        </x-nav-link>
                    @else
                        <x-nav-link href="{{ route('public.home') }}" :active="request()->routeIs('public.home')">
                            {{ __('Home') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('public.books.index') }}" :active="request()->routeIs('public.books.index')">
                            {{ __('Catálogo') }}
                        </x-nav-link>
                    @endif
                @else
                    <x-nav-link href="{{ route('public.home') }}" :active="request()->routeIs('public.home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('public.books.index') }}" :active="request()->routeIs('public.books.index')">
                        {{ __('Catálogo') }}
                    </x-nav-link>
                @endauth
            </div>

            <!-- Ações do usuário -->
            <div class="flex items-center space-x-2">
                <!-- Ícone do Carrinho -->
                @auth
                    <div class="relative">
                        <a href="{{ route('cart.index') }}" class="btn btn-ghost btn-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <!-- Contador de itens -->
                            @if($cartCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 bg-primary text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                @endauth
                @auth
                    <!-- Dropdown com foto -->
                    <div class="hidden md:flex">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button class="flex text-sm rounded-full focus:outline-none">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                            src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    </button>
                                @else
                                    <button class="btn btn-ghost">
                                        <span>{{ Auth::user()->name }}</span>
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                @endif
                            </x-slot>
                            <x-slot name="content">
                                <div class="px-4 py-2 text-sm text-base-content/70">
                                    {{ __('Manage Account') }}
                                </div>
                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                        {{ __('API Tokens') }}
                                    </x-dropdown-link>
                                @endif
                                <div class="divider my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <!-- Botões login/registro -->
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm md:btn-md">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm md:btn-md text-white">Register</a>
                    @endif
                @endauth

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="btn btn-ghost btn-square">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index')">
                    {{ __('Carrinho') }}
                    @if($cartCount > 0)
                        <span class="ml-2 bg-primary text-white rounded-full px-2 py-1 text-xs">
                            {{ $cartCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                        {{ __('Utilizadores') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('books.index') }}" :active="request()->routeIs('books.index')">
                        {{ __('Livros') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('authors.index') }}" :active="request()->routeIs('authors.index')">
                        {{ __('Autores') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('publishers.index') }}"
                        :active="request()->routeIs('publishers.index')">
                        {{ __('Editoras') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('public.books.index') }}"
                        :active="request()->routeIs('public.books.index')">
                        {{ __('Catálogo') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link href="{{ route('public.home') }}" :active="request()->routeIs('public.home')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('public.books.index') }}"
                    :active="request()->routeIs('public.books.index')">
                    {{ __('Catálogo') }}
                </x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>