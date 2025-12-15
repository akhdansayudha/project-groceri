@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto fade-in">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('admin.notifications.index') }}"
                class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-bold tracking-tight">Compose Broadcast</h1>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
            <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Target Audience --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Target Audience</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['client' => 'All Clients', 'staff' => 'All Staff', 'all_users' => 'Everyone'] as $val => $label)
                            <label class="cursor-pointer w-full">
                                <input type="radio" name="target" value="{{ $val }}" class="peer sr-only"
                                    {{ $loop->first ? 'checked' : '' }}>
                                <div
                                    class="p-3 rounded-xl border border-gray-200 peer-checked:border-black peer-checked:bg-black peer-checked:text-white hover:bg-gray-50 transition-all text-center h-full flex items-center justify-center">
                                    <span class="text-xs font-bold">{{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Notification Type (Full Width Grid) --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Notification Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach (['info', 'success', 'warning', 'promo'] as $type)
                            <label class="cursor-pointer w-full">
                                <input type="radio" name="type" value="{{ $type }}" class="peer sr-only"
                                    {{ $type == 'info' ? 'checked' : '' }}>
                                <div
                                    class="py-3 px-2 rounded-xl border border-gray-200 peer-checked:border-black peer-checked:ring-1 peer-checked:ring-black hover:bg-gray-50 transition-all text-center">
                                    <span class="text-xs font-bold capitalize">{{ $type }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Title --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Subject / Title</label>
                    <input type="text" name="title" required placeholder="e.g. System Maintenance Update"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all font-bold">
                </div>

                {{-- Message --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Message Content</label>

                        {{-- Variable Helper Buttons --}}
                        <div class="flex gap-2">
                            <button type="button" onclick="insertVar('{nama}')"
                                class="px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-lg text-[10px] font-bold text-gray-600 transition-colors flex items-center gap-1">
                                <i data-feather="plus" class="w-3 h-3"></i> Name
                            </button>
                            <button type="button" onclick="insertVar('{email}')"
                                class="px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-lg text-[10px] font-bold text-gray-600 transition-colors flex items-center gap-1">
                                <i data-feather="plus" class="w-3 h-3"></i> Email
                            </button>
                        </div>
                    </div>

                    <textarea id="messageArea" name="message" required rows="6"
                        placeholder="Hi {nama}, kami punya promo menarik untukmu..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all resize-none font-medium"></textarea>

                    <p class="text-[10px] text-gray-400">
                        *Gunakan variabel <strong>{nama}</strong> atau <strong>{email}</strong> untuk personalisasi pesan
                        otomatis.
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="w-full py-3 bg-black text-white rounded-xl font-bold shadow-lg shadow-black/20 hover:bg-gray-800 transition-all flex justify-center items-center gap-2">
                        <i data-feather="send" class="w-4 h-4"></i> Send Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT INSERT VARIABLE --}}
    <script>
        function insertVar(text) {
            const textarea = document.getElementById('messageArea');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const before = textarea.value.substring(0, start);
            const after = textarea.value.substring(end, textarea.value.length);

            // Insert text
            textarea.value = before + text + after;

            // Move cursor after inserted text
            textarea.selectionStart = textarea.selectionEnd = start + text.length;

            // Focus back to textarea
            textarea.focus();
        }
    </script>
@endsection
