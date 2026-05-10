@extends('admin.layout')

@section('page_title', 'Kelola Akun')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/profile.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <!-- PROFILE HEADER -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <div class="greeting-tag">Manajemen Akses</div>
            <h2>Pengaturan <em>Akun Admin</em></h2>
            <p>Kelola kredensial akses Anda dan pastikan keamanan akun administrator tetap terjaga.</p>
        </div>
        <div class="welcome-actions">
            <div class="avatar profile-avatar-large">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </div>

    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="profile-grid">
            <!-- INFORMASI DASAR -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="v-align-mid mr-2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Informasi Dasar
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label class="form-label">Email Saat Ini</label>
                        <div class="form-input-icon-wrapper">
                            <input type="email" value="{{ Auth::user()->email ?? '' }}" class="form-control form-input-with-icon input-disabled" disabled>
                            <svg class="input-icon-left" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Email Baru</label>
                        <div class="form-input-icon-wrapper">
                            <input type="email" name="admin_email" class="form-control form-input-with-icon" value="{{ Auth::user()->email ?? old('admin_email') }}" placeholder="Masukkan email baru..." required>
                            <svg class="input-icon-left active" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        @error('admin_email')
                            <span class="text-error-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Username</label>
                        <div class="form-input-icon-wrapper">
                            <input type="text" name="admin_username" class="form-control form-input-with-icon" value="{{ Auth::user()->username ?? old('admin_username') }}" placeholder="Masukkan username baru..." required>
                            <svg class="input-icon-left active" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        @error('admin_username')
                            <span class="text-error-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-action-footer">
                        <button type="submit" class="btn-primary">Perbarui Profil</button>
                    </div>
                </div>
            </div>

            <!-- KEAMANAN AKUN -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="v-align-mid mr-2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        Keamanan Akun
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="form-input-icon-wrapper flex-align-center">
                            <span class="input-icon-left z-5">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4.5 8-11.8A8 8 0 0012 2a8 8 0 00-8 8.2c0 7.3 8 11.8 8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <input type="password" name="current_password" id="current_password" placeholder="Masukkan password lama..." class="form-control form-input-with-icon pr-45" required>
                            <button type="button" onclick="togglePassword('current_password', this)" class="password-toggle-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('current_password')
                            <span class="text-error-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Ganti Password Baru</label>
                        <div class="form-input-icon-wrapper flex-align-center">
                            <span class="input-icon-left z-5">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </span>
                            <input type="password" name="admin_password" id="admin_password" placeholder="Masukkan password baru..." class="form-control form-input-with-icon pr-45">
                            <button type="button" onclick="togglePassword('admin_password', this)" class="password-toggle-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <span class="password-help-text">
                            Kosongkan jika Anda tidak ingin mengubah password saat ini. Gunakan kombinasi huruf dan angka untuk keamanan maksimal.
                        </span>
                        @error('admin_password')
                            <span class="text-error-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-action-footer">
                        <button type="submit" class="btn-primary btn-gold">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

    <!-- INFO BOX -->
    <div class="info-bar">
        <div class="info-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        </div>
    </div>

    <!-- RIWAYAT AKTIVITAS -->
    <div class="card history-card">
        <div class="card-header">
            <h3 class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="v-align-mid mr-2"><path d="M12 22s8-4.5 8-11.8A8 8 0 0012 2a8 8 0 00-8 8.2c0 7.3 8 11.8 8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                Riwayat Aktivitas Login
            </h3>
            <span class="text-muted history-header-muted">5 Sesi Terakhir</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>Perangkat</th>
                            <th>Akun</th>
                            <th>Alamat IP</th>
                            <th>Waktu Login</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginHistory as $log)
                            @php
                                $isCurrent = ($log['ip'] === request()->ip() && $log['user_agent'] === request()->userAgent());
                                $isMobile  = preg_match('/Mobile|Android|iPhone/i', $log['user_agent']);
                            @endphp
                             <tr>
                                <td>
                                    <div class="flex-align-center gap-12">
                                        <div class="device-icon-box {{ $isCurrent ? 'current' : 'other' }}">
                                            @if($isMobile)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                            @endif
                                        </div>
                                         <div>
                                            <div class="device-info-name">
                                                {{ $isMobile ? 'Perangkat Mobile' : 'Desktop / Laptop' }}
                                                @if($isCurrent)
                                                    <span class="session-badge">Sesi Ini</span>
                                                @endif
                                            </div>
                                            <div class="user-agent-text">
                                                {{ $log['user_agent'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="account-identity">
                                    @php
                                        $displayUser = $log['username'] ?? explode('@', $log['email'] ?? 'admin@travel.com')[0];
                                    @endphp
                                    <div class="identity-main">{{ $displayUser }}</div>
                                    <div class="identity-sub">{{ $log['email'] ?? '-' }}</div>
                                </td>
                                 <td class="ip-address">
                                    {{ $log['ip'] }}
                                </td>
                                <td>
                                    <div class="timestamp-main">{{ \Carbon\Carbon::parse($log['timestamp'])->diffForHumans() }}</div>
                                    <div class="timestamp-sub">{{ $log['timestamp'] }}</div>
                                </td>
                                <td>
                                    <span class="status-badge-success">Berhasil</span>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="5" class="empty-history">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <div class="empty-history-text">Belum ada riwayat aktivitas.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('svg');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
</script>
@endpush

@endsection

