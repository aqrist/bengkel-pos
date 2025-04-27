@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Kategori</h1>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus d-sm-none"></i>
                <span class="d-none d-sm-inline">Tambah Kategori</span>
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
                                <th>No</th>
                                <th>Nama</th>
                                <th class="d-none d-md-table-cell">Deskripsi</th>
                                <th>Jml Produk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                                    </td>
                                    <td>
                                        {{ $category->name }}
                                        <div class="d-md-none small text-muted">
                                            {{ Str::limit($category->description, 30) }}
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $category->description }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $category->products->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('categories.edit', $category) }}">Edit</a></li>
                                                @if ($category->products->count() == 0)
                                                    <li>
                                                        <form action="{{ route('categories.destroy', $category) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item text-danger">Hapus</button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button class="dropdown-item text-muted" disabled>
                                                            Hapus (Tidak bisa dihapus)
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <p>Belum ada kategori</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($categories->hasPages())
                <div class="card-footer">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
