<aside id="admin-sidebar" class="fixed lg:static inset-y-0 left-0 w-64 bg-gradient-to-b from-gray-800 to-gray-900 text-white flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <!-- Logo + Close Button -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-12 mx-auto">
            <!-- Close button (solo mobile) -->
            <button id="sidebar-close" class="lg:hidden text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <h1 class="text-lg font-bold text-accent-400 text-center">
            Admin Panel
        </h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white border-l-4 border-accent-400' : '' }}">
            <i class="fas fa-home w-6"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('admin.organizadores.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('admin.organizadores.*') ? 'bg-gray-700 text-white border-l-4 border-accent-400' : '' }}">
            <i class="fas fa-users w-6"></i>
            <span class="ml-3">Organizadores</span>
        </a>

        <a href="{{ route('admin.torneos.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('admin.torneos.*') ? 'bg-gray-700 text-white border-l-4 border-accent-400' : '' }}">
            <i class="fas fa-trophy w-6"></i>
            <span class="ml-3">Torneos</span>
        </a>

        <a href="{{ route('admin.pagos.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('admin.pagos.*') ? 'bg-gray-700 text-white border-l-4 border-accent-400' : '' }}">
            <i class="fas fa-credit-card w-6"></i>
            <span class="ml-3">Pagos</span>
        </a>

        <a href="{{ route('admin.sugerencias.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('admin.sugerencias.*') ? 'bg-gray-700 text-white border-l-4 border-accent-400' : '' }}">
            <i class="fas fa-comments w-6"></i>
            <span class="ml-3">Sugerencias</span>
            @if(isset($sugerenciasPendientes) && $sugerenciasPendientes > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $sugerenciasPendientes }}</span>
            @endif
        </a>

        <div class="border-t border-gray-700 my-4"></div>

        <a href="{{ route('admin.configuracion.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('admin.configuracion.*') ? 'bg-gray-700 text-white border-l-4 border-accent-400' : '' }}">
            <i class="fas fa-cog w-6"></i>
            <span class="ml-3">Configuración</span>
        </a>

        <div class="border-t border-gray-700 my-4"></div>

        <!--<a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition">
            <i class="fas fa-arrow-left w-6"></i>
            <span class="ml-3">Volver a Panel</span>
        </a>-->
    </nav>

    <!-- User Info -->
    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-accent-500 flex items-center justify-center font-bold text-gray-900">
                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido, 0, 1)) }}
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</p>
                <p class="text-xs text-gray-400">Superadministrador</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>
