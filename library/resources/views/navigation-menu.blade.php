<nav x-data="{ open: false }"
    class="bg-base-200 shadow-sm dark:bg-gray-800 border-b border-black/20 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8 ">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo and Desktop Links -->
            <div class="flex ">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-application-mark class="h-8 w-auto" />
                    </a>
                </div>
            </div>
            <div>
                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex md:space-x-2 lg:space-x-4 md:ms-10">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('books.index') }}" :active="request()->routeIs('books.index')">
                        {{ __('Livros') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('publishers.index') }}" :active="request()->routeIs('publishers.index')">
                        {{ __('Editoras') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('authors.index') }}" :active="request()->routeIs('authors.index')">
                        {{ __('Autores') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Desktop User Dropdown -->
            <div class="hidden md:flex md:items-center md:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button class="flex text-sm rounded-full focus:outline-none">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                    alt="{{ Auth::user()->name }}" />
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
                        <!-- Account Management -->
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

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

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

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('books.index') }}" :active="request()->routeIs('books.index')">
                {{ __('Livros') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('publishers.index') }}"
                :active="request()->routeIs('publishers.index')">
                {{ __('Editoras') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('authors.index') }}" :active="request()->routeIs('authors.index')">
                {{ __('Autores') }}
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Options -->
        <div class="pt-4 pb-3 border-t border-base-content/10">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-base-content">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-base-content/70">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}"
                        :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>