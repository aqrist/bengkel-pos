@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Tambah Kategori</h1>

        <div class="card mt-4">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
