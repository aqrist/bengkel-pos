@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">{{ isset($category) ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>

        <div class="card">
            <div class="card-body">
                <form action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($category))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $category->name ?? '') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ isset($category) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
