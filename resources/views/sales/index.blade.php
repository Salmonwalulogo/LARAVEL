@extends('layouts.app')

@section('title', 'Sales')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-receipt"></i> Sales</h1>
    <a href="{{ route('sales.create') }}" class="btn btn-warning">
        <i class="bi bi-plus-circle"></i> Transaksi Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Sale Date</th>
                        <th>Total Amount</th>
                        <th>Items</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>#INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        <td><strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></td>
                        <td>{{ $sale->saleItems->count() }} items</td>
                        <td>
                            <a href="{{ route('sales.show', $sale->id) }}" 
                               class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <form action="{{ route('sales.destroy', $sale->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <i class="bi bi-inbox"></i> Belum ada transaksi penjualan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection