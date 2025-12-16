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
            {{-- UPDATE: Max width diperlebar agar teks muat 1 baris di desktop --}}
            <p class="text-gray-400 max-w-3xl leading-relaxed">
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
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg border border-blue-100">
                    <i data-feather="zap" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Active Tasks</p>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 relative z-10">{{ $stats['tasks_active'] }}</h3>
        </div>

        {{-- Card 2: Completed --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-50 text-green-600 rounded-lg border border-green-100">
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Completed</p>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['tasks_completed'] }}</h3>
        </div>

        {{-- Card 3: Balance --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg border border-purple-100">
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
            <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm h-full">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i data-feather="layers" class="w-4 h-4 text-gray-400"></i> My Assignments
                    </h3>
                </div>

                <table class="w-full text-left">
                    <thead
                        class="bg-white text-[10px] uppercase text-gray-400 font-bold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Project</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($recentTasks as $task)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ Str::limit($task->title, 30) }}</div>
                                    <span
                                        class="text-[10px] font-bold text-gray-500 uppercase bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 mt-1 inline-block">
                                        {{ $task->service->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->user->full_name) }}"
                                            class="w-6 h-6 rounded-full border border-gray-100">
                                        <span
                                            class="text-xs font-bold text-gray-600">{{ explode(' ', $task->user->full_name)[0] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = match ($task->status) {
                                            'active' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'revision' => 'bg-orange-50 text-orange-700 border-orange-100',
                                            'review' => 'bg-purple-50 text-purple-700 border-purple-100',
                                            'completed' => 'bg-green-50 text-green-700 border-green-100',
                                            default => 'bg-gray-50 text-gray-600 border-gray-100',
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $badgeClass }}">
                                        {{ str_replace('_', ' ', $task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{-- UPDATE: Link ke detail project --}}
                                    <a href="{{ route('staff.projects.show', $task->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-200 text-gray-400 hover:bg-black hover:text-white hover:border-black transition-all shadow-sm">
                                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                            <i data-feather="coffee" class="w-5 h-5 text-gray-400"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-900">No active tasks</p>
                                        <p class="text-xs text-gray-500">You're all caught up! Relax for a bit.</p>
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

            {{-- CARD NOTIFICATIONS --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="bell" class="w-4 h-4 text-gray-400"></i> Notifications
                        </h3>
                        <span id="unread-badge"
                            class="{{ ($unreadCount ?? 0) > 0 ? 'flex' : 'hidden' }} items-center justify-center bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">
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
                <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                    @if (isset($notifications) && $notifications->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach ($notifications as $notif)
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
                                    class="p-4 transition-all relative group cursor-pointer hover:bg-gray-50 
                                    {{ $notif->is_read ? 'bg-white opacity-60' : 'bg-blue-50/30' }}">

                                    {{-- Blue Dot --}}
                                    <div id="dot-{{ $notif->id }}"
                                        class="absolute top-4 right-4 w-2 h-2 bg-blue-500 rounded-full {{ $notif->is_read ? 'hidden' : '' }}">
                                    </div>

                                    <div class="flex gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-white border border-gray-100 flex items-center justify-center shrink-0 shadow-sm">
                                            <i data-feather="info" class="w-3 h-3 text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span
                                                    class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded {{ $labelColor }}">
                                                    {{ $notif->type ?? 'System' }}
                                                </span>
                                                <span
                                                    class="text-[10px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h5 class="font-bold text-xs text-gray-900 leading-snug mb-0.5">
                                                {{ $notif->title }}</h5>
                                            <p class="text-[12px] text-gray-500 leading-relaxed">
                                                {{ Str::limit($notif->message) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- UPDATE: Empty State Notification --}}
                        <div class="py-10 text-center">
                            <div
                                class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                <i data-feather="bell-off" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-900">No new notifications</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">We'll let you know when something arrives.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Payouts Small Card --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i data-feather="dollar-sign" class="w-4 h-4 text-gray-400"></i> Recent Payouts
                    </h3>
                    <a href="{{ route('staff.finance.earnings') }}"
                        class="text-[10px] font-bold text-gray-400 hover:text-black uppercase transition-colors">
                        View All
                    </a>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($recentPayouts as $payout)
                        {{-- Gunakan Grid untuk membagi 3 Kolom secara rapi --}}
                        <div class="p-4 grid grid-cols-12 items-center hover:bg-gray-50 transition-colors group">

                            {{-- KOLOM 1: Identity (Icon + ID + Date) - Lebar 5/12 --}}
                            <div class="col-span-5 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center border shadow-sm shrink-0
                                    {{ $payout->status == 'approved'
                                        ? 'bg-green-50 border-green-100 text-green-600'
                                        : ($payout->status == 'rejected'
                                            ? 'bg-red-50 border-red-100 text-red-600'
                                            : 'bg-yellow-50 border-yellow-100 text-yellow-600') }}">
                                    @if ($payout->status == 'approved')
                                        <i data-feather="check" class="w-3 h-3"></i>
                                    @elseif($payout->status == 'rejected')
                                        <i data-feather="x" class="w-3 h-3"></i>
                                    @else
                                        <i data-feather="clock" class="w-3 h-3"></i>
                                    @endif
                                </div>
                                <div class="min-w-0"> {{-- min-w-0 agar text truncate works --}}
                                    <p class="text-xs font-bold text-gray-900 font-mono truncate">#PY-{{ $payout->id }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-medium truncate">
                                        {{ $payout->created_at->format('d M Y • H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- KOLOM 2: Status Badge - Lebar 3/12 (Posisi Tengah) --}}
                            <div class="col-span-3 flex justify-center">
                                @if ($payout->status == 'pending')
                                    <span
                                        class="text-[9px] font-bold uppercase text-yellow-600 bg-yellow-50 px-2 py-1 rounded-md border border-yellow-100">
                                        Pending
                                    </span>
                                @elseif($payout->status == 'approved')
                                    <span
                                        class="text-[9px] font-bold uppercase text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-100">
                                        Paid
                                    </span>
                                @else
                                    <span
                                        class="text-[9px] font-bold uppercase text-red-600 bg-red-50 px-2 py-1 rounded-md border border-red-100">
                                        Rejected
                                    </span>
                                @endif
                            </div>

                            {{-- KOLOM 3: Nominal - Lebar 4/12 (Rata Kanan) --}}
                            <div class="col-span-4 text-right">
                                <p class="text-xs font-bold text-gray-900">
                                    Rp {{ number_format($payout->amount_currency, 0, ',', '.') }}
                                </p>
                            </div>

                        </div>
                    @empty
                        {{-- Empty State --}}
                        <div class="py-8 text-center">
                            <div
                                class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 border border-gray-100">
                                <i data-feather="credit-card" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-400">No payouts yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS DARI CLIENT DASHBOARD --}}
    <script>
        const csrfToken = "{{ csrf_token() }}";

        function markOneRead(id) {
            const notifEl = document.getElementById(`notif-${id}`);
            const dotEl = document.getElementById(`dot-${id}`);
            if (notifEl.classList.contains('opacity-60')) return;

            notifEl.classList.add('opacity-60', 'bg-white');
            notifEl.classList.remove('bg-blue-50/30');
            if (dotEl) dotEl.classList.add('hidden');
            decreaseBadge();

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
                el.classList.remove('bg-blue-50/30');
                const id = el.id.replace('notif-', '');
                const dot = document.getElementById(`dot-${id}`);
                if (dot) dot.classList.add('hidden');
            });
            updateBadge(0);

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
