<x-layouts.app :title="'POS Kasir'">
    <div class="space-y-6">
        <section class="pos-panel overflow-hidden">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">POS Kasir</p>
                    <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">Transaksi penjualan cepat</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Tampilan kasir dengan pencarian produk, kartu item berwarna, dan keranjang di sisi kanan seperti mockup POS modern.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 px-4 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Cabang</div>
                        <div class="mt-2 font-semibold text-slate-950" data-active-branch-label>{{ $branches->first()?->name ?? 'Pilih cabang' }}</div>
                    </div>
                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 px-4 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Item</div>
                        <div class="mt-2 font-semibold text-slate-950" data-cart-count>0 produk</div>
                    </div>
                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 px-4 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total</div>
                        <div class="mt-2 font-semibold text-slate-950" data-grand-total>Rp 0</div>
                    </div>
                </div>
            </div>
        </section>

        <form action="{{ route('sales.store') }}" method="POST" class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_430px] xl:items-start" id="pos-form">
            @csrf

            <div class="space-y-6 xl:max-h-[calc(100vh-8rem)] xl:overflow-y-auto xl:pr-2">
                <section class="pos-panel">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Cabang</label>
                            <select
                                name="branch_id"
                                id="branch_id"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                                required
                            >
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('branch_id', $branches->first()?->id) == $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Kasir</label>
                            <select
                                name="cashier_id"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                            >
                                <option value="">Pilih kasir</option>
                                @foreach ($cashiers as $cashier)
                                    <option value="{{ $cashier->id }}" @selected(old('cashier_id') == $cashier->id)>{{ $cashier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Pelanggan</label>
                            <select
                                name="customer_id"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                            >
                                <option value="">Umum</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                            <input
                                type="text"
                                name="notes"
                                value="{{ old('notes') }}"
                                placeholder="Catatan transaksi"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                            >
                        </div>
                    </div>
                </section>

                <section class="pos-panel">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                id="product-search"
                                placeholder="Cari produk atau scan barcode..."
                                class="w-full rounded-[24px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:outline-none"
                            >
                        </div>

                        <div class="flex flex-wrap gap-2" id="category-filters">
                            <button type="button" data-category="Semua" class="rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition">Semua</button>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" id="product-grid"></div>

                    <div id="empty-products" class="mt-6 hidden rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-8 text-center text-sm text-slate-500">
                        Tidak ada produk aktif untuk filter atau cabang ini.
                    </div>
                </section>
            </div>

            <aside class="pos-panel h-fit xl:sticky xl:top-6 xl:self-start xl:flex xl:max-h-[calc(100vh-8rem)] xl:flex-col xl:overflow-hidden">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Keranjang</h3>
                        <p class="mt-1 text-sm text-slate-500" data-cart-caption>0 item dipilih</p>
                    </div>
                    <button type="button" id="clear-cart" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Batal</button>
                </div>

                <div class="mt-5 space-y-3 xl:min-h-0 xl:flex-1 xl:overflow-y-auto xl:pr-1" id="cart-items"></div>

                <div id="empty-cart" class="mt-5 rounded-[22px] border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Pilih produk dari panel kiri untuk mulai transaksi.
                </div>

                <div class="mt-6 shrink-0 space-y-4 border-t border-slate-200 pt-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Diskon Transaksi</label>
                        <input
                            type="number"
                            step="0.01"
                            name="discount"
                            id="discount"
                            value="{{ old('discount', 0) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Pajak</label>
                        <input
                            type="number"
                            step="0.01"
                            name="tax"
                            id="tax"
                            value="{{ old('tax', 0) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Jumlah Bayar</label>
                        <input
                            type="number"
                            step="0.01"
                            name="paid_amount"
                            id="paid_amount"
                            value="{{ old('paid_amount', 0) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none"
                        >
                    </div>
                </div>

                <div class="mt-6 shrink-0 space-y-3 rounded-[24px] bg-slate-50 px-4 py-5">
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span data-subtotal>Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Diskon</span>
                        <span data-discount-value>- Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Pajak</span>
                        <span data-tax-value>Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-lg font-semibold text-slate-950">
                        <span>Total</span>
                        <span data-total>Rp 0</span>
                    </div>
                </div>

                <input type="hidden" name="items_json" id="items_json" value="{{ old('items_json', '[]') }}">

                <button
                    type="submit"
                    id="submit-sale"
                    class="mt-6 inline-flex w-full shrink-0 items-center justify-center rounded-[24px] bg-blue-600 px-5 py-4 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none"
                >
                    Bayar Sekarang
                </button>
            </aside>
        </form>
    </div>

    <script>
        (function () {
            const catalog = @json($catalog);
            const branchSelect = document.getElementById('branch_id');
            const searchInput = document.getElementById('product-search');
            const categoryFilters = document.getElementById('category-filters');
            const productGrid = document.getElementById('product-grid');
            const emptyProducts = document.getElementById('empty-products');
            const cartItems = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const clearCartButton = document.getElementById('clear-cart');
            const discountInput = document.getElementById('discount');
            const taxInput = document.getElementById('tax');
            const paidAmountInput = document.getElementById('paid_amount');
            const itemsJsonInput = document.getElementById('items_json');
            const submitButton = document.getElementById('submit-sale');
            const activeBranchLabel = document.querySelector('[data-active-branch-label]');
            const cartCaption = document.querySelector('[data-cart-caption]');
            const cartCountLabel = document.querySelector('[data-cart-count]');
            const subtotalLabel = document.querySelector('[data-subtotal]');
            const discountLabel = document.querySelector('[data-discount-value]');
            const taxLabel = document.querySelector('[data-tax-value]');
            const totalLabel = document.querySelector('[data-total]');
            const grandTotalLabel = document.querySelector('[data-grand-total]');
            const form = document.getElementById('pos-form');

            const cardClasses = [
                'bg-gradient-to-br from-sky-500 via-blue-500 to-indigo-500',
                'bg-gradient-to-br from-emerald-500 via-teal-400 to-cyan-400',
                'bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500',
                'bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400',
                'bg-gradient-to-br from-pink-500 via-rose-500 to-red-400',
            ];

            let activeCategory = 'Semua';
            let cart = [];

            try {
                const oldCart = JSON.parse(itemsJsonInput.value || '[]');
                if (Array.isArray(oldCart)) {
                    cart = oldCart.map((item) => ({
                        product_id: Number(item.product_id),
                        name: item.name || '',
                        quantity: Number(item.quantity || 1),
                        unit_price: Number(item.unit_price || 0),
                        total: Number(item.total || 0),
                        max_stock: Number(item.max_stock || 0),
                    }));
                }
            } catch (error) {
                cart = [];
            }

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0,
                }).format(Number(value || 0));
            }

            function activeBranchId() {
                return String(branchSelect.value || '');
            }

            function availableProducts() {
                return catalog
                    .map((product, index) => {
                        const branch = product.branches?.[activeBranchId()];

                        if (!branch || Number(branch.stock) <= 0) {
                            return null;
                        }

                        return {
                            ...product,
                            price: Number(branch.price || product.base_price || 0),
                            stock: Number(branch.stock || 0),
                            stockLabel: `${Math.floor(Number(branch.stock || 0))} stok`,
                            cardClass: cardClasses[index % cardClasses.length],
                        };
                    })
                    .filter(Boolean);
            }

            function visibleCategories() {
                return [...new Set(availableProducts().map((product) => product.category).filter(Boolean))];
            }

            function filteredProducts() {
                const keyword = searchInput.value.toLowerCase().trim();

                return availableProducts().filter((product) => {
                    const matchesCategory = activeCategory === 'Semua' || product.category === activeCategory;
                    const matchesSearch = !keyword
                        || product.name.toLowerCase().includes(keyword)
                        || String(product.sku || '').toLowerCase().includes(keyword)
                        || String(product.barcode || '').toLowerCase().includes(keyword);

                    return matchesCategory && matchesSearch;
                });
            }

            function syncCartWithBranch() {
                const branchId = activeBranchId();

                cart = cart
                    .map((item) => {
                        const product = catalog.find((entry) => entry.id === item.product_id);
                        const branch = product?.branches?.[branchId];

                        if (!product || !branch) {
                            return null;
                        }

                        return {
                            ...item,
                            name: product.name,
                            unit_price: Number(branch.price || item.unit_price || 0),
                            max_stock: Number(branch.stock || 0),
                            quantity: Math.min(Number(item.quantity || 1), Number(branch.stock || 0)),
                        };
                    })
                    .filter(Boolean)
                    .filter((item) => item.quantity > 0)
                    .map((item) => ({
                        ...item,
                        total: item.quantity * item.unit_price,
                    }));
            }

            function buildCategoryFilters() {
                const categories = ['Semua', ...visibleCategories()];
                categoryFilters.innerHTML = '';

                categories.forEach((category) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.dataset.category = category;
                    button.textContent = category;
                    button.className = `rounded-2xl px-4 py-2 text-sm font-semibold transition ${
                        activeCategory === category
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25'
                            : 'bg-slate-100 text-slate-600'
                    }`;
                    button.addEventListener('click', () => {
                        activeCategory = category;
                        buildCategoryFilters();
                        renderProducts();
                    });
                    categoryFilters.appendChild(button);
                });
            }

            function addToCart(productId) {
                const product = availableProducts().find((entry) => entry.id === productId);
                if (!product) {
                    return;
                }

                const existing = cart.find((item) => item.product_id === productId);
                if (existing) {
                    increaseQuantity(productId);
                    return;
                }

                cart.push({
                    product_id: product.id,
                    name: product.name,
                    quantity: 1,
                    unit_price: product.price,
                    total: product.price,
                    max_stock: product.stock,
                });

                renderCart();
            }

            function increaseQuantity(productId) {
                cart = cart.map((item) => {
                    if (item.product_id !== productId) {
                        return item;
                    }

                    const nextQuantity = Math.min(item.quantity + 1, item.max_stock);
                    return {
                        ...item,
                        quantity: nextQuantity,
                        total: nextQuantity * item.unit_price,
                    };
                });

                renderCart();
            }

            function decreaseQuantity(productId) {
                const current = cart.find((item) => item.product_id === productId);
                if (!current) {
                    return;
                }

                if (current.quantity <= 1) {
                    removeItem(productId);
                    return;
                }

                cart = cart.map((item) => {
                    if (item.product_id !== productId) {
                        return item;
                    }

                    const nextQuantity = item.quantity - 1;
                    return {
                        ...item,
                        quantity: nextQuantity,
                        total: nextQuantity * item.unit_price,
                    };
                });

                renderCart();
            }

            function removeItem(productId) {
                cart = cart.filter((item) => item.product_id !== productId);
                renderCart();
            }

            function subtotal() {
                return cart.reduce((sum, item) => sum + item.total, 0);
            }

            function total() {
                return Math.max(0, subtotal() - Number(discountInput.value || 0) + Number(taxInput.value || 0));
            }

            function renderProducts() {
                const products = filteredProducts();
                productGrid.innerHTML = '';
                emptyProducts.classList.toggle('hidden', products.length > 0);

                products.forEach((product) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'group overflow-hidden rounded-[28px] border border-slate-200 bg-white text-left shadow-[0_12px_35px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(37,99,235,0.16)]';
                    button.innerHTML = `
                        <div class="${product.cardClass} relative px-5 py-6 text-white">
                            <div class="absolute right-4 top-4 rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold">${product.stockLabel}</div>
                            <div class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">${product.category}</div>
                            <div class="mt-8 text-lg font-semibold leading-6">${product.name}</div>
                        </div>
                        <div class="flex items-end justify-between gap-3 px-5 py-4">
                            <div>
                                <div class="text-lg font-semibold text-slate-950">${formatCurrency(product.price)}</div>
                                <div class="mt-1 text-xs text-slate-400">${product.sku ?? ''}</div>
                            </div>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white transition group-hover:bg-slate-950">+</span>
                        </div>
                    `;
                    button.addEventListener('click', () => addToCart(product.id));
                    productGrid.appendChild(button);
                });
            }

            function renderCart() {
                cartItems.innerHTML = '';
                emptyCart.classList.toggle('hidden', cart.length > 0);

                cart.forEach((item) => {
                    const row = document.createElement('div');
                    row.className = 'rounded-[22px] border border-slate-200 bg-white px-4 py-4';
                    row.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate font-semibold text-slate-950">${item.name}</div>
                                <div class="mt-1 text-sm text-slate-400">${formatCurrency(item.unit_price)} x ${item.quantity}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-slate-950">${formatCurrency(item.total)}</div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-2 py-1">
                                <button type="button" data-action="minus" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-slate-700">-</button>
                                <span class="min-w-6 text-center text-sm font-semibold text-slate-900">${item.quantity}</span>
                                <button type="button" data-action="plus" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-slate-700">+</button>
                            </div>
                            <button type="button" data-action="remove" class="text-sm font-semibold text-slate-400 hover:text-rose-500">Hapus</button>
                        </div>
                    `;

                    row.querySelector('[data-action="minus"]').addEventListener('click', () => decreaseQuantity(item.product_id));
                    row.querySelector('[data-action="plus"]').addEventListener('click', () => increaseQuantity(item.product_id));
                    row.querySelector('[data-action="remove"]').addEventListener('click', () => removeItem(item.product_id));

                    cartItems.appendChild(row);
                });

                const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
                const currentSubtotal = subtotal();
                const currentDiscount = Number(discountInput.value || 0);
                const currentTax = Number(taxInput.value || 0);
                const currentTotal = total();

                cartCaption.textContent = `${itemCount} item dipilih`;
                cartCountLabel.textContent = `${itemCount} produk`;
                subtotalLabel.textContent = formatCurrency(currentSubtotal);
                discountLabel.textContent = `- ${formatCurrency(currentDiscount)}`;
                taxLabel.textContent = formatCurrency(currentTax);
                totalLabel.textContent = formatCurrency(currentTotal);
                grandTotalLabel.textContent = formatCurrency(currentTotal);
                itemsJsonInput.value = JSON.stringify(cart.map((item) => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    discount: 0,
                    total: item.total,
                    name: item.name,
                    max_stock: item.max_stock,
                })));

                if (Number(paidAmountInput.value || 0) === 0) {
                    paidAmountInput.value = currentTotal;
                }

                submitButton.disabled = cart.length === 0;
            }

            function updateBranchLabel() {
                const label = branchSelect.options[branchSelect.selectedIndex]?.text ?? 'Pilih cabang';
                activeBranchLabel.textContent = label;
            }

            branchSelect.addEventListener('change', () => {
                updateBranchLabel();
                syncCartWithBranch();
                buildCategoryFilters();
                renderProducts();
                renderCart();
            });

            searchInput.addEventListener('input', renderProducts);
            discountInput.addEventListener('input', renderCart);
            taxInput.addEventListener('input', renderCart);
            clearCartButton.addEventListener('click', () => {
                cart = [];
                renderCart();
            });

            form.addEventListener('submit', () => {
                if (Number(paidAmountInput.value || 0) === 0) {
                    paidAmountInput.value = total();
                }
            });

            syncCartWithBranch();
            updateBranchLabel();
            buildCategoryFilters();
            renderProducts();
            renderCart();
        })();
    </script>
</x-layouts.app>
