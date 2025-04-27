@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h1>Produk</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
