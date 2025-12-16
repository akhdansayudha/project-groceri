{{-- TRANSFER MODAL WRAPPER --}}
<div id="transferModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeTransferModal()"></div>

    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all scale-100 flex flex-col max-h-[90vh]">

        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-gray-900">Process Payout Request</h3>
                <p class="text-xs text-gray-500 mt-0.5 font-mono" id="modalPayoutID">#PY-0000</p>
            </div>
            <button onclick="closeTransferModal()"
                class="p-2 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="p-8 overflow-y-auto custom-scrollbar">

            {{-- Staff & Amount Info --}}
            <div class="flex gap-4 mb-6">
                <div class="flex-1 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Staff</p>
                    <p class="font-bold text-gray-900 text-sm" id="modalStaffName">-</p>
                </div>
                <div class="flex-1 bg-black text-white p-4 rounded-2xl shadow-lg">
                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Transfer Amount</p>
                    <p class="font-bold text-xl" id="modalAmountIDR">Rp 0</p>
                </div>
            </div>

            {{-- Bank Details Section --}}
            <div class="mb-8">
                <div class="flex justify-between items-end mb-2">
                    <label class="text-[10px] uppercase font-bold text-gray-400">Destination Account</label>
                    <button onclick="copyBankDetails()"
                        class="text-[10px] font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <i data-feather="copy" class="w-3 h-3"></i> Copy Number
                    </button>
                </div>
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl relative">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-white rounded-lg text-blue-600 shadow-sm">
                            <i data-feather="credit-card" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900" id="modalBankName">-</p>
                            <p class="text-sm font-mono text-gray-700 tracking-wide my-0.5" id="modalBankAccount">-</p>
                            <p class="text-xs text-gray-500 uppercase font-bold" id="modalBankHolder">-</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABS SWITCHER --}}
            <div class="flex p-1 bg-gray-100 rounded-xl mb-6">
                <button type="button" id="tabApprove" onclick="switchTab('approve')"
                    class="flex-1 py-2 text-xs font-bold rounded-lg transition-all bg-white shadow-sm text-black">
                    Approve & Upload Proof
                </button>
                <button type="button" id="tabReject" onclick="switchTab('reject')"
                    class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-gray-500 hover:text-gray-700">
                    Reject Request
                </button>
            </div>

            {{-- FORM APPROVE --}}
            <form id="approveForm" method="POST" enctype="multipart/form-data" action="">
                @csrf
                <div class="mb-4">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block">Upload Transfer
                        Receipt</label>
                    <input type="file" name="proof_file" accept="image/*,application/pdf" required
                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-200 rounded-xl">
                    <p class="text-[10px] text-gray-400 mt-1">Supported: JPG, PNG, PDF. Max 2MB.</p>
                </div>

                <div class="mb-6">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block">Admin Note
                        (Optional)</label>
                    <textarea name="admin_note" rows="2" placeholder="e.g. Transfer via KlikBCA..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-black transition-all"></textarea>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 shadow-lg shadow-black/20 flex items-center justify-center gap-2">
                    <i data-feather="check-circle" class="w-4 h-4"></i> Confirm Payout
                </button>
            </form>

            {{-- FORM REJECT --}}
            <form id="rejectForm" method="POST" action="" class="hidden">
                @csrf
                <div class="mb-6">
                    <label class="text-[10px] uppercase font-bold text-red-400 mb-2 block">Reason for Rejection
                        (Required)</label>
                    <textarea name="reject_reason" rows="3" required placeholder="e.g. Bank details incorrect..."
                        class="w-full bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-xs text-red-900 placeholder-red-300 focus:outline-none focus:border-red-300 transition-all"></textarea>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-500/20 flex items-center justify-center gap-2">
                    <i data-feather="x-circle" class="w-4 h-4"></i> Reject & Refund Tokens
                </button>
            </form>

        </div>
    </div>
</div>

<script>
    let currentAccountNum = '';

    // 1. Fungsi Utama Buka Modal
    function openTransferModal(payout, user) {
        const modal = document.getElementById('transferModal');
        modal.classList.remove('hidden');

        // Reset Tab ke 'approve' setiap kali modal dibuka
        switchTab('approve');

        // Fill Data Text
        document.getElementById('modalPayoutID').innerText = '#PY-' + payout.id;
        document.getElementById('modalStaffName').innerText = user.full_name;
        document.getElementById('modalAmountIDR').innerText = 'Rp ' + parseInt(payout.amount_currency).toLocaleString(
            'id-ID');

        // Fill Bank Data (Fallback logic)
        const bankName = payout.bank_name || user.bank_name || '-';
        const bankAcc = payout.bank_account || user.bank_account || '-';
        const bankHolder = payout.bank_holder || user.bank_holder || '-';

        document.getElementById('modalBankName').innerText = bankName;
        document.getElementById('modalBankAccount').innerText = bankAcc;
        document.getElementById('modalBankHolder').innerText = bankHolder;

        currentAccountNum = bankAcc;

        // Set Action URL Forms
        const baseUrl = "{{ url('/') }}";
        document.getElementById('approveForm').action = `${baseUrl}/admin/performance/${payout.id}/approve`;
        document.getElementById('rejectForm').action = `${baseUrl}/admin/performance/${payout.id}/reject`;

        // Reset Input File
        const fileInput = document.querySelector('input[name="proof_file"]');
        if (fileInput) fileInput.value = '';
    }

    // 2. Fungsi Tutup Modal
    function closeTransferModal() {
        document.getElementById('transferModal').classList.add('hidden');
    }

    // 3. Logic Tab Switcher (Approve vs Reject)
    function switchTab(mode) {
        const tabApprove = document.getElementById('tabApprove');
        const tabReject = document.getElementById('tabReject');
        const formApprove = document.getElementById('approveForm');
        const formReject = document.getElementById('rejectForm');

        // Reset Base Classes
        const inactiveClass = "text-gray-500 hover:text-gray-700 bg-transparent shadow-none";
        const activeApproveClass = "bg-white shadow-sm text-black";
        const activeRejectClass = "bg-red-50 text-red-600 shadow-sm";

        if (mode === 'approve') {
            // Tampilkan Form Approve, Sembunyikan Reject
            formApprove.classList.remove('hidden');
            formReject.classList.add('hidden');

            // Style Tombol
            tabApprove.className = `flex-1 py-2 text-xs font-bold rounded-lg transition-all ${activeApproveClass}`;
            tabReject.className = `flex-1 py-2 text-xs font-bold rounded-lg transition-all ${inactiveClass}`;
        } else {
            // Tampilkan Form Reject, Sembunyikan Approve
            formApprove.classList.add('hidden');
            formReject.classList.remove('hidden');

            // Style Tombol
            tabApprove.className = `flex-1 py-2 text-xs font-bold rounded-lg transition-all ${inactiveClass}`;
            tabReject.className = `flex-1 py-2 text-xs font-bold rounded-lg transition-all ${activeRejectClass}`;
        }
    }

    // 4. Copy Clipboard
    function copyBankDetails() {
        if (!currentAccountNum || currentAccountNum === '-') return;
        navigator.clipboard.writeText(currentAccountNum).then(() => {
            // Opsional: Bisa tambahkan toast notif sederhana disini
            // alert('Account copied!'); 
        });
    }
</script>
