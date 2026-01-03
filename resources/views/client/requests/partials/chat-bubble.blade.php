@php
    // Variabel $msg dan $isMe dikirim dari Controller
    $senderRole = $msg->user->role ?? 'unknown';
    $roleBadge = match ($senderRole) {
        'client' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Client'],
        'staff' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Staff'],
        'admin' => ['bg' => 'bg-black', 'text' => 'text-white', 'label' => 'Admin'],
        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'User'],
    };
@endphp

<div class="flex w-full mb-4 {{ $isMe ? 'justify-end' : 'justify-start' }} fade-in">
    <div class="flex max-w-[85%] md:max-w-[75%] gap-3 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

        {{-- Avatar --}}
        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full border border-gray-200 overflow-hidden bg-gray-100">
                <img src="{{ $msg->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->full_name) }}"
                    class="w-full h-full object-cover">
            </div>
        </div>

        <div>
            {{-- Name & Role --}}
            <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                @if (!$isMe)
                    <span
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleBadge['bg'] }} {{ $roleBadge['text'] }}">
                        {{ $roleBadge['label'] }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold">{{ $msg->user->full_name }}</span>
                @else
                    <span class="text-xs text-gray-400 font-bold">You</span>
                @endif
            </div>

            {{-- Bubble --}}
            <div
                class="relative px-4 py-3 shadow-sm text-sm leading-relaxed break-words
                {{ $isMe ? 'bg-black text-white rounded-2xl rounded-tr-sm' : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                @if ($msg->content)
                    <p>{!! nl2br(e($msg->content)) !!}</p>
                @endif

                @if ($msg->attachment_url)
                    <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-gray-700' : 'border-gray-100' }}">
                        @php
                            $fileLink = str_starts_with($msg->attachment_url, 'http')
                                ? $msg->attachment_url
                                : \Illuminate\Support\Facades\Storage::disk('supabase')->url($msg->attachment_url);
                        @endphp
                        <a href="{{ $fileLink }}" target="_blank"
                            class="flex items-center gap-2 hover:opacity-80 transition">
                            <i data-feather="file" class="w-4 h-4"></i>
                            <span class="text-xs underline">Attachment</span>
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
