<!-- Sidebar -->
<aside class="w-64 bg-white dark:bg-gray-800 shadow-md">
    <div class="p-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menu</h2>
        <div class="mt-4 space-y-2">
            <x-sidebar-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-sidebar-nav-link>

            {{-- Menu para Alunos --}}
            @if (auth()->user() && auth()->user()->isUser())
                <x-sidebar-nav-link :href="route('cursos.list')" :active="request()->routeIs('cursos.list', 'cursos.enroll')">
                    {{ __('Matricular em Cursos') }}
                </x-sidebar-nav-link>
                <x-sidebar-nav-link :href="route('registrations.my')" :active="request()->routeIs('registrations.my')">
                    {{ __('Meus Cursos') }}
                </x-sidebar-nav-link>
            @endif

            {{-- Menu para Admin --}}
            @if (auth()->user() && auth()->user()->isAdmin())
                <x-sidebar-nav-link :href="route('cursos.index')" :active="request()->routeIs('cursos.index')">
                    {{ __('Cursos') }}
                </x-sidebar-nav-link>
                <x-sidebar-nav-link :href="route('students.index')" :active="request()->routeIs('students.index')">
                    {{ __('Estudantes') }}
                </x-sidebar-nav-link>
                <x-sidebar-nav-link :href="route('registrations.index')" :active="request()->routeIs('registrations.index')">
                    {{ __('Inscrições') }}
                </x-sidebar-nav-link>
            @endif
        </div>
    </div>
</aside>
