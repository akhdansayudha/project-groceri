@php
    // Data dikirim dari Controller: $msg, $isMe
    $senderRole = $msg->user->role ?? 'unknown';

    // Logic Tanggal (Opsional: Jika dirender satuan via AJAX, date separator biasanya dihandle JS atau diabaikan untuk pesan baru)

    // Cek Pesan Revisi
    $isRevisionMsg = \Illuminate\Support\Str::startsWith($msg->content, 'REVISION REQUESTED');
    $cleanContent = str_replace(['REVISION REQUESTED (BY ADMIN):', 'REVISION REQUESTED:'], '', $msg->content);

    $roleBadge = match ($senderRole) {
        'client' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Client'],
        'staff' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Staff'],
        'admin' => ['bg' => 'bg-black', 'text' => 'text-white', 'label' => 'Admin'],
        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'User'],
    };
@endphp

@if ($isRevisionMsg)
    {{-- TAMPILAN KHUSUS REVISI --}}
    <div class="flex justify-center mb-6 mt-2 fade-in w-full">
        <div
            class="bg-orange-50 border border-orange-200 rounded-2xl p-5 max-w-[85%] w-full shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-500"></div>
            <div class="flex items-start gap-4">
                <div class="p-2.5 bg-orange-100 text-orange-600 rounded-xl shrink-0">
                    <i data-feather="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <h4 class="font-bold text-orange-900 text-sm uppercase tracking-wide flex items-center gap-2">
                            Revision Requested
                            <span
                                class="text-[10px] font-bold text-orange-600 bg-white/60 px-2 py-0.5 rounded-full border border-orange-100 normal-case">
                                by {{ $msg->user->full_name }}
                            </span>
                        </h4>
                        <span
                            class="text-[10px] text-orange-400 font-mono">{{ $msg->created_at->format('d M, H:i') }}</span>
                    </div>
                    <p
                        class="text-sm text-gray-800 leading-relaxed whitespace-pre-line font-medium border-t border-orange-200/50 pt-2 mt-1">
                        {{ trim($cleanContent) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- TAMPILAN CHAT BIASA --}}
    <div class="flex w-full mb-4 {{ $isMe ? 'justify-end' : 'justify-start' }} fade-in">
        <div class="flex max-w-[85%] md:max-w-[75%] gap-3 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <img src="{{ $msg->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->full_name ?? 'User') }}"
                    class="w-9 h-9 rounded-full bg-gray-200 object-cover border border-gray-300 shadow-sm">
            </div>

            <div>
                {{-- Nama & Role Badge --}}
                <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    @if (!$isMe)
                        <span
                            class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleBadge['bg'] }} {{ $roleBadge['text'] }}">
                            {{ $roleBadge['label'] }}
                        </span>
                        <span class="text-xs text-gray-500 font-bold">
                            {{ $msg->user->full_name ?? 'Unknown' }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400 font-bold">You (Admin)</span>
                    @endif
                </div>

                {{-- Bubble --}}
                <div
                    class="relative px-5 py-3 shadow-sm text-sm leading-relaxed
                    {{ $isMe ? 'bg-black text-white rounded-2xl rounded-tr-sm' : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                    @if ($msg->content)
                        <p>{!! nl2br(e($msg->content)) !!}</p>
                    @endif

                    @if ($msg->attachment_url)
                        <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-gray-700' : 'border-gray-100' }}">
                            @php
                                $link = $msg->attachment_url;
                                if (!str_starts_with($link, 'http')) {
                                    $link = \Illuminate\Support\Facades\Storage::disk('supabase')->url($link);
                                }
                            @endphp
                            <a href="{{ $link }}" target="_blank"
                                class="flex items-center gap-3 group p-1 rounded hover:bg-white/10 transition-colors">
                                <div
                                    class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center {{ $isMe ? 'bg-white text-white' : 'bg-gray-100 text-gray-600' }}">
                                    <i data-feather="file" class="w-4 h-4"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="font-bold text-xs truncate max-w-[150px] group-hover:underline">Attachment
                                    </p>
                                    <p class="text-[9px] opacity-70">Click to view</p>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>

                <p class="text-[10px] text-gray-400 mt-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                    {{ $msg->created_at->format('H:i') }}
                </p>
            </div>
        </div>
    </div>
@endif
