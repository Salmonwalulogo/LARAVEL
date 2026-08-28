@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-receipt"></i> Detail Transaksi #INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</h1>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">Informasi Customer</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nama</th>
                        <td>{{ $sale->customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td>{{ $sale->customer->phone }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $sale->customer->address ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">Informasi Transaksi</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">No. Invoice</th>
                        <td><strong>#INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Total Amount</th>
                        <td><strong class="text-success">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3">Detail Produk</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->saleItems as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product->code }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total:</th>
                        <th><strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')">
            <i class="bi bi-trash"></i> Hapus Transaksi
        </button>
    </form>
</div>
@endsection