@extends('admin.layouts.master')
@section('title', 'Kasir (POS)')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/pos.css') }}">
    <!-- Midtrans Snap JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
@endsection

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h3>Kasir (POS)</h3>
                    <p class="text-subtitle text-muted">Buat pesanan langsung dari kasir</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="pos-container">
                {{-- ═══ LEFT: MENU PANEL ═══ --}}
                <div class="pos-menu-panel">
                    {{-- Search --}}
                    <div class="pos-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="posSearch" placeholder="Cari menu...">
                    </div>

                    {{-- Category Filter --}}
                    <div class="pos-categories">
                        <button class="pos-cat-btn active" data-cat="all">Semua</button>
                        @foreach ($categories as $cat)
                            <button class="pos-cat-btn" data-cat="{{ $cat->id }}">{{ $cat->cat_name }}</button>
                        @endforeach
                    </div>

                    {{-- Menu Grid --}}
                    <div class="pos-grid" id="posGrid">
                        @foreach ($items as $item)
                            <div class="pos-item-card" data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                data-price="{{ $item->price }}" data-cat="{{ $item->category_id }}"
                                data-img="{{ $item->img }}">
                                <img src="{{ asset('img_item_upload/' . $item->img) }}" alt="{{ $item->name }}"
                                    onerror="this.onerror=null; this.src='{{ $item->img }}';">
                                <div class="pos-item-info">
                                    <h6 title="{{ $item->name }}">{{ $item->name }}</h6>
                                    <span class="price">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ═══ RIGHT: CART PANEL ═══ --}}
                <div class="pos-cart-panel">
                    <div class="cart-header">
                        <h5><i class="bi bi-cart3 me-2"></i>Keranjang</h5>
                        <button class="btn btn-sm btn-outline-danger" id="clearCart" title="Kosongkan">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    {{-- Cart Items --}}
                    <div class="cart-items-wrapper" id="cartItems">
                        <div class="cart-empty" id="cartEmpty">
                            <i class="bi bi-cart-x" style="font-size:2rem;display:block;margin-bottom:.3rem"></i>
                            Keranjang kosong
                        </div>
                    </div>

                    {{-- Form Inputs --}}
                    <div class="cart-form-group">
                        <label for="tableNumber">No. Meja</label>
                        <input type="number" id="tableNumber" min="1" placeholder="Contoh: 1">
                    </div>

                    <div class="cart-form-group">
                        <label>Metode Pembayaran</label>
                        <div class="payment-options">
                            <div class="payment-option selected" data-method="tunai">
                                <i class="bi bi-cash-stack"></i> Tunai
                            </div>
                            <div class="payment-option" data-method="qris">
                                <i class="bi bi-qr-code"></i> QRIS
                            </div>
                        </div>
                    </div>

                    {{-- Cash Input (only visible when Tunai) --}}
                    <div class="cash-section" id="cashSection">
                        <label for="cashAmount"><i class="bi bi-cash me-1"></i>Uang Diterima</label>
                        <input type="number" id="cashAmount" min="0" placeholder="Masukkan nominal...">
                        <div class="cash-change" id="cashChangeRow" style="display:none">
                            <span>Kembalian</span>
                            <span id="cashChangeAmount">Rp0</span>
                        </div>
                    </div>

                    <div class="cart-form-group">
                        <label for="orderNote">Catatan</label>
                        <textarea id="orderNote" placeholder="Opsional..."></textarea>
                    </div>

                    {{-- Summary --}}
                    <div class="cart-summary">
                        <div class="row-summary">
                            <span>Subtotal</span>
                            <span id="cartSubtotal">Rp0</span>
                        </div>
                        <div class="row-summary">
                            <span>Pajak (10%)</span>
                            <span id="cartTax">Rp0</span>
                        </div>
                        <div class="row-summary total">
                            <span>Total</span>
                            <span id="cartTotal">Rp0</span>
                        </div>
                    </div>

                    <button class="btn-pos-submit" id="submitOrder" disabled>
                        <i class="bi bi-check-circle me-1"></i> Buat Pesanan
                    </button>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── State ──
            let cart = {};
            let selectedPayment = 'tunai';
            let currentTotal = 0;

            const fmt = (n) => 'Rp' + Number(n).toLocaleString('id-ID');

            // ── Cash Elements ──
            const cashSection = document.getElementById('cashSection');
            const cashInput = document.getElementById('cashAmount');
            const cashChangeRow = document.getElementById('cashChangeRow');
            const cashChangeAmt = document.getElementById('cashChangeAmount');

            // ── Elements ──
            const cartItemsEl = document.getElementById('cartItems');
            const cartEmptyEl = document.getElementById('cartEmpty');
            const subtotalEl = document.getElementById('cartSubtotal');
            const taxEl = document.getElementById('cartTax');
            const totalEl = document.getElementById('cartTotal');
            const submitBtn = document.getElementById('submitOrder');

            // ═══ ADD TO CART ═══
            document.querySelectorAll('.pos-item-card').forEach(card => {
                card.addEventListener('click', () => {
                    const id = card.dataset.id;
                    const name = card.dataset.name;
                    const price = parseInt(card.dataset.price);

                    if (cart[id]) {
                        cart[id].qty++;
                    } else {
                        cart[id] = {
                            id,
                            name,
                            price,
                            qty: 1
                        };
                    }
                    renderCart();
                });
            });

            // ═══ RENDER CART ═══
            function renderCart() {
                const keys = Object.keys(cart);
                if (keys.length === 0) {
                    cartItemsEl.innerHTML = `<div class="cart-empty" id="cartEmpty">
                <i class="bi bi-cart-x" style="font-size:2rem;display:block;margin-bottom:.3rem"></i>
                Keranjang kosong</div>`;
                    subtotalEl.textContent = fmt(0);
                    taxEl.textContent = fmt(0);
                    totalEl.textContent = fmt(0);
                    submitBtn.disabled = true;
                    return;
                }

                let html = '';
                let subtotal = 0;

                keys.forEach(id => {
                    const item = cart[id];
                    const lineTotal = item.price * item.qty;
                    subtotal += lineTotal;

                    html += `
            <div class="cart-item" data-id="${id}">
                <div class="cart-item-info">
                    <div class="name">${item.name}</div>
                    <div class="item-price">${fmt(item.price)} × ${item.qty} = ${fmt(lineTotal)}</div>
                </div>
                <div class="cart-qty">
                    <button onclick="posChangeQty('${id}', -1)">−</button>
                    <span>${item.qty}</span>
                    <button onclick="posChangeQty('${id}', 1)">+</button>
                </div>
                <button class="cart-remove" onclick="posRemoveItem('${id}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>`;
                });

                cartItemsEl.innerHTML = html;

                const tax = subtotal * 0.1;
                currentTotal = subtotal + tax;
                subtotalEl.textContent = fmt(subtotal);
                taxEl.textContent = fmt(tax);
                totalEl.textContent = fmt(currentTotal);

                // Validate submit button
                validateSubmit();
            }

            // ═══ CART ACTIONS (global) ═══
            window.posChangeQty = function(id, delta) {
                if (!cart[id]) return;
                cart[id].qty += delta;
                if (cart[id].qty <= 0) delete cart[id];
                renderCart();
            };

            window.posRemoveItem = function(id) {
                delete cart[id];
                renderCart();
            };

            // ═══ CLEAR CART ═══
            document.getElementById('clearCart').addEventListener('click', () => {
                cart = {};
                renderCart();
            });

            // ═══ CATEGORY FILTER ═══
            document.querySelectorAll('.pos-cat-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.pos-cat-btn').forEach(b => b.classList.remove(
                        'active'));
                    btn.classList.add('active');

                    const cat = btn.dataset.cat;
                    document.querySelectorAll('.pos-item-card').forEach(card => {
                        card.style.display = (cat === 'all' || card.dataset.cat === cat) ?
                            '' : 'none';
                    });
                });
            });

            // ═══ SEARCH ═══
            document.getElementById('posSearch').addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.pos-item-card').forEach(card => {
                    card.style.display = card.dataset.name.toLowerCase().includes(q) ? '' : 'none';
                });
                // Reset category filter
                document.querySelectorAll('.pos-cat-btn').forEach(b => b.classList.remove('active'));
                document.querySelector('.pos-cat-btn[data-cat="all"]').classList.add('active');
            });

            // ═══ PAYMENT TOGGLE ═══
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.addEventListener('click', () => {
                    document.querySelectorAll('.payment-option').forEach(o => o.classList.remove(
                        'selected'));
                    opt.classList.add('selected');
                    selectedPayment = opt.dataset.method;
                    toggleCashSection();
                    validateSubmit();
                });
            });

            // ═══ CASH SECTION ═══
            function toggleCashSection() {
                if (selectedPayment === 'tunai') {
                    cashSection.style.display = '';
                } else {
                    cashSection.style.display = 'none';
                    cashInput.value = '';
                    cashChangeRow.style.display = 'none';
                }
            }

            cashInput.addEventListener('input', () => {
                const cashVal = parseInt(cashInput.value) || 0;
                if (cashVal > 0 && currentTotal > 0) {
                    cashChangeRow.style.display = '';
                    const change = cashVal - currentTotal;
                    cashChangeAmt.textContent = fmt(Math.abs(change));
                    if (change >= 0) {
                        cashChangeRow.className = 'cash-change sufficient';
                        cashChangeAmt.textContent = fmt(change);
                    } else {
                        cashChangeRow.className = 'cash-change insufficient';
                        cashChangeAmt.textContent = 'Kurang ' + fmt(Math.abs(change));
                    }
                } else {
                    cashChangeRow.style.display = 'none';
                }
                validateSubmit();
            });

            // ═══ VALIDATE SUBMIT ═══
            function validateSubmit() {
                const hasItems = Object.keys(cart).length > 0;
                if (!hasItems) {
                    submitBtn.disabled = true;
                    return;
                }
                if (selectedPayment === 'tunai') {
                    const cashVal = parseInt(cashInput.value) || 0;
                    submitBtn.disabled = cashVal < currentTotal;
                } else {
                    submitBtn.disabled = false;
                }
            }

            // ═══ RESET FORM ═══
            function resetForm() {
                cart = {};
                currentTotal = 0;
                renderCart();
                document.getElementById('tableNumber').value = '';
                document.getElementById('orderNote').value = '';
                cashInput.value = '';
                cashChangeRow.style.display = 'none';
            }

            // ═══ SUBMIT ORDER ═══
            submitBtn.addEventListener('click', async () => {
                const tableNumber = document.getElementById('tableNumber').value;
                if (!tableNumber || parseInt(tableNumber) < 1) {
                    alert('Harap isi nomor meja!');
                    return;
                }

                const items = Object.values(cart).map(i => ({
                    id: i.id,
                    qty: i.qty
                }));
                if (items.length === 0) return;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Memproses...';

                try {
                    const res = await fetch("{{ route('pos.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            items,
                            table_number: parseInt(tableNumber),
                            payment_method: selectedPayment,
                            note: document.getElementById('orderNote').value || null,
                        }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        // ── QRIS: Open Midtrans Snap Popup ──
                        if (data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: async function(result) {
                                    // Update status ke settlement
                                    await fetch(`/pos/update-status/${data.order_code}`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                    'meta[name="csrf-token"]')
                                                .content,
                                            'Accept': 'application/json',
                                        },
                                    });
                                    alert('✅ Pembayaran QRIS berhasil!\nKode: ' + data
                                        .order_code);
                                    resetForm();
                                },
                                onPending: function(result) {
                                    alert('⏳ Pembayaran sedang diproses...\nKode: ' + data
                                        .order_code);
                                    resetForm();
                                },
                                onError: function(result) {
                                    alert(
                                        '❌ Pembayaran gagal. Pesanan tetap tersimpan dengan status pending.'
                                    );
                                },
                                onClose: function() {
                                    // User menutup popup tanpa bayar
                                    alert('⚠️ Pembayaran belum selesai.\nPesanan tersimpan dengan status pending.\nKode: ' +
                                        data.order_code);
                                    resetForm();
                                },
                            });
                        } else {
                            // ── TUNAI: langsung selesai ──
                            alert('✅ ' + data.message + '\nKode: ' + data.order_code);
                            resetForm();
                        }
                    } else {
                        alert('❌ ' + (data.message || 'Gagal membuat pesanan'));
                    }
                } catch (err) {
                    alert('❌ Terjadi kesalahan: ' + err.message);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Buat Pesanan';
                }
            });
        });
    </script>
@endsection
