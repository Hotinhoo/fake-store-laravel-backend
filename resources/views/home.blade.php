<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FakeStore Admin - Laravel API</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Phosphor Icons para icones bonitos e leves -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Fonte Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: '#1a56db',
                        surface: '#f3f4f6',
                        cardbg: '#f0f2f5'
                    }
                }
            }
        }
    </script>
    <style>
        /* Customizações finas para ficar pixel-perfect com a imagem */
        body { background-color: #ffffff; color: #111827; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }
        
        .radio-custom {
            appearance: none;
            width: 16px; height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            display: inline-block;
            position: relative;
            cursor: pointer;
        }
        .radio-custom:checked {
            background-color: #1a56db; border-color: #1a56db;
        }
        .radio-custom:checked::after {
            content: ''; position: absolute;
            top: 2px; left: 5px; width: 4px; height: 8px;
            border: solid white; border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
    </style>
</head>
<body class="antialiased h-screen flex overflow-hidden">

    <!-- SIDEBAR (Filtros) -->
    <aside class="w-72 border-r border-gray-100 p-6 flex flex-col h-full overflow-y-auto custom-scrollbar shrink-0">
        <h2 class="text-2xl font-bold mb-8">Filters</h2>

        <!-- Busca por texto (Customizado para nossa API) -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4 flex justify-between items-center">
                Search
                <i class="ph ph-caret-up text-gray-400"></i>
            </h3>
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                <input type="text" id="filter-search" placeholder="Product title..." 
                       class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
            </div>
        </div>

        <!-- Categorias (Dinâmico) -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4 flex justify-between items-center">
                Categories
                <i class="ph ph-caret-up text-gray-400"></i>
            </h3>
            <div id="filter-categories" class="space-y-3 text-sm text-gray-600">
                <!-- Preenchido via JS -->
                <div class="animate-pulse flex space-x-2"><div class="h-4 w-4 bg-gray-200 rounded"></div><div class="h-4 w-24 bg-gray-200 rounded"></div></div>
            </div>
        </div>

        <!-- Rating -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4 flex justify-between items-center">
                Rating Min.
                <i class="ph ph-caret-up text-gray-400"></i>
            </h3>
            <div class="space-y-3 text-sm text-gray-600">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="rating" value="4" class="radio-custom" onchange="applyFilters()">
                    <span>4.0 and above</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="rating" value="3" class="radio-custom" onchange="applyFilters()">
                    <span>3.0 and above</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="rating" value="" class="radio-custom" checked onchange="applyFilters()">
                    <span>Any rating</span>
                </label>
            </div>
        </div>

        <!-- Preço -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4 flex justify-between items-center">
                Price
                <i class="ph ph-caret-up text-gray-400"></i>
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Minimum</label>
                    <input type="number" id="filter-price-min" placeholder="$ 0.00" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand" onchange="applyFilters()">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Maximum</label>
                    <input type="number" id="filter-price-max" placeholder="$ 999.00" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand" onchange="applyFilters()">
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full bg-white relative">
        
        <!-- Header Principal -->
        <header class="p-6 border-b border-gray-50 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-semibold">Products Grid</h1>
                <div id="stats-pill" class="hidden items-center gap-2 bg-blue-50 text-brand px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="ph-fill ph-database"></i> <span id="total-products-count">0</span> items total
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="importProducts()" id="btn-import" class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition shadow-sm">
                    <i class="ph ph-cloud-arrow-down text-lg"></i> Import from FakeStore
                </button>
                
                <div class="flex items-center gap-2 text-sm border border-gray-200 rounded-lg px-3 py-2">
                    <span class="text-gray-500">Sort by:</span>
                    <select id="sort-by" class="font-medium bg-transparent focus:outline-none cursor-pointer" onchange="applyFilters()">
                        <option value="id-asc">Recent</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="rating_rate-desc">Highest Rated</option>
                    </select>
                </div>
            </div>
        </header>

        <!-- Product Grid Area -->
        <div class="flex-1 overflow-y-auto p-6 bg-white custom-scrollbar" id="grid-container">
            <!-- Loading State -->
            <div id="loading-state" class="hidden flex justify-center items-center h-64">
                <i class="ph ph-spinner animate-spin text-4xl text-brand"></i>
            </div>
            
            <!-- Grid -->
            <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Itens injetados aqui -->
            </div>
            
            <!-- Pagination -->
            <div id="pagination-controls" class="mt-10 flex justify-center items-center gap-2 pb-6">
            </div>
        </div>
    </main>

    <!-- MODAL: DETALHES, EDIÇÃO E REMOÇÃO -->
    <div id="product-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl transform transition-all flex flex-col max-h-[90vh]">
            
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold" id="modal-title-display">Product Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 bg-gray-100 p-2 rounded-full">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                <form id="edit-form" class="space-y-5">
                    <input type="hidden" id="modal-id">
                    <input type="hidden" id="modal-rating-val">
                    
                    <div class="flex gap-6 mb-6 items-start">
                        <div class="w-32 h-32 bg-cardbg rounded-xl p-2 flex items-center justify-center shrink-0">
                            <img id="modal-image" src="" class="max-h-full max-w-full object-contain mix-blend-multiply">
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                                <i class="ph-fill ph-star text-yellow-400 text-lg"></i>
                                <span id="modal-rating">5.0</span>
                                <span>&bull;</span>
                                <span id="modal-price-tax" class="text-green-600 bg-green-50 px-2 py-0.5 rounded text-xs">Tax included: $0.00</span>
                            </div>
                            <p id="modal-desc" class="text-sm text-gray-600 line-clamp-3"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" id="modal-title" required minlength="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand">
                            <p class="text-xs text-gray-500 mt-1">Min 3 characters (API Rule)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price ($) <span class="text-red-500">*</span></label>
                            <input type="number" id="modal-price" required min="0.01" step="0.01" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand">
                            <p class="text-xs text-gray-500 mt-1">Must be > 0 (API Rule)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <input type="text" id="modal-category" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand">
                        </div>
                    </div>
                </form>

                <!-- Área de Deleção -->
                <div class="mt-8 pt-6 border-t border-red-100 bg-red-50/50 -mx-6 px-6 pb-2 rounded-b-2xl">
                    <h4 class="text-red-600 font-semibold mb-2 flex items-center gap-2">
                        <i class="ph ph-warning-circle"></i> Danger Zone
                    </h4>
                    <div id="delete-rules-msg" class="text-sm text-red-500 mb-3 hidden font-medium">
                        Deleção bloqueada: Produtos com rating > 4.5 não podem ser removidos (API Rule).
                    </div>
                    <div id="delete-area" class="space-y-3">
                        <input type="text" id="delete-reason" placeholder="Motivo da remoção (Obrigatório min 5 chars)..." class="w-full px-4 py-2 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm">
                        <button onclick="deleteProduct()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium transition text-sm">
                            Confirmar Soft Delete
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button onclick="closeModal()" class="px-5 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg transition">Cancel</button>
                <button onclick="updateProduct()" class="px-5 py-2 bg-brand hover:bg-blue-700 text-white font-medium rounded-lg transition flex items-center gap-2">
                    <i class="ph ph-floppy-disk"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-5 right-5 transform translate-y-20 opacity-0 transition-all duration-300 z-50 flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg font-medium text-sm">
        <i id="toast-icon" class="text-xl"></i>
        <span id="toast-msg"></span>
    </div>

    <!-- SCRIPT DE INTEGRAÇÃO COM A API DO LARAVEL -->
    <script>
        // Aponta para a API na mesma origem em que a interface está rodando
        const API_BASE = window.location.origin + '/api/products';
        
        let currentPage = 1;

        // Inicia a página
        document.addEventListener('DOMContentLoaded', () => {
            loadStats();
            applyFilters();

            // Adiciona delay na busca por texto para não floodar a API
            let timeout = null;
            document.getElementById('filter-search').addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(applyFilters, 500);
            });
        });

        // ==========================================
        // 1. CARREGAR ESTATÍSTICAS E CATEGORIAS
        // ==========================================
        async function loadStats() {
            try {
                const res = await fetch(`${API_BASE}/stats`);
                if(!res.ok) throw new Error('API Offline');
                const stats = await res.json();
                
                // Atualiza Pill de Total
                document.getElementById('stats-pill').classList.replace('hidden', 'flex');
                document.getElementById('total-products-count').innerText = stats.total_products;

                // Renderiza categorias no menu lateral
                const catContainer = document.getElementById('filter-categories');
                catContainer.innerHTML = '';
                
                // Opção 'Todas'
                catContainer.innerHTML += `
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="category" value="" class="radio-custom" checked onchange="applyFilters()">
                        <span class="capitalize">All Categories</span>
                    </label>
                `;

                Object.entries(stats.categories_count).forEach(([cat, count]) => {
                    catContainer.innerHTML += `
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="category" value="${cat}" class="radio-custom" onchange="applyFilters()">
                            <span class="capitalize">${cat} <span class="text-gray-400 text-xs ml-1">(${count})</span></span>
                        </label>
                    `;
                });
            } catch (error) {
                console.error("Erro ao carregar estatísticas:", error);
            }
        }

        // ==========================================
        // 2. LISTAGEM E FILTROS COMBINADOS
        // ==========================================
        async function applyFilters(page = 1) {
            currentPage = page;
            showLoading(true);

            // Coleta valores do sidebar
            const search = document.getElementById('filter-search').value;
            const category = document.querySelector('input[name="category"]:checked')?.value || '';
            const ratingMin = document.querySelector('input[name="rating"]:checked')?.value || '';
            const priceMin = document.getElementById('filter-price-min').value;
            const priceMax = document.getElementById('filter-price-max').value;
            
            // Coleta ordenação
            const sortVal = document.getElementById('sort-by').value;
            const [sortBy, sortDir] = sortVal.split('-');

            // Monta Query String para a API Laravel
            const params = new URLSearchParams({
                page: currentPage,
                per_page: 12,
            });

            if (search.length >= 3) params.append('search', search); // Regra API min:3
            if (category) params.append('category', category);
            if (ratingMin) params.append('rating_min', ratingMin);
            if (priceMin) params.append('price_min', priceMin);
            if (priceMax) params.append('price_max', priceMax);
            if (sortBy !== 'id') {
                params.append('sort_by', sortBy);
                params.append('sort_dir', sortDir);
            }

            try {
                const res = await fetch(`${API_BASE}?${params.toString()}`);
                const responseData = await res.json();
                
                // Verifica formato padrão do API Resource do Laravel
                const products = responseData.data || [];
                const meta = responseData.meta || { current_page: 1, last_page: 1 };
                
                renderGrid(products);
                renderPagination(meta);
            } catch (error) {
                showToast("Erro ao buscar produtos. A API está rodando?", "error");
            } finally {
                showLoading(false);
            }
        }

        function renderGrid(products) {
            const grid = document.getElementById('products-grid');
            grid.innerHTML = '';

            if(products.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-20 text-gray-400">Nenhum produto encontrado com os filtros atuais.</div>`;
                return;
            }

            products.forEach(p => {
                // Design Pixel-Perfect inspirado na imagem
                const card = `
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 hover:shadow-xl transition-shadow cursor-pointer group flex flex-col h-full" onclick="openModal(${p.id})">
                        <!-- Imagem com bg cinza do mockup -->
                        <div class="bg-cardbg rounded-xl h-56 mb-4 flex items-center justify-center p-6 relative overflow-hidden group-hover:bg-gray-200 transition-colors">
                            <img src="${p.image || 'https://via.placeholder.com/150'}" alt="${p.title}" class="max-h-full max-w-full object-contain mix-blend-multiply">
                            
                            <!-- Badges visuais -->
                            <div class="absolute top-3 left-3 bg-white/80 backdrop-blur px-2 py-1 rounded text-[10px] font-bold text-gray-600 uppercase tracking-wide border border-gray-200">
                                ${p.category}
                            </div>
                        </div>
                        
                        <!-- Infos -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-2xl font-bold text-gray-900 leading-none">$${p.price.toFixed(2)}</h3>
                                    <!-- Pontinhos coloridos puramente estéticos baseados no mockup -->
                                    <div class="flex flex-col gap-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-800"></div>
                                        <div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div>
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-300"></div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-snug">${p.title}</p>
                            </div>
                            
                            <!-- Rating Footer -->
                            <div class="flex justify-between items-center text-xs font-medium text-gray-500 pt-3 border-t border-gray-50">
                                <div class="flex items-center gap-1">
                                    <i class="ph-fill ph-star text-yellow-400 text-sm"></i>
                                    <span class="text-gray-900">${parseFloat(p.rating?.rate || 0).toFixed(1)}</span>
                                </div>
                                <div>${p.rating?.count || 0} Sold</div>
                            </div>
                        </div>
                    </div>
                `;
                grid.innerHTML += card;
            });
        }

        function renderPagination(meta) {
            const container = document.getElementById('pagination-controls');
            container.innerHTML = '';
            
            if (meta.last_page <= 1) return;

            const prevDisabled = meta.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100';
            const nextDisabled = meta.current_page === meta.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100';

            container.innerHTML = `
                <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium transition ${prevDisabled}" 
                        onclick="${meta.current_page > 1 ? `applyFilters(${meta.current_page - 1})` : ''}">
                    Previous
                </button>
                <span class="text-sm font-medium text-gray-600 mx-4">Page ${meta.current_page} of ${meta.last_page}</span>
                <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium transition ${nextDisabled}" 
                        onclick="${meta.current_page < meta.last_page ? `applyFilters(${meta.current_page + 1})` : ''}">
                    Next
                </button>
            `;
        }

        // ==========================================
        // 3. IMPORTAÇÃO (Dispara o Endpoint que criamos)
        // ==========================================
        async function importProducts() {
            const btn = document.getElementById('btn-import');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = `<i class="ph ph-spinner animate-spin text-lg"></i> Importing...`;
            btn.disabled = true;

            try {
                const res = await fetch(`${API_BASE}/import`, { method: 'POST', headers: {'Accept': 'application/json'} });
                const data = await res.json();
                
                if (res.ok) {
                    showToast(`Success! Imported: ${data.imported} | Updated: ${data.updated} | Skipped: ${data.skipped}`, 'success');
                    loadStats();
                    applyFilters(1);
                } else {
                    showToast(data.message || data.error || 'Erro na importação', 'error');
                }
            } catch (e) {
                showToast('Erro de comunicação com a API', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // ==========================================
        // 4. MODAL: DETALHES E UPDATE PARCIAL
        // ==========================================
        async function openModal(id) {
            try {
                // Busca detalhes atualizados na API
                const res = await fetch(`${API_BASE}/${id}`);
                const json = await res.json();
                const p = json.data || json; // Depende de como o resource devolve (wrap 'data' ou não)

                document.getElementById('modal-id').value = p.id;
                document.getElementById('modal-title').value = p.title;
                document.getElementById('modal-price').value = p.price;
                document.getElementById('modal-category').value = p.category;
                
                document.getElementById('modal-image').src = p.image || 'https://via.placeholder.com/150';
                document.getElementById('modal-rating').innerText = `${p.rating?.rate} (${p.rating?.count} reviews)`;
                document.getElementById('modal-desc').innerText = p.description;
                
                // Exibe o campo calculado pela API
                document.getElementById('modal-price-tax').innerText = `Tax included: $${p.price_with_tax}`;

                // Regras de Deleção
                const rating = parseFloat(p.rating?.rate || 0);
                document.getElementById('modal-rating-val').value = rating;
                
                const deleteArea = document.getElementById('delete-area');
                const rulesMsg = document.getElementById('delete-rules-msg');
                
                // Aplicando visualmente a regra: "não permitir remover produto com rating > 4.5"
                if (rating > 4.5) {
                    deleteArea.classList.add('hidden');
                    rulesMsg.classList.remove('hidden');
                } else {
                    deleteArea.classList.remove('hidden');
                    rulesMsg.classList.add('hidden');
                    document.getElementById('delete-reason').value = ''; // limpa motivo
                }

                document.getElementById('product-modal').classList.replace('hidden', 'flex');
            } catch (e) {
                showToast('Erro ao carregar detalhes', 'error');
            }
        }

        function closeModal() {
            document.getElementById('product-modal').classList.replace('flex', 'hidden');
        }

        async function updateProduct() {
            const id = document.getElementById('modal-id').value;
            const payload = {
                title: document.getElementById('modal-title').value,
                price: parseFloat(document.getElementById('modal-price').value),
                category: document.getElementById('modal-category').value
            };

            // Validação local rápida antes de bater na API
            if(payload.title.length < 3) return showToast('Title min 3 chars', 'error');
            if(payload.price <= 0 || isNaN(payload.price)) return showToast('Price must be > 0', 'error');

            try {
                const res = await fetch(`${API_BASE}/${id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                if (res.ok) {
                    showToast('Produto atualizado com sucesso e log registrado!', 'success');
                    closeModal();
                    applyFilters(currentPage);
                } else {
                    const err = await res.json();
                    showToast(err.message || 'Erro na validação da API', 'error');
                }
            } catch (e) {
                showToast('Falha na comunicação', 'error');
            }
        }

        // ==========================================
        // 5. REMOÇÃO (SOFT DELETE + MOTIVO)
        // ==========================================
        async function deleteProduct() {
            const id = document.getElementById('modal-id').value;
            const reason = document.getElementById('delete-reason').value;

            if (reason.length < 5) {
                return showToast('Motivo da remoção obrigatório (mínimo 5 caracteres).', 'error');
            }

            try {
                const res = await fetch(`${API_BASE}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ reason: reason })
                });

                if (res.ok) {
                    showToast('Produto removido via Soft Delete!', 'success');
                    closeModal();
                    loadStats(); // atualiza painel
                    applyFilters(currentPage);
                } else {
                    const err = await res.json();
                    showToast(err.error || err.message || 'Erro ao deletar', 'error');
                }
            } catch (e) {
                showToast('Falha na comunicação', 'error');
            }
        }

        // ==========================================
        // UTILITÁRIOS
        // ==========================================
        function showLoading(show) {
            document.getElementById('loading-state').classList.toggle('hidden', !show);
            if(show) document.getElementById('products-grid').innerHTML = '';
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').innerText = message;
            
            const icon = document.getElementById('toast-icon');
            if (type === 'success') {
                toast.className = 'fixed bottom-5 right-5 z-50 flex items-center gap-3 px-5 py-4 rounded-xl shadow-xl font-medium text-sm bg-gray-900 text-white transform transition-all duration-300';
                icon.className = 'ph-fill ph-check-circle text-green-400 text-xl';
            } else {
                toast.className = 'fixed bottom-5 right-5 z-50 flex items-center gap-3 px-5 py-4 rounded-xl shadow-xl font-medium text-sm bg-red-50 border border-red-200 text-red-700 transform transition-all duration-300';
                icon.className = 'ph-fill ph-warning-circle text-red-500 text-xl';
            }

            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 4000);
        }
    </script>
</body>
</html>