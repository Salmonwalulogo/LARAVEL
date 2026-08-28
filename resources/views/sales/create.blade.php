@extends('layouts.app')

@section('title', 'Transaksi Penjualan Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-cart-plus"></i> Transaksi Penjualan Baru</h1>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('sales.store') }}" method="POST" id="saleForm">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Informasi Customer</h5>
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" 
                                id="customer_id" name="customer_id" required>
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sale_date" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('sale_date') is-invalid @enderror" 
                               id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Informasi Produk</h5>
                    <div class="mb-3">
                        <label for="product_select" class="form-label">Pilih Product</label>
                        <select class="form-select" id="product_select">
                            <option value="">-- Pilih Product --</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->price }}"
                                    data-stock="{{ $product->stock }}">
                                {{ $product->code }} - {{ $product->name }} (Stok: {{ $product->stock }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" value="1" min="1">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">
                        <i class="bi bi-plus-circle"></i> Tambah ke Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Detail Produk</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="productsBody">
                        <!-- Products will be added here dynamically -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th colspan="2">
                                <strong id="totalAmount">Rp 0</strong>
                                <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-warning btn-lg">
            <i class="bi bi-save"></i> Simpan Transaksi
        </button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-lg">
            <i class="bi bi-x-circle"></i> Batal
        </a>
    </div>
</form>

<!-- Hidden input untuk menyimpan data products -->
<input type="hidden" name="products_json" id="products_json">

@endsection

@push('scripts')
<script>
let cart = [];

function addProduct() {
    const productSelect = document.getElementById('product_select');
    const quantity = parseInt(document.getElementById('quantity').value);
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Silakan pilih product terlebih dahulu');
        return;
    }

    const productId = selectedOption.value;
    const productName = selectedOption.dataset.name;
    const productPrice = parseFloat(selectedOption.dataset.price);
    const productStock = parseInt(selectedOption.dataset.stock);

    // Cek apakah product sudah ada di cart
    const existingItem = cart.find(item => item.product_id == productId);
    if (existingItem) {
        if (existingItem.quantity + quantity > productStock) {
            alert('Quantity melebihi stok yang tersedia');
            return;
        }
        existingItem.quantity += quantity;
        existingItem.subtotal = existingItem.quantity * existingItem.price;
    } else {
        if (quantity > productStock) {
            alert('Quantity melebihi stok yang tersedia');
            return;
        }
        cart.push({
            product_id: productId,
            name: productName,
            price: productPrice,
            quantity: quantity,
            subtotal: quantity * productPrice
        });
    }

    renderCart();
    productSelect.value = '';
    document.getElementById('quantity').value = 1;
}

function removeProduct(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('productsBody');
    tbody.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        total += item.subtotal;
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>${item.quantity}</td>
                <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_amount_input').value = total;
}

// Submit form
document.getElementById('saleForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Silakan tambahkan product ke keranjang');
        return;
    }

    // Add cart data to form
    cart.forEach((item, index) => {
        const inputProductId = document.createElement('input');
        inputProductId.type = 'hidden';
        inputProductId.name = `products[${index}][product_id]`;
        inputProductId.value = item.product_id;
        this.appendChild(inputProductId);

        const inputQuantity = document.createElement('input');
        inputQuantity.type = 'hidden';
        inputQuantity.name = `products[${index}][quantity]`;
        inputQuantity.value = item.quantity;
        this.appendChild(inputQuantity);
    });
});
</script>
@endpush
@push('scripts')
<script>
let cart = [];

function addProduct() {
    const productSelect = document.getElementById('product_select');
    const quantity = parseInt(document.getElementById('quantity').value);
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Silakan pilih product terlebih dahulu');
        return;
    }

    const productId = selectedOption.value;
    const productName = selectedOption.dataset.name;
    const productPrice = parseFloat(selectedOption.dataset.price);
    const productStock = parseInt(selectedOption.dataset.stock);

    const existingItem = cart.find(item => item.product_id == productId);
    if (existingItem) {
        if (existingItem.quantity + quantity > productStock) {
            alert('Quantity melebihi stok yang tersedia');
            return;
        }
        existingItem.quantity += quantity;
        existingItem.subtotal = existingItem.quantity * existingItem.price;
    } else {
        if (quantity > productStock) {
            alert('Quantity melebihi stok yang tersedia');
            return;
        }
        cart.push({
            product_id: productId,
            name: productName,
            price: productPrice,
            quantity: quantity,
            subtotal: quantity * productPrice
        });
    }

    renderCart();
    productSelect.value = '';
    document.getElementById('quantity').value = 1;
}

function removeProduct(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('productsBody');
    tbody.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        total += item.subtotal;
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>${item.quantity}</td>
                <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_amount_input').value = total;
}

document.getElementById('saleForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Silakan tambahkan product ke keranjang');
        return;
    }

    cart.forEach((item, index) => {
        const inputProductId = document.createElement('input');
        inputProductId.type = 'hidden';
        inputProductId.name = `products[${index}][product_id]`;
        inputProductId.value = item.product_id;
        this.appendChild(inputProductId);

        const inputQuantity = document.createElement('input');
        inputQuantity.type = 'hidden';
        inputQuantity.name = `products[${index}][quantity]`;
        inputQuantity.value = item.quantity;
        this.appendChild(inputQuantity);
    });
});
</script>
@endpush