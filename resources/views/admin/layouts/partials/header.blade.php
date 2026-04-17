<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-4 md:px-6 py-4">
        <div class="flex items-center space-x-4">
            <!-- Hamburger Menu (Mobile) -->
            <button id="sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-800 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>

            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h2>
                @if(isset($breadcrumbs))
                    <nav class="text-xs md:text-sm text-gray-600 mt-1 hidden sm:block">
                        @foreach($breadcrumbs as $label => $url)
                            @if($loop->last)
                                <span class="text-gray-400">{{ $label }}</span>
                            @else
                                <a href="{{ $url }}" class="hover:text-accent-600">{{ $label }}</a>
                                <span class="mx-2">/</span>
                            @endif
                        @endforeach
                    </nav>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-xs md:text-sm text-gray-600 hidden sm:inline-flex items-center">
                <i class="far fa-calendar mr-2"></i>
                <span class="hidden md:inline">{{ now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</span>
                <span class="md:hidden">{{ now()->format('d/m/Y') }}</span>
            </span>
        </div>
    </div>
</header>
