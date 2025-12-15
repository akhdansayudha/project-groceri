@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('client.requests.show', $task->id) }}"
                class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-3xl font-bold tracking-tight">Edit Request</h1>
        </div>
        <p class="text-gray-500 ml-14">Update details for project #{{ substr($task->id, 0, 8) }}. Status:
            {{ ucfirst($task->status) }}</p>
    </div>

    @if (session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 flex items-center gap-3">
            <i data-feather="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('client.requests.update', $task->id) }}" method="POST" enctype="multipart/form-data"
        class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">
        @csrf
        @method('PUT')

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-xs">1</span>
                    Project Details
                </h3>

                <div class="space-y-6">

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Workspace</label>
                        <select name="workspace_id" required
                            class="w-full border-b border-gray-200 py-3 bg-transparent font-medium focus:border-black outline-none cursor-pointer">
                            @foreach ($workspaces as $ws)
                                <option value="{{ $ws->id }}"
                                    {{ old('workspace_id', $task->workspace_id) == $ws->id ? 'selected' : '' }}>
                                    {{ $ws->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Project
                            Title</label>
                        <input type="text" name="title" required value="{{ old('title', $task->title) }}"
                            class="w-full border-b border-gray-200 py-3 text-lg font-medium focus:outline-none focus:border-black transition-colors bg-transparent placeholder-gray-300">
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Target
                            Deadline</label>
                        <input type="date" name="deadline" required
                            value="{{ old('deadline', $task->deadline->format('Y-m-d')) }}"
                            class="w-full border-b border-gray-200 py-3 bg-transparent font-medium focus:border-black outline-none text-gray-700">
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description /
                            Brief</label>
                        <textarea name="description" required rows="6"
                            class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all resize-none bg-gray-50/50">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                        <p class="text-sm font-bold mb-4 flex items-center gap-2">
                            <i data-feather="sliders" class="w-4 h-4"></i> Additional Details
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Brand Color (Hex)</label>
                                <input type="text" name="brief_data[color]"
                                    value="{{ old('brief_data.color', $task->brief_data['color'] ?? '') }}"
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mt-1 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Target Audience</label>
                                <input type="text" name="brief_data[audience]"
                                    value="{{ old('brief_data.audience', $task->brief_data['audience'] ?? '') }}"
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mt-1 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-500 font-semibold">Reference Link (Google Drive /
                                    Figma)</label>
                                <input type="url" name="brief_data[reference]"
                                    value="{{ old('brief_data.reference', $task->brief_data['reference'] ?? '') }}"
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mt-1 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="group">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Attachments</label>

                        @if (isset($task->attachments) && !empty($task->attachments))
                            <div
                                class="mb-3 p-3 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-blue-500">
                                        <i data-feather="file" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-blue-700">File Saat Ini</p>
                                        <p class="text-xs text-blue-500 truncate max-w-[200px]">
                                            {{ is_array($task->attachments) ? count($task->attachments) . ' files uploaded' : basename($task->attachments) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-blue-400 font-medium">Upload baru untuk mengganti</span>
                            </div>
                        @endif

                        <div
                            class="border-2 border-dashed border-gray-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-all relative">
                            <input type="file" name="attachments" class="absolute inset-0 opacity-0 cursor-pointer">
                            <div
                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-400">
                                <i data-feather="upload-cloud" class="w-5 h-5"></i>
                            </div>
                            <p class="text-sm font-bold">Click to replace or drag new files here</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF up to 10MB</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm opacity-80">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs">2</span>
                    Selected Service
                </h3>

                <div class="border border-black bg-black text-white rounded-2xl p-4 relative">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-black">
                                @if ($task->service->icon_url)
                                    <img src="{{ $task->service->icon_url }}" class="w-4 h-4">
                                @else
                                    <i data-feather="zap" class="w-4 h-4"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="font-bold text-sm">{{ $task->service->name }}</h5>
                                <p class="text-[10px] text-gray-400">Service type is locked</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold bg-gray-800 px-2 py-1 rounded text-white">
                            {{ $task->toratix_locked }} TX
                        </span>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mt-3 text-center">
                    <i data-feather="lock" class="w-3 h-3 inline mr-1"></i>
                    Jenis layanan tidak dapat diubah karena mempengaruhi biaya token.
                </p>
            </div>

            <div class="bg-[#111] text-white p-8 rounded-3xl shadow-xl sticky top-24">
                <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-4">Actions</p>

                <button type="submit"
                    class="w-full py-4 bg-white text-black rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors flex justify-center items-center gap-2 shadow-lg">
                    <span>Save Changes</span>
                    <i data-feather="save" class="w-4 h-4"></i>
                </button>

                <a href="{{ route('client.requests.show', $task->id) }}"
                    class="w-full mt-3 py-4 border border-gray-700 text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
                    Cancel
                </a>
            </div>

        </div>
    </form>
@endsection
