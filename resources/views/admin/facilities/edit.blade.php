@extends('admin.layout')

@section('page_title', 'Edit Fasilitas')

@section('content')
<div class="card fac-card-create">
    <div class="card-header fac-form-header">
        <h2 class="card-title fac-form-title">Edit Fasilitas</h2>
        <a href="{{ route('admin.facilities.index') }}" class="btn-back-fac">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="fac-error-box">
            <ul class="fac-error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.facilities.update', $facility->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group mb-3">
            <label class="form-label fac-label-bold">Judul Fasilitas</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $facility->title ?? '') }}" required placeholder="Contoh: Hotel Bintang 5">
        </div>

        <div class="form-group mb-3">
            <label class="form-label fac-label-bold">Emoji / Icon</label>
            <input type="text" name="icon" class="form-control" value="{{ old('icon', $facility->icon ?? '🏨') }}" required placeholder="Contoh: 🏨">
            <small class="fac-help-text">Gunakan 1-2 karakter emoji untuk merepresentasikan fasilitas ini.</small>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fac-label-bold">URL Gambar Cover (Opsional)</label>
            <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $facility->image_url ?? '') }}" placeholder="https://unsplash.com/...">
            <small class="fac-help-text">Masukkan link gambar dari internet, atau biarkan kosong untuk pakai gambar default.</small>
        </div>

        <div class="form-group mb-4">
            <label class="form-label fac-label-bold">Deskripsi Singkat</label>
            <textarea name="description" class="form-control" rows="4" required placeholder="Jelaskan fasilitas yang didapat jamaah...">{{ old('description', $facility->description ?? '') }}</textarea>
        </div>

        <div class="fac-form-actions">
            <a href="{{ route('admin.facilities.index') }}" class="btn-primary btn-cancel-create">Batal</a>
            <button type="submit" class="btn-primary btn-submit-create">Update Fasilitas</button>
        </div>
    </form>
</div>
@endsection
