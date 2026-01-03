{{-- File: resources/views/staff/projects/partials/chat-bubble.blade.php --}}
@php
    $senderRole = $msg->user->role ?? 'unknown';

    // Cek Pesan Revisi
    $isRevisionMsg = \Illuminate\Support\Str::startsWith($msg->content, 'REVISION REQUESTED:');
    $cleanContent = str_replace('REVISION REQUESTED:', '', $msg->content);

    $roleBadge = match ($senderRole) {
        'client' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Client'],
        'admin' => ['bg' => 'bg-black', 'text' => 'text-white', 'label' => 'Admin'],
        'staff' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Staff'],
        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'User'],
    };
@endphp

@if ($isRevisionMsg)
    {{-- TAMPILAN KHUSUS REVISI --}}
    <div class="flex justify-center mb-6 mt-2 fade-in w-full">
        <div
            class="bg-orange-50 border border-orange-200 rounded-2xl p-5 max-w-[90%] w-full shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-500"></div>
            <div class="flex items-start gap-3">
                <div class="p-2 bg-orange-100 text-orange-600 rounded-lg shrink-0">
                    <i data-feather="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-orange-800 text-sm uppercase tracking-wide mb-1 flex items-center gap-2">
                        Revision Requested
                        <span
                            class="text-[10px] font-normal text-orange-600 lowercase bg-white/50 px-2 py-0.5 rounded-full border border-orange-100">
                            by {{ $msg->user->full_name }}
                        </span>
                    </h4>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line font-medium">
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
                <img src="{{ $msg->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->full_name) }}"
                    class="w-8 h-8 rounded-full bg-gray-200 object-cover border border-gray-300">
            </div>

            <div>
                {{-- Nama & Role --}}
                <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    @if (!$isMe)
                        <span
                            class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleBadge['bg'] }} {{ $roleBadge['text'] }}">
                            {{ $roleBadge['label'] }}
                        </span>
                        <span class="text-xs text-gray-500 font-bold">{{ $msg->user->full_name }}</span>
                    @else
                        <span class="text-xs text-gray-400 font-bold">You (Staff)</span>
                    @endif
                </div>

                {{-- Bubble --}}
                <div
                    class="relative px-5 py-3 shadow-sm text-sm leading-relaxed
                    {{ $isMe ? 'bg-purple-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                    @if ($msg->content)
                        <p>{!! nl2br(e($msg->content)) !!}</p>
                    @endif

                    @if ($msg->attachment_url)
                        <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-purple-400' : 'border-gray-100' }}">
                            @php
                                $link = $msg->attachment_url;
                                if (!str_starts_with($link, 'http')) {
                                    $link = \Illuminate\Support\Facades\Storage::disk('supabase')->url($link);
                                }
                            @endphp
                            <a href="{{ $link }}" target="_blank"
                                class="flex items-center gap-2 group hover:opacity-80 transition">
                                <div
                                    class="w-8 h-8 rounded bg-opacity-20 flex items-center justify-center {{ $isMe ? 'bg-white text-white' : 'bg-gray-100 text-gray-600' }}">
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
