{{-- UBAH py-2 MENJADI py-6 agar padding atas dan bawah seimbang --}}
<footer class="mt-auto border-t border-gray-200 py-6 mb-2">
    <div class="flex flex-col md:flex-row items-center justify-center gap-2 md:gap-3 text-center h-full">
        {{-- Copyright --}}
        <p class="text-xs text-gray-400">
            &copy; {{ date('Y') }} Vektora Admin Panel. v1.0.0
        </p>

        {{-- Separator (Hanya tampil di Desktop) --}}
        <span class="hidden md:block text-gray-300 text-xs">&bull;</span>

        {{-- Credits --}}
        <p class="text-xs text-gray-400">
            Dibuat oleh <span class="font-bold text-gray-600">Kelompok 3 Komputasi Awan IS-06-03</span>
        </p>
    </div>
</footer>
