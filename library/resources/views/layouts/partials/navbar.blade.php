<!-- Navbar -->
@if (Route::has('login'))
    <nav class="flex flex-col md:flex-row justify-between items-center  border-b border-black/20 gap-4">
        <div>
            <x-application-logo />
        </div>
        <div class="flex flex-col md:flex-row items-center gap-2 px-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline btn-sm md:btn-md">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm md:btn-md">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm md:btn-md">Register</a>
                @endif
            @endauth
        </div>
    </nav>
@endif