<x-layouts.app>
    <section class="max-w-7xl mx-auto py-12 px-6">
        <nav class="mb-6">
            <div class="breadcrumbs">
                <ul>
                    <li><a href="{{ route('home') }}" class="link link-neutral">Beranda</a></li>
                    <li><a href="#" class="link link-neutral">Event</a></li>
                    <li>{{ $event->judul }}</li>
                </ul>
            </div>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow">
                    <figure>
                        <img src="{{ $event->gambar ? asset('storage/' . $event->gambar) : 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp' }}"
                            alt="{{ $event->judul }}" class="w-full h-96 object-cover" />
                    </figure>

                    <div class="card-body">
                        <h1 class="text-3xl font-extrabold">{{ $event->judul }}</h1>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($event->tanggal_waktu)->locale('id')->translatedFormat('d F Y, H:i') }}
                            • 📍
                            {{ $event->lokasi }}
                        </p>

                        <div class="mt-3 flex gap-2 items-center">
                            <span class="badge badge-primary">{{ $event->kategori?->nama ?? 'Tanpa Kategori' }}</span>
                            <span class="badge">{{ $event->user?->name ?? 'Penyelenggara' }}</span>
                        </div>

                        <p class="mt-4 text-gray-700 leading-relaxed">{{ $event->deskripsi }}</p>

                        <div class="divider"></div>

                        <h3 class="text-xl font-bold">Pilih Tiket</h3>

                        <div class="mt-4 space-y-4">
                            @forelse($event->tikets as $tiket)
                                <div class="card card-side shadow-sm p-4 items-center">
                                    <div class="flex-1">
                                        <h4 class="font-bold">{{ $tiket->tipe }}</h4>
                                        <p class="text-sm text-gray-500">Stok: {{ $tiket->stok }}</p>
                                    </div>

                                    <div class="w-44 text-right">
                                        <div class="text-lg font-bold">
                                            {{ $tiket->harga ? 'Rp ' . number_format($tiket->harga, 0, ',', '.') : 'Gratis' }}
                                        </div>

                                        <div class="mt-3 flex items-center justify-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline" data-action="dec"
                                                data-id="{{ $tiket->id }}">−</button>

                                            <input id="qty-{{ $tiket->id }}" type="number" min="0"
                                                max="{{ $tiket->stok }}" value="0"
                                                class="input input-bordered w-16 text-center"
                                                data-id="{{ $tiket->id }}" />

                                            <button type="button" class="btn btn-sm btn-outline" data-action="inc"
                                                data-id="{{ $tiket->id }}">+</button>
                                        </div>

                                        <div class="text-sm text-gray-500 mt-2">
                                            Subtotal: <span id="subtotal-{{ $tiket->id }}">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">Tiket belum tersedia.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-1">
                <div class="card sticky top-24 p-4 bg-base-100 shadow">
                    <h4 class="font-bold text-lg">Ringkasan</h4>

                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Item</span><span id="summaryItems">0</span>
                        </div>

                        <div class="flex justify-between text-xl font-bold mt-1">
                            <span>Total</span><span id="summaryTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div id="selectedList" class="space-y-2 text-sm text-gray-700">
                        <p class="text-gray-500">Belum ada tiket dipilih</p>
                    </div>

                    @auth
                        <button id="checkoutButton" onclick="openCheckout()" disabled
                            class="btn btn-primary !bg-blue-900 text-white btn-block mt-6">
                            Checkout
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-block mt-6 text-white">Login</a>
                    @endauth
                </div>
            </aside>
        </div>

        <dialog id="checkout_modal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Konfirmasi</h3>

                <div class="mt-4 space-y-2 text-sm">
                    <div id="modalItems"></div>

                    <div class="divider"></div>

                    <div class="flex justify-between items-center">
                        <span class="font-bold">Total</span>
                        <span class="font-bold text-lg" id="modalTotal">Rp 0</span>
                    </div>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn" onclick="checkout_modal.close()">Tutup</button>
                    <button type="button" id="confirmCheckout" class="btn btn-primary !bg-blue-900 text-white">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </dialog>
    </section>

    <script>
        const formatRupiah = v => 'Rp ' + Number(v).toLocaleString('id-ID');

        const tickets = {
            @foreach ($event->tikets as $tiket)
                {{ $tiket->id }}: {
                    id: {{ $tiket->id }},
                    price: {{ $tiket->harga ?? 0 }},
                    stock: {{ $tiket->stok }},
                    tipe: "{{ e($tiket->tipe) }}"
                },
            @endforeach
        };

        const summaryItemsEl = document.getElementById('summaryItems');
        const summaryTotalEl = document.getElementById('summaryTotal');
        const selectedListEl = document.getElementById('selectedList');
        const checkoutButton = document.getElementById('checkoutButton');

        function updateSummary() {
            let totalQty = 0;
            let totalPrice = 0;
            let html = '';

            Object.values(tickets).forEach(t => {
                const qty = Number(document.getElementById('qty-' + t.id).value || 0);
                if (qty > 0) {
                    totalQty += qty;
                    totalPrice += qty * t.price;
                    html +=
                        `<div class="flex justify-between"><span>${t.tipe} x ${qty}</span><span>${formatRupiah(qty * t.price)}</span></div>`;
                }
            });

            summaryItemsEl.textContent = totalQty;
            summaryTotalEl.textContent = formatRupiah(totalPrice);
            selectedListEl.innerHTML = html || '<p class="text-gray-500">Belum ada tiket dipilih</p>';
            checkoutButton.disabled = totalQty === 0;
        }

        function updateTicketSubtotal(id) {
            const qty = Number(document.getElementById('qty-' + id).value || 0);
            document.getElementById('subtotal-' + id).textContent = formatRupiah(qty * tickets[id].price);
        }

        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.target.dataset.id;
                const input = document.getElementById('qty-' + id);
                let val = Number(input.value || 0);

                if (e.target.dataset.action === 'inc' && val < tickets[id].stock) val++;
                if (e.target.dataset.action === 'dec' && val > 0) val--;

                input.value = val;
                updateTicketSubtotal(id);
                updateSummary();
            });
        });

        function openCheckout() {
            let html = '';
            let total = 0;

            Object.values(tickets).forEach(t => {
                const qty = Number(document.getElementById('qty-' + t.id).value || 0);
                if (qty > 0) {
                    html +=
                        `<div class="flex justify-between"><span>${t.tipe} x ${qty}</span><span>${formatRupiah(qty * t.price)}</span></div>`;
                    total += qty * t.price;
                }
            });

            document.getElementById('modalItems').innerHTML = html;
            document.getElementById('modalTotal').textContent = formatRupiah(total);
            checkout_modal.showModal();
        }

        document.getElementById('confirmCheckout').addEventListener('click', async () => {
            const btn = confirmCheckout;
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            const items = [];

            Object.values(tickets).forEach(t => {
                const qty = Number(document.getElementById('qty-' + t.id).value || 0);
                if (qty > 0) items.push({
                    tiket_id: t.id,
                    jumlah: qty
                });
            });

            try {
                const res = await fetch("{{ route('orders.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        event_id: {{ $event->id }},
                        items
                    })
                });

                const data = await res.json();
                window.location.href = data.redirect || "{{ route('orders.index') }}";

            } catch (e) {
                alert('Gagal checkout');
                btn.disabled = false;
                btn.textContent = 'Konfirmasi';
            }
        });

        updateSummary();
    </script>
</x-layouts.app>
