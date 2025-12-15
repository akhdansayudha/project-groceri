@extends('client.layouts.app')

@section('content')
    <div class="mb-10 fade-in">
        <h1 class="text-4xl font-bold mb-2 tracking-tight">
            @php
                $hour = date('H');
                $greeting =
                    $hour < 12
                        ? 'Good morning'
                        : ($hour < 16
                            ? 'Good afternoon'
                            : ($hour < 18
                                ? 'Good evening'
                                : 'Good night'));
            @endphp
            {{ $greeting }}, {{ $user->full_name ?? explode('@', $user->email)[0] }} 👋
        </h1>
        <p class="text-gray-500">Here is the overview of your creative workspace.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        <div class="lg:col-span-2 space-y-8">

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i data-feather="grid" class="w-5 h-5 text-gray-400"></i>
                        Active Workspaces
                    </h3>
                    <a href="{{ route('client.workspaces.index') }}"
                        class="text-xs font-bold text-gray-500 hover:text-black">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($workspaces as $ws)
                        <a href="{{ route('client.workspaces.show', $ws->id) }}"
                            class="group bg-white p-5 rounded-3xl border border-gray-200 shadow-sm hover:border-black transition-all flex items-center gap-4 relative overflow-hidden">

                            <div
                                class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-black group-hover:text-white transition-colors">
                                <i data-feather="folder" class="w-6 h-6"></i>
                            </div>

                            <div>
                                <h4 class="font-bold text-gray-900 truncate max-w-[150px]">{{ $ws->name }}</h4>
                                <p class="text-xs text-gray-400">{{ $ws->tasks_count }} Projects inside</p>
                            </div>

                            <div
                                class="absolute right-4 opacity-0 group-hover:opacity-100 transition-opacity -translate-x-2 group-hover:translate-x-0">
                                <i data-feather="arrow-right" class="w-4 h-4 text-gray-400"></i>
                            </div>
                        </a>
                    @empty
                        <div
                            class="col-span-2 py-8 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center text-center">
                            <p class="text-gray-500 font-medium text-sm">No workspaces created.</p>
                            <a href="{{ route('client.workspaces.index') }}"
                                class="mt-2 text-xs font-bold text-black underline">Create Workspace</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Running Projects
                    </h3>
                    <span class="text-xs bg-black text-white px-3 py-1 rounded-full font-medium">
                        {{ $activeTasks->count() }}/{{ $maxSlots }} Slot Used
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($activeTasks as $task)
                        {{-- Mengarah ke detail project, asumsikan route show ada --}}
                        <a href="{{ Route::has('client.requests.show') ? route('client.requests.show', $task->id) : '#' }}"
                            class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md hover:border-black transition-all relative overflow-hidden group cursor-pointer">

                            {{-- Status Badge --}}
                            <div
                                class="absolute top-0 right-0 bg-yellow-100 text-yellow-800 text-[10px] font-bold px-3 py-1.5 rounded-bl-xl uppercase tracking-wider">
                                {{ str_replace('_', ' ', $task->status) }}
                            </div>

                            <div class="flex items-center gap-3 mb-4 mt-2">
                                <div
                                    class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-feather="loader" class="w-5 h-5 animate-spin-slow"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold truncate">
                                        {{ $task->service->name ?? 'Service' }}
                                    </p>
                                    <h4 class="font-bold text-lg truncate" title="{{ $task->title }}">
                                        {{ $task->title }}
                                    </h4>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            @php
                                $progress = match ($task->status) {
                                    'queue' => 0,
                                    'active' => 10,
                                    'in_progress' => 50,
                                    'review' => 80,
                                    'revision' => 90,
                                    default => 0,
                                };
                            @endphp
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2">
                                <div class="bg-black h-1.5 rounded-full transition-all duration-1000"
                                    style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                        </a>
                    @empty
                        <div
                            class="col-span-full py-10 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center text-center">
                            <div
                                class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                <i data-feather="box" class="w-6 h-6"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No projects running.</p>
                            <a href="{{ route('client.requests.create') }}"
                                class="mt-2 text-xs font-bold underline hover:text-black">
                                Create New Request
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">Recent Deliverables</h3>
                <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">File Name</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 p-3 rounded-full mb-2">
                                            <i data-feather="folder" class="w-5 h-5 text-gray-400"></i>
                                        </div>
                                        <span class="text-gray-400 text-sm font-medium">No files delivered yet.</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="space-y-8">

            <div class="bg-[#111] text-white p-8 rounded-3xl shadow-xl shadow-black/10 relative overflow-hidden group">

                {{-- Decorative Background Blob --}}
                <div
                    class="absolute -top-10 -left-10 w-40 h-40 bg-gray-800 rounded-full blur-3xl opacity-50 group-hover:opacity-70 transition-opacity">
                </div>

                {{-- DECORATIVE ICON (Request: Top Right, Low Opacity, Bleeding Out) --}}
                <div
                    class="absolute -top-6 -right-8 text-gray-800 opacity-90 transform rotate-6 pointer-events-none group-hover:opacity-30 group-hover:scale-110 transition-all duration-700">
                    {{-- Icon logic berdasarkan tier name --}}
                    @if (stripos($tier->name ?? '', 'Ultimate') !== false)
                        <svg xmlns="http://www.w3.org/2000/svg" width="140" height="140" viewBox="0 0 24 24"
                            fill="currentColor" class="lucide lucide-crown">
                            <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14" />
                        </svg>
                    @elseif(stripos($tier->name ?? '', 'Professional') !== false)
                        <i data-feather="star" class="w-32 h-32 fill-current"></i>
                    @else
                        <i data-feather="shield" class="w-32 h-32 fill-current"></i>
                    @endif
                </div>

                <div class="relative z-10">
                    <div class="mb-6">
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Current Tier</p>
                        <h3 class="text-3xl font-bold tracking-tight text-white">{{ $tier->name ?? 'Scout' }}</h3>
                    </div>

                    <div class="mb-8 relative">
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-3">Your Benefits</p>

                        {{-- Benefits Grid (2 Columns) --}}
                        <div class="grid grid-cols-2 gap-x-2 gap-y-3 text-sm text-gray-300">
                            @if (isset($tier->benefits) && !empty($tier->benefits) && is_array($tier->benefits))
                                @foreach ($tier->benefits as $benefit)
                                    <div class="flex items-start gap-2">
                                        <i data-feather="check-circle"
                                            class="w-3.5 h-3.5 text-green-400 mt-0.5 shrink-0"></i>
                                        <span class="text-xs leading-tight">{{ $benefit }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-2 flex items-center gap-2 opacity-50">
                                    <i data-feather="info" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs">Upgrade to unlock more benefits</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 relative z-20">
                        <a href="{{ route('client.wallet.topup') }}"
                            class="flex items-center justify-center py-3 bg-white text-black rounded-xl font-bold text-xs hover:bg-gray-200 transition-colors shadow-lg">
                            Top Up Token
                        </a>
                        <a href="{{ route('client.wallet.index') }}"
                            class="flex items-center justify-center py-3 border border-gray-700 text-white rounded-xl font-bold text-xs hover:bg-gray-800 transition-colors">
                            History
                        </a>
                    </div>
                </div>
            </div>

            {{-- CARD NOTIFICATIONS --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-lg">Notifications</h3>

                        {{-- BADGE COUNTER --}}
                        <span id="unread-badge"
                            class="{{ $unreadCount > 0 ? 'flex' : 'hidden' }} items-center justify-center bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                            {{ $unreadCount }}
                        </span>
                    </div>

                    @if ($notifications->count() > 0)
                        <button onclick="markAllRead()"
                            class="text-[10px] text-gray-400 hover:text-black uppercase font-bold transition-colors">
                            Read All
                        </button>
                    @endif
                </div>

                {{-- SCROLLABLE CONTAINER --}}
                <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($notifications as $notif)
                        {{-- LOGIC WARNA LABEL BERDASARKAN TIPE --}}
                        @php
                            $labelColor = match ($notif->type) {
                                'info' => 'bg-blue-100 text-blue-700',
                                'success' => 'bg-green-100 text-green-700',
                                'warning' => 'bg-yellow-100 text-yellow-700',
                                'error' => 'bg-red-100 text-red-700',
                                'promo' => 'bg-purple-100 text-purple-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <div id="notif-{{ $notif->id }}" onclick="markOneRead('{{ $notif->id }}')"
                            class="flex gap-3 p-3 rounded-2xl transition-all relative group cursor-pointer border border-transparent hover:border-gray-200 
                             {{ $notif->is_read ? 'opacity-60 bg-white' : 'bg-blue-50/50 hover:bg-white' }}">

                            {{-- Blue Dot Indicator --}}
                            <div id="dot-{{ $notif->id }}"
                                class="absolute top-4 right-4 w-2 h-2 bg-blue-500 rounded-full {{ $notif->is_read ? 'hidden' : '' }}">
                            </div>

                            {{-- Icon / Initial --}}
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-feather="bell" class="w-4 h-4 text-gray-500"></i>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    {{-- LABEL TYPE --}}
                                    <span
                                        class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded {{ $labelColor }}">
                                        {{ $notif->type ?? 'System' }}
                                    </span>
                                    <span
                                        class="text-[10px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>

                                <h5 class="font-bold text-sm text-gray-900 leading-tight mb-0.5">{{ $notif->title }}</h5>
                                <p class="text-xs text-gray-500 leading-relaxed">{{ $notif->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-feather="bell-off" class="w-5 h-5 text-gray-300"></i>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">No notifications found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Next in Queue</h3>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-full font-bold text-gray-600">
                        {{ $queueTasks->count() }}
                    </span>
                </div>

                <div class="space-y-2">
                    @forelse($queueTasks as $index => $qTask)
                        {{-- LINK KE DETAIL PROJECT --}}
                        <a href="{{ Route::has('client.requests.show') ? route('client.requests.show', $qTask->id) : '#' }}"
                            class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-2xl transition-colors cursor-pointer group">

                            <div class="text-gray-300 font-bold text-xs w-5">0{{ $index + 1 }}</div>
                            <div class="flex-1 overflow-hidden">
                                <h5
                                    class="font-bold text-sm truncate text-gray-800 group-hover:text-black transition-colors">
                                    {{ $qTask->title }}</h5>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">
                                    {{ $qTask->service->name ?? 'Service' }}
                                </p>
                            </div>
                            <i data-feather="chevron-right"
                                class="w-4 h-4 text-gray-300 group-hover:text-black transition-colors"></i>
                        </a>
                    @empty
                        <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-2xl">
                            <p class="text-xs text-gray-400">Queue is empty.</p>
                        </div>
                    @endforelse
                </div>

                @if ($queueTasks->count() > 0)
                    <a href="{{ route('client.requests.index', ['status' => 'queue']) }}"
                        class="flex items-center justify-center w-full mt-4 py-3 border border-gray-200 rounded-xl text-xs font-bold hover:bg-black hover:text-white transition-colors">
                        View Full Queue
                    </a>
                @endif
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();

        // --- AJAX MARK AS READ FUNCTION ---
        const csrfToken = "{{ csrf_token() }}";

        function markOneRead(id) {
            const notifEl = document.getElementById(`notif-${id}`);
            const dotEl = document.getElementById(`dot-${id}`);

            // Cek jika sudah read (opacity-60), tidak perlu request lagi
            if (notifEl.classList.contains('opacity-60')) return;

            // UI Update Optimistic (Langsung ubah tampilan biar terasa cepat)
            notifEl.classList.add('opacity-60', 'bg-white');
            notifEl.classList.remove('bg-blue-50/50');
            if (dotEl) dotEl.classList.add('hidden');
            decreaseBadge();

            // Send Request
            fetch(`/client/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            }).catch(err => console.error(err));
        }

        function markAllRead() {
            // UI Update All Optimistic
            const unreadItems = document.querySelectorAll('[id^="notif-"]:not(.opacity-60)');
            unreadItems.forEach(el => {
                el.classList.add('opacity-60', 'bg-white');
                el.classList.remove('bg-blue-50/50');
                const id = el.id.replace('notif-', '');
                const dot = document.getElementById(`dot-${id}`);
                if (dot) dot.classList.add('hidden');
            });

            updateBadge(0);

            // Send Request
            fetch(`/client/notifications/read-all`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                // Optional: Toast success
            }).catch(err => console.error(err));
        }

        // Helper Badge Counters
        function decreaseBadge() {
            const badge = document.getElementById('unread-badge');
            let current = parseInt(badge.innerText);
            if (current > 0) {
                current--;
                badge.innerText = current;
                if (current === 0) badge.classList.add('hidden');
            }
        }

        function updateBadge(num) {
            const badge = document.getElementById('unread-badge');
            badge.innerText = num;
            if (num === 0) {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }
    </script>

    <style>
        .animate-spin-slow {
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: #d1d5db;
        }
    </style>
@endsection
