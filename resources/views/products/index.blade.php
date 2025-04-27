@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Produk</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus d-sm-none"></i>
                <span class="d-none d-sm-inline">Tambah Produk</span>
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="d-none d-md-table-cell">Kategori</th>
                                <th>Stok</th>
                                <th class="d-none d-lg-table-cell">Harga Beli</th>
                                <th class="d-none d-sm-table-cell">Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        {{ $product->name }}
                                        <div class="d-md-none small text-muted">
                                            {{ $product->category->name }}
                                        </div>
                                        <div class="d-sm-none small text-muted">
                                            Jual: Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $product->category->name }}</td>
                                    <td>
                                        <span class="badge {{ $product->stock <= 5 ? 'bg-danger' : 'bg-success' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">Rp
                                        {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                    <td class="d-none d-sm-table-cell">Rp
                                        {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('products.edit', $product) }}">Edit</a></li>
                                                <li>
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item text-danger">Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
