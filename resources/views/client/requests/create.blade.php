@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ Route::has('client.workspaces.index') ? route('client.workspaces.index') : '#' }}"
                class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-3xl font-bold tracking-tight">New Request</h1>
        </div>
        <p class="text-gray-500 ml-14">Start a new project by selecting a workspace and providing details.</p>
    </div>

    @if (session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 flex items-center gap-3">
            <i data-feather="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('client.requests.store') }}" method="POST" enctype="multipart/form-data"
        class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">
        @csrf

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-xs">1</span>
                    Project Details
                </h3>

                <div class="space-y-6">

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Select
                            Workspace</label>
                        <select name="workspace_id" required
                            class="w-full border-b border-gray-200 py-3 bg-transparent font-medium focus:border-black outline-none cursor-pointer">

                            {{-- Option Default --}}
                            <option value="" disabled {{ !isset($preselectedWorkspaceId) ? 'selected' : '' }}>
                                Choose a workspace folder...
                            </option>

                            {{-- Loop Workspaces --}}
                            @foreach ($workspaces as $ws)
                                <option value="{{ $ws->id }}"
                                    {{ isset($preselectedWorkspaceId) && $preselectedWorkspaceId == $ws->id ? 'selected' : '' }}>
                                    {{ $ws->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="text-[10px] text-gray-400 mt-1">Project ini akan dikelompokkan ke folder workspace
                            tersebut.</p>

                        {{-- Helper jika user belum punya workspace sama sekali --}}
                        @if ($workspaces->count() == 0)
                            <p class="text-xs text-red-500 mt-2 font-medium">
                                Anda belum memiliki workspace.
                                <a href="{{ route('client.workspaces.index') }}" class="underline hover:text-red-700">Buat
                                    disini</a> terlebih dahulu.
                            </p>
                        @endif
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Project
                            Title</label>
                        <input type="text" name="title" required placeholder="e.g. Redesign Landing Page Vektora"
                            class="w-full border-b border-gray-200 py-3 text-lg font-medium focus:outline-none focus:border-black transition-colors bg-transparent placeholder-gray-300">
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Target
                            Deadline</label>
                        <input type="date" name="deadline" required
                            class="w-full border-b border-gray-200 py-3 bg-transparent font-medium focus:border-black outline-none text-gray-700">
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description /
                            Brief</label>
                        <textarea name="description" required rows="6" placeholder="Describe what you need in detail..."
                            class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all resize-none bg-gray-50/50"></textarea>
                        <p class="text-xs text-gray-400 mt-2 text-right">Markdown supported</p>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                        <p class="text-sm font-bold mb-4 flex items-center gap-2">
                            <i data-feather="sliders" class="w-4 h-4"></i> Additional Details
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Brand Color (Hex)</label>
                                <input type="text" name="brief_data[color]" placeholder="#000000"
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mt-1 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Target Audience</label>
                                <input type="text" name="brief_data[audience]" placeholder="Gen Z, Professionals..."
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mt-1 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-500 font-semibold">Reference Link (Google Drive /
                                    Figma)</label>
                                <input type="url" name="brief_data[reference]" placeholder="https://..."
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mt-1 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Attachments
                            (Optional)</label>

                        {{-- Area Upload --}}
                        <div id="upload-area"
                            class="border-2 border-dashed border-gray-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-all relative">

                            {{-- Input File Asli (Hidden tapi aktif) --}}
                            <input type="file" name="attachments" id="file-input"
                                class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="handleFileSelect(this)">

                            {{-- Tampilan Default (Sebelum Upload) --}}
                            <div id="default-view" class="flex flex-col items-center pointer-events-none">
                                <div
                                    class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-400">
                                    <i data-feather="upload-cloud" class="w-5 h-5"></i>
                                </div>
                                <p class="text-sm font-bold">Click to upload or drag files here</p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF up to 10MB</p>
                            </div>

                            {{-- Tampilan Preview (Setelah File Dipilih) --}}
                            <div id="preview-view" class="hidden flex-col items-center pointer-events-none fade-in">
                                <div
                                    class="w-12 h-12 bg-black text-white rounded-xl flex items-center justify-center mb-3 shadow-lg">
                                    {{-- Ikon File akan berubah via JS --}}
                                    <i id="file-icon" data-feather="file" class="w-6 h-6"></i>
                                </div>
                                <p id="file-name" class="text-sm font-bold text-gray-900 break-all max-w-[200px]">
                                    filename.jpg</p>
                                <p id="file-size" class="text-xs text-gray-500 mt-1 font-mono">0 KB</p>
                                <p class="text-[10px] text-green-600 font-bold mt-2 bg-green-50 px-2 py-1 rounded">Ready to
                                    upload</p>
                            </div>
                        </div>
                    </div>

                    {{-- Script Khusus untuk Preview File --}}
                    <script>
                        function handleFileSelect(input) {
                            const defaultView = document.getElementById('default-view');
                            const previewView = document.getElementById('preview-view');
                            const fileNameEl = document.getElementById('file-name');
                            const fileSizeEl = document.getElementById('file-size');
                            const uploadArea = document.getElementById('upload-area');

                            if (input.files && input.files[0]) {
                                const file = input.files[0];

                                // 1. Update Teks Nama & Ukuran
                                fileNameEl.textContent = file.name;

                                // Format ukuran file (Bytes -> KB/MB)
                                let size = file.size;
                                let unit = 'Bytes';
                                if (size > 1024 * 1024) {
                                    size = (size / (1024 * 1024)).toFixed(2);
                                    unit = 'MB';
                                } else if (size > 1024) {
                                    size = (size / 1024).toFixed(1);
                                    unit = 'KB';
                                }
                                fileSizeEl.textContent = `${size} ${unit}`;

                                // 2. Ganti Tampilan
                                defaultView.classList.add('hidden');
                                previewView.classList.remove('hidden');
                                previewView.classList.add('flex');

                                // Ubah border jadi hijau agar user tahu berhasil dipilih
                                uploadArea.classList.add('border-green-400', 'bg-green-50/20');
                                uploadArea.classList.remove('border-gray-200');

                            } else {
                                // Reset jika batal pilih
                                defaultView.classList.remove('hidden');
                                previewView.classList.add('hidden');
                                previewView.classList.remove('flex');
                                uploadArea.classList.remove('border-green-400', 'bg-green-50/20');
                                uploadArea.classList.add('border-gray-200');
                            }
                        }
                    </script>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-xs">2</span>
                    Select Service
                </h3>

                <input type="hidden" name="service_id" id="selected_service_id" required>

                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach ($services as $service)
                        <div onclick="selectService(this, '{{ $service->id }}', '{{ $service->name }}', {{ $service->toratix_cost }})"
                            class="service-card border border-gray-200 rounded-2xl p-4 cursor-pointer hover:border-black transition-all group relative">

                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-black group-hover:text-white transition-colors icon-container">
                                        @if ($service->icon_url)
                                            <img src="{{ $service->icon_url }}" class="w-4 h-4">
                                        @else
                                            <i data-feather="zap" class="w-4 h-4"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-sm service-name">{{ $service->name }}</h5>
                                        <p class="text-[10px] text-gray-400">{{ Str::limit($service->description, 40) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded text-gray-600 cost-badge">
                                    {{ $service->toratix_cost }} TX
                                </span>
                            </div>

                            <div
                                class="absolute inset-0 border-2 border-black rounded-2xl opacity-0 scale-95 transition-all active-indicator pointer-events-none">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-[#111] text-white p-8 rounded-3xl shadow-xl sticky top-24">
                <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-6">Payment Summary</p>

                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-800">
                    <span class="text-sm text-gray-300">Your Balance</span>
                    <span class="font-bold">{{ $balance }} TX</span>
                </div>

                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-300" id="summary_service_name">Select a service...</span>
                    <span class="font-bold text-xl" id="summary_cost">0 TX</span>
                </div>

                <div id="insufficient_funds"
                    class="hidden mt-4 bg-red-900/50 border border-red-800 p-3 rounded-xl text-xs text-red-200 flex items-center gap-2">
                    <i data-feather="alert-triangle" class="w-4 h-4"></i>
                    <span>Insufficient balance.</span>
                    <a href="{{ route('client.wallet.topup') }}" class="underline font-bold hover:text-white">Top Up</a>
                </div>

                <button type="submit" id="btn_submit" disabled
                    class="w-full mt-8 py-4 bg-white text-black rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                    <span>Create Request</span>
                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>

        </div>
    </form>

    <script>
        const userBalance = {{ $balance }};
        let selectedCost = 0;

        function selectService(el, id, name, cost) {
            // Reset UI
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('border-black', 'bg-black', 'text-white');
                card.classList.add('border-gray-200');

                const iconBox = card.querySelector('.icon-container');
                iconBox.classList.remove('bg-white', 'text-black');
                iconBox.classList.add('bg-gray-100', 'text-gray-500');

                card.querySelector('.service-name').classList.remove('text-white');
                const badge = card.querySelector('.cost-badge');
                badge.classList.remove('bg-gray-800', 'text-white');
                badge.classList.add('bg-gray-100', 'text-gray-600');
            });

            // Highlight Selected
            el.classList.remove('border-gray-200');
            el.classList.add('bg-black', 'text-white', 'border-black');

            const activeIcon = el.querySelector('.icon-container');
            activeIcon.classList.remove('bg-gray-100', 'text-gray-500');
            activeIcon.classList.add('bg-white', 'text-black');

            const activeBadge = el.querySelector('.cost-badge');
            activeBadge.classList.remove('bg-gray-100', 'text-gray-600');
            activeBadge.classList.add('bg-gray-800', 'text-white');

            // Update Input
            document.getElementById('selected_service_id').value = id;
            document.getElementById('summary_service_name').innerText = name;
            document.getElementById('summary_cost').innerText = cost + " TX";
            selectedCost = cost;

            validateBalance();
        }

        function validateBalance() {
            const btn = document.getElementById('btn_submit');
            const alert = document.getElementById('insufficient_funds');

            if (selectedCost > 0) {
                if (userBalance >= selectedCost) {
                    btn.disabled = false;
                    alert.classList.add('hidden');
                } else {
                    btn.disabled = true;
                    alert.classList.remove('hidden');
                }
            } else {
                btn.disabled = true;
            }
        }
    </script>
@endsection
