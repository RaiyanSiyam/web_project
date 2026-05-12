// assets/js/billing.js
document.addEventListener('DOMContentLoaded', () => {
    let cart = [];
    const VAT_RATE = 0.05;

    // DOM Elements
    const searchInput = document.getElementById('productSearch');
    const searchDropdown = document.getElementById('searchDropdown');
    const cartBody = document.getElementById('cartBody');
    const emptyCartRow = document.getElementById('emptyCartRow');
    
    // Summary Elements
    const elSubtotal = document.getElementById('sumSubtotal');
    const elVat = document.getElementById('sumVat');
    const elTotal = document.getElementById('sumTotal');
    const elChange = document.getElementById('sumChange');
    const inpDiscount = document.getElementById('inpDiscount');
    const inpTendered = document.getElementById('inpTendered');
    
    // Buttons
    const btnCheckout = document.getElementById('btnCheckout');
    const btnClearCart = document.getElementById('btnClearCart');

    // --- Search Logic ---
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        searchDropdown.innerHTML = '';
        
        if (query.length < 2) {
            searchDropdown.classList.add('hidden');
            return;
        }

        const filtered = posProducts.filter(p => 
            p.name.toLowerCase().includes(query) || 
            p.sku.toLowerCase().includes(query)
        ).slice(0, 10); // Limit to 10 results

        if (filtered.length > 0) {
            filtered.forEach(p => {
                const div = document.createElement('div');
                div.className = 'search-item';
                div.innerHTML = `
                    <div class="search-item-info">
                        <strong>${p.name}</strong>
                        <small>${p.sku} | Stock: ${p.stock_quantity}</small>
                    </div>
                    <div class="search-item-price">৳${parseFloat(p.price).toFixed(2)}</div>
                `;
                div.addEventListener('click', () => {
                    addToCart(p);
                    searchInput.value = '';
                    searchDropdown.innerHTML = '';
                    searchDropdown.classList.add('hidden');
                    searchInput.focus();
                });
                searchDropdown.appendChild(div);
            });
            searchDropdown.classList.remove('hidden');
        } else {
            searchDropdown.classList.add('hidden');
        }
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.add('hidden');
        }
    });

    // --- Cart Logic ---
    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        
        if (existingItem) {
            if (existingItem.qty < product.stock_quantity) {
                existingItem.qty++;
            } else {
                alert('Maximum stock reached for this item.');
            }
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                qty: 1,
                maxStock: product.stock_quantity
            });
        }
        renderCart();
    }

    function renderCart() {
        if (cart.length === 0) {
            cartBody.innerHTML = '';
            emptyCartRow.innerHTML = `<td colspan="5" class="text-muted" style="padding: 60px 0;"><div style="text-align: center; margin-left: 20px;">Cart is empty. Search products to add.</div></td>`;
            cartBody.appendChild(emptyCartRow);
            emptyCartRow.style.display = 'table-row';
        } else {
            emptyCartRow.style.display = 'none';
            cartBody.innerHTML = '';
            
            cart.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${item.name}</strong></td>
                    <td>৳${item.price.toFixed(2)}</td>
                    <td>
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                            <input type="text" class="qty-input" value="${item.qty}" readonly>
                            <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td style="font-weight:600;">৳${(item.price * item.qty).toFixed(2)}</td>
                    <td>
                        <button class="item-remove" onclick="removeFromCart(${index})">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </td>
                `;
                cartBody.appendChild(tr);
            });
        }
        calculateTotals();
    }

    window.updateQty = function(index, change) {
        const item = cart[index];
        const newQty = item.qty + change;
        if (newQty > 0 && newQty <= item.maxStock) {
            item.qty = newQty;
            renderCart();
        } else if (newQty > item.maxStock) {
            alert('Cannot exceed available stock.');
        }
    };

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        renderCart();
    };

    btnClearCart.addEventListener('click', () => {
        if (cart.length > 0 && confirm('Clear cart?')) {
            cart = [];
            renderCart();
        }
    });

    // --- Math & Checkout Logic ---
    let finalGrandTotal = 0;

    function calculateTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.price * item.qty;
        });

        let discount = parseFloat(inpDiscount.value) || 0;
        if (discount > subtotal) discount = subtotal; // Prevent over-discounting
        
        let subAfterDiscount = subtotal - discount;
        let vat = subAfterDiscount * VAT_RATE;
        finalGrandTotal = subAfterDiscount + vat;

        elSubtotal.innerText = `৳${subtotal.toFixed(2)}`;
        elVat.innerText = `৳${vat.toFixed(2)}`;
        elTotal.innerText = `৳${finalGrandTotal.toFixed(2)}`;
        
        calculateChange();
        
        // Enable/Disable checkout button
        btnCheckout.disabled = cart.length === 0;
    }

    function calculateChange() {
        let tendered = parseFloat(inpTendered.value) || 0;
        let change = tendered - finalGrandTotal;
        
        if (tendered >= finalGrandTotal && cart.length > 0) {
            elChange.innerText = `৳${change.toFixed(2)}`;
            elChange.style.color = '#059669'; // Green
            btnCheckout.disabled = false;
        } else {
            elChange.innerText = `৳0.00`;
            elChange.style.color = '#dc2626'; // Red
            btnCheckout.disabled = true;
        }
    }

    inpDiscount.addEventListener('input', calculateTotals);
    inpTendered.addEventListener('input', calculateChange);

    // --- AJAX Checkout Submit ---
    btnCheckout.addEventListener('click', () => {
        if (cart.length === 0) return;

        const payload = {
            customer_name: document.getElementById('custName').value.trim(),
            customer_phone: document.getElementById('custPhone').value.trim(),
            discount: parseFloat(inpDiscount.value) || 0,
            amount_paid: parseFloat(inpTendered.value) || 0,
            cart: cart.map(item => ({ id: item.id, qty: item.qty }))
        };

        // UI Loading State
        btnCheckout.disabled = true;
        document.querySelector('.btn-text').classList.add('hidden');
        document.getElementById('checkoutLoader').classList.remove('hidden');

        fetch('../../app/controllers/BillingController.php?action=process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Trigger Custom Success Modal instead of alert()
                const successModal = document.getElementById('universalSuccessModal');
                document.getElementById('successModalTitle').innerText = "Sale Successful!";
                document.getElementById('successModalMessage').innerText = `Invoice Number: ${data.invoice_number}`;
                successModal.classList.remove('hidden');

                // Reload page when user clicks Continue
                document.getElementById('successModalCloseBtn').addEventListener('click', () => {
                    location.reload(); 
                });
            } else {
                alert(`Error: ${data.message}`); // Keep alert for actual errors, or map it to an error modal
                // Reset UI state
                btnCheckout.disabled = false;
                document.querySelector('.btn-text').classList.remove('hidden');
                document.getElementById('checkoutLoader').classList.add('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            alert('A network error occurred. Please try again.');
            location.reload();
        });
    });
});