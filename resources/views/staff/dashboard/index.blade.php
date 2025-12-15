@extends('staff.layouts.app')

@section('content')

    {{-- WELCOME BANNER (Style Admin) --}}
    <div class="bg-black text-white p-8 rounded-3xl mb-8 relative overflow-hidden fade-in group">
        <div class="absolute right-0 top-0 w-64 h-64 bg-gray-800 rounded-full blur-3xl opacity-20 -mr-16 -mt-16"></div>
        <div
            class="absolute right-6 top-6 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
            <i data-feather="cpu" class="w-24 h-24 text-white"></i>
        </div>

        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-2">
                @php
                    $h = date('H');
                    $greet = $h < 12 ? 'Good morning' : ($h < 18 ? 'Good afternoon' : 'Good evening');
                @endphp
                {{ $greet }}, {{ explode(' ', Auth::user()->full_name)[0] }}! 🚀
            </h1>
            <p class="text-gray-400 max-w-xl">
                You have <strong class="text-white">{{ $stats['tasks_active'] }} active projects</strong> requiring your
                attention today. Keep up the great work!
            </p>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        {{-- Card 1: Active Tasks --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2 relative z-10">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i data-feather="zap" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Active Tasks</p>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 relative z-10">{{ $stats['tasks_active'] }}</h3>
        </div>

        {{-- Card 2: Completed --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Completed</p>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['tasks_completed'] }}</h3>
        </div>

        {{-- Card 3: Balance --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <i data-feather="database" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">My Balance</p>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['token_balance']) }} <span
                    class="text-sm text-gray-400 font-medium">TX</span></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- LEFT COLUMN: ACTIVE TASKS (2/3) --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                        <h3 class="font-bold">My Assignments</h3>
                    </div>
                </div>

                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Project</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($recentTasks as $task)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ Str::limit($task->title, 30) }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $task->service->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden">
                                            <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <span
                                            class="text-xs font-medium text-gray-700">{{ explode(' ', $task->user->full_name)[0] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = match ($task->status) {
                                            'active' => 'bg-blue-100 text-blue-700',
                                            'revision' => 'bg-orange-100 text-orange-700',
                                            'review' => 'bg-purple-100 text-purple-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $badgeClass }}">
                                        {{ str_replace('_', ' ', $task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-400 hover:bg-black hover:text-white hover:border-black transition-all">
                                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i data-feather="coffee" class="w-8 h-8 mb-2 opacity-50"></i>
                                        <p>No active tasks. Relax!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT COLUMN: NOTIFICATIONS (1/3) --}}
        <div class="space-y-6">

            {{-- CARD NOTIFICATIONS (Dari Client Script) --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-lg">Notifications</h3>
                        {{-- BADGE COUNTER --}}
                        {{-- NOTE: Pastikan $unreadCount dikirim dari Controller --}}
                        <span id="unread-badge"
                            class="{{ ($unreadCount ?? 0) > 0 ? 'flex' : 'hidden' }} items-center justify-center bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                            {{ $unreadCount ?? 0 }}
                        </span>
                    </div>

                    @if (isset($notifications) && $notifications->count() > 0)
                        <button onclick="markAllRead()"
                            class="text-[10px] text-gray-400 hover:text-black uppercase font-bold transition-colors">
                            Read All
                        </button>
                    @endif
                </div>

                {{-- SCROLLABLE CONTAINER --}}
                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1 custom-scrollbar">
                    @if (isset($notifications))
                        @forelse($notifications as $notif)
                            @php
                                $labelColor = match ($notif->type) {
                                    'info' => 'bg-blue-100 text-blue-700',
                                    'success' => 'bg-green-100 text-green-700',
                                    'warning' => 'bg-yellow-100 text-yellow-700',
                                    'error' => 'bg-red-100 text-red-700',
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

                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                    <i data-feather="bell" class="w-4 h-4 text-gray-500"></i>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded {{ $labelColor }}">
                                            {{ $notif->type ?? 'System' }}
                                        </span>
                                        <span
                                            class="text-[10px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h5 class="font-bold text-sm text-gray-900 leading-tight mb-0.5">{{ $notif->title }}
                                    </h5>
                                    <p class="text-xs text-gray-500 leading-relaxed">{{ Str::limit($notif->message, 60) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div
                                    class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-feather="bell-off" class="w-5 h-5 text-gray-300"></i>
                                </div>
                                <p class="text-xs text-gray-400 font-medium">No new notifications.</p>
                            </div>
                        @endforelse
                    @else
                        <div class="text-center py-4">
                            <p class="text-xs text-gray-400">Loading notifications...</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Payouts Small Card --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-sm mb-4">Recent Payouts</h3>
                <div class="space-y-3">
                    @forelse($recentPayouts as $payout)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                    <i data-feather="arrow-down-left" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900">IDR
                                        {{ number_format($payout->amount_currency / 1000) }}k</p>
                                    <p class="text-[9px] text-gray-400">{{ $payout->created_at->format('d M') }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-mono text-gray-500">-{{ $payout->amount_token }} TX</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center">No payouts yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS DARI CLIENT DASHBOARD --}}
    <script>
        // --- AJAX MARK AS READ FUNCTION ---
        // Anda perlu memastikan route '/staff/notifications/...' sudah dibuat di web.php
        // Ganti '/client/' menjadi '/staff/' pada fetch URL di bawah ini
        const csrfToken = "{{ csrf_token() }}";

        function markOneRead(id) {
            const notifEl = document.getElementById(`notif-${id}`);
            const dotEl = document.getElementById(`dot-${id}`);
            if (notifEl.classList.contains('opacity-60')) return;

            notifEl.classList.add('opacity-60', 'bg-white');
            notifEl.classList.remove('bg-blue-50/50');
            if (dotEl) dotEl.classList.add('hidden');
            decreaseBadge();

            // Ubah URL ke endpoint Staff
            fetch(`/staff/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            }).catch(err => console.error(err));
        }

        function markAllRead() {
            const unreadItems = document.querySelectorAll('[id^="notif-"]:not(.opacity-60)');
            unreadItems.forEach(el => {
                el.classList.add('opacity-60', 'bg-white');
                el.classList.remove('bg-blue-50/50');
                const id = el.id.replace('notif-', '');
                const dot = document.getElementById(`dot-${id}`);
                if (dot) dot.classList.add('hidden');
            });
            updateBadge(0);

            // Ubah URL ke endpoint Staff
            fetch(`/staff/notifications/read-all`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            }).catch(err => console.error(err));
        }

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
@endsection
