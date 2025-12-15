@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto fade-in">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('admin.services.index') }}"
                class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-bold tracking-tight">Edit Service</h1>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Service Name</label>
                    <input type="text" name="name" required value="{{ $service->name }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Toratix Cost</label>
                    <div class="relative">
                        <input type="number" name="toratix_cost" required value="{{ $service->toratix_cost }}"
                            min="1"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-10 focus:outline-none focus:border-black focus:bg-white transition-all font-bold">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-yellow-600">
                            <i data-feather="zap" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Description</label>
                    <textarea name="description" required rows="4"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all resize-none">{{ $service->description }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Change Icon (Optional)</label>
                    <div
                        class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer group">
                        <input type="file" name="icon" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">

                        <div class="flex flex-col items-center justify-center gap-2 {{ $service->icon_url ? 'hidden' : '' }}"
                            id="uploadPlaceholder">
                            <div class="p-3 bg-gray-100 rounded-full group-hover:bg-white transition-colors">
                                <i data-feather="image" class="w-6 h-6 text-gray-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 font-medium">Click to upload new icon</p>
                        </div>

                        <img id="iconPreview"
                            src="{{ $service->icon_url ? \Illuminate\Support\Facades\Storage::disk('supabase')->url($service->icon_url) : '' }}"
                            class="{{ $service->icon_url ? '' : 'hidden' }} h-16 w-16 object-contain mx-auto">
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit"
                        class="flex-1 py-3 bg-black text-white rounded-xl font-bold shadow-lg shadow-black/20 hover:bg-gray-800 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('iconPreview');
            const placeholder = document.getElementById('uploadPlaceholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
