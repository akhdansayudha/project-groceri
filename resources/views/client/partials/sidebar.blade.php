<aside class="flex flex-col w-72 h-screen px-6 py-8 overflow-y-auto bg-white border-r border-gray-200 sticky top-0">

    <div class="mb-10 px-2">
        <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tighter">
            Vektora<span class="text-blue-600">.</span>
        </a>
    </div>

    <div class="flex flex-col justify-between flex-1">
        <nav class="space-y-6">

            <div>
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Workspace</p>
                <div class="space-y-1">
                    {{-- OVERVIEW / DASHBOARD --}}
                    <a href="{{ route('client.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium {{ request()->routeIs('client.dashboard') ? 'bg-black text-white shadow-lg shadow-black/20' : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}">
                        <i data-feather="grid" class="w-5 h-5"></i>
                        <span>Overview</span>
                    </a>

                    {{-- WORKSPACE (DULU NEW REQUEST) --}}
                    {{-- Update: Text jadi Workspace & Active state pakai wildcard (.*) --}}
                    <a href="{{ Route::has('client.workspaces.index') ? route('client.workspaces.index') : '#' }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium {{ request()->routeIs('client.workspaces.*') ? 'bg-black text-white shadow-lg shadow-black/20' : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}">
                        {{-- Opsional: Ikon bisa diganti 'folder' jika lebih cocok, tapi 'plus-circle' tetap oke --}}
                        <i data-feather="folder" class="w-5 h-5"></i>
                        <span>Workspace</span>
                    </a>

                    {{-- MY REQUESTS --}}
                    <a href="{{ Route::has('client.requests.index') ? route('client.requests.index') : '#' }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium {{ request()->routeIs('client.requests.*') || request()->routeIs('client.requests.show') ? 'bg-black text-white shadow-lg shadow-black/20' : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}">
                        <i data-feather="layers" class="w-5 h-5"></i>
                        <span>My Requests</span>
                        @if (isset($activeTasksCount) && $activeTasksCount > 0)
                            <span
                                class="ml-auto bg-blue-100 text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $activeTasksCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <div>
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Finance</p>
                <div class="space-y-1">
                    <a href="{{ Route::has('client.wallet.index') ? route('client.wallet.index') : '#' }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium {{ request()->routeIs('client.wallet.*') ? 'bg-black text-white shadow-lg shadow-black/20' : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}">
                        <i data-feather="credit-card" class="w-5 h-5"></i>
                        <span>My Wallet</span>
                    </a>

                    <a href="{{ Route::has('client.invoices.index') ? route('client.invoices.index') : '#' }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium {{ request()->routeIs('client.invoices.*') ? 'bg-black text-white shadow-lg shadow-black/20' : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}">
                        <i data-feather="file-text" class="w-5 h-5"></i>
                        <span>Invoices</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Support</p>
                <div class="space-y-1">
                    <a href="{{ Route::has('client.support') ? route('client.support') : '#' }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium text-gray-500 hover:bg-gray-50 hover:text-black">
                        <i data-feather="message-circle" class="w-5 h-5"></i>
                        <span>Help Center</span>
                    </a>
                </div>
            </div>

        </nav>

        <div class="space-y-2 mt-auto pt-6 border-t border-gray-100">
            <a href="{{ Route::has('client.settings') ? route('client.settings') : '#' }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group font-medium {{ request()->routeIs('client.settings') ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}">
                <i data-feather="settings" class="w-5 h-5"></i>
                <span>Settings</span>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition-colors text-left font-medium">
                    <i data-feather="log-out" class="w-5 h-5"></i>
                    <span>Log out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
