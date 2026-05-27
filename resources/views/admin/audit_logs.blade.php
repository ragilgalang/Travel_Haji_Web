@extends('admin.layout')

@section('page_title', 'Monitoring Audit Akun')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/audit_logs.css') }}">
@endpush

@section('content')
<div class="audit-container">
    <div class="audit-header">
        <div>
            <h2>🛡️ Audit Keamanan Akun</h2>
            <p>Pantau siapa saja yang masuk ke sistem, perangkat yang digunakan, dan password yang diinputkan.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="toggleBannedSection()" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 10px 16px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                Akun Terbanned ({{ count($bannedAccounts) }})
            </button>
            <form action="{{ route('admin.audit.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh histori monitoring ini? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                <button type="submit" class="btn-clear-logs">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6"/></svg>
                    Bersihkan Histori
                </button>
            </form>
        </div>
    </div>
    @php
        $statsSuccess = count(collect($auditLogs)->where('status', 'LOGIN_SUCCESS'));
        $statsFailed = count(collect($auditLogs)->where('status', 'LOGIN_FAILED'));
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 2rem;">
        <div class="card-modern" style="padding: 1rem; display: flex; align-items: center; gap: 15px; background: linear-gradient(135deg, #f0fdf4, #ffffff);">
            <div style="background: #dcfce7; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">✅</div>
            <div>
                <div style="font-size: 0.75rem; color: #166534; font-weight: 700; text-transform: uppercase;">Berhasil</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #064e3b;">{{ $statsSuccess }}</div>
            </div>
        </div>
        <div class="card-modern" style="padding: 1rem; display: flex; align-items: center; gap: 15px; background: linear-gradient(135deg, #fef2f2, #ffffff);">
            <div style="background: #fee2e2; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">❌</div>
            <div>
                <div style="font-size: 0.75rem; color: #991b1b; font-weight: 700; text-transform: uppercase;">Gagal</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #7f1d1d;">{{ $statsFailed }}</div>
            </div>
        </div>
    </div>

    {{-- GRAFIK REAL-TIME LOGIN HARI INI --}}
    <!-- ========================================== -->
    <!-- [TANDA: GRAFIK AUDIT KEAMANAN - CANVAS HTML] -->
    <!-- ========================================== -->
    <div class="card-modern" style="margin-bottom: 2rem; padding: 1.5rem;">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-family: 'Playfair Display', serif;">
            📈 Tren Aktivitas Login (Hari Ini)
            <span style="background: #10b981; color: white; font-size: 0.65rem; padding: 2px 8px; border-radius: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; animation: pulse 2s infinite;">Live</span>
        </h3>
        <div style="height: 300px;">
            <canvas id="auditChart"></canvas>
        </div>
    </div>

    <div class="card-modern">
        <div class="table-responsive">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Akun / Identity</th>
                        <th>Password Input</th>
                        <th>IP & Perangkat</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Kelola Keamanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">{{ date('d M Y', strtotime($log['timestamp'])) }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ date('H:i:s', strtotime($log['timestamp'])) }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1a5c3a;">{{ $log['account'] ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">{{ $log['email'] ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="pwd-box" style="display: flex; align-items: center; gap: 8px;">
                                <code
                                    id="pwd-{{ $loop->index }}"
                                    data-pwd="{{ $log['password'] ?? '' }}"
                                    style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-size: 0.85rem; letter-spacing: 2px; color: #475569; min-width: 80px; display: inline-block;"
                                >••••••••</code>
                                <button type="button"
                                    onclick="togglePwd(this, 'pwd-{{ $loop->index }}')"
                                    title="Tampilkan/Sembunyikan Password"
                                    style="background: #eff6ff; border: 1px solid #bfdbfe; color: #3b82f6; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; transition: 0.2s; flex-shrink: 0;"
                                    onmouseover="this.style.background='#dbeafe'"
                                    onmouseout="this.style.background='#eff6ff'"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 600;">{{ $log['ip'] ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: #1e293b;">{{ $log['device'] ?? 'Unknown' }}</div>
                        </td>
                        <td style="text-align: center;">
                            @php
                                $status = $log['status'] ?? '';
                            @endphp
                            @if($status == 'LOGIN_SUCCESS')
                                <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid #bbf7d0;">BERHASIL</span>
                            @elseif($status == 'ACCOUNT_DELETED_TOO_MANY_ATTEMPTS')
                                <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; box-shadow: 0 0 10px rgba(239, 68, 68, 0.15); letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">🗑️ TERHAPUS (3x GAGAL)</span>
                            @elseif($status == 'LOGIN_BLOCKED_SPECIAL_RULE')
                                <span style="background: #fff7ed; color: #c2410c; border: 1px solid #fdba74; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">🔒 TERBLOKIR (2x GAGAL)</span>
                            @elseif($status == 'LOGIN_BANNED_PERMANENT')
                                <span style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">🚫 BANNED</span>
                            @elseif($status == 'LOGIN_LOCKED_2_MINUTES')
                                <span style="background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">⏳ TERKUNCI (2 MENIT)</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">GAGAL</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <form action="{{ route('admin.audit.unlock') }}" method="POST" onsubmit="return confirm('Buka blokir untuk akun {{ $log['email'] ?? '' }}?')">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $log['email'] ?? '' }}">
                                    <button type="submit" title="Buka Blokir / Reset Pinalti" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">🔓</button>
                                </form>
                                <button type="button" onclick="promptResetPassword('{{ $log['email'] ?? '' }}')" title="Ganti Password" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">🔑</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8;">Belum ada data audit log yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- BANNED ACCOUNTS SECTION -->
    <div id="banned-section" class="card-modern" style="display: none; margin-top: 2rem; border: 2px solid #fee2e2; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.1);">
        <div style="padding: 1.5rem; background: #fef2f2; border-bottom: 1px solid #fee2e2; border-radius: 20px 20px 0 0;">
            <h3 style="margin: 0; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                🚫 Daftar Akun Banned Permanen
            </h3>
            <p style="margin: 5px 0 0; font-size: 0.85rem; color: #7f1d1d;">Akun di bawah ini tidak dapat login karena telah salah memasukkan password lebih dari 10 kali.</p>
        </div>
        <div class="table-responsive">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Email Akun</th>
                        <th>Terakhir Gagal</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bannedAccounts as $banned)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1a5c3a;">{{ $banned->name ?? '-' }}</div>
                            <div style="font-size: 0.85rem; color: #64748b;">{{ $banned->email }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 600;">{{ $banned->banned_reason ?? 'Salah password 0 kali' }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ $banned->updated_at ? date('d M Y H:i', strtotime($banned->updated_at)) : '-' }}</div>
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.audit.unlock') }}" method="POST" onsubmit="return confirm('Buka blokir permanen untuk akun {{ $banned->email }}?')">
                                @csrf
                                <input type="hidden" name="email" value="{{ $banned->email }}">
                                <button type="submit" style="background: #f0fdf4; border: 1px solid #16a34a; color: #166534; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    🔓 Buka Blokir
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 30px; text-align: center; color: #94a3b8;">Tidak ada akun yang terkena banned permanen saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleBannedSection() {
    const section = document.getElementById('banned-section');
    if (section.style.display === 'none') {
        section.style.display = 'block';
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        section.style.display = 'none';
    }
}

function togglePwd(btn, id) {
    const el = document.getElementById(id);
    const real = el.getAttribute('data-pwd');
    const isHidden = el.textContent === '••••••••';
    if (isHidden) {
        el.textContent = real || '(kosong)';
        el.style.color = '#1a5c3a';
        el.style.background = '#f0fdf4';
        btn.style.background = '#dcfce7';
        btn.style.borderColor = '#86efac';
        btn.style.color = '#166534';
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
    } else {
        el.textContent = '••••••••';
        el.style.color = '#475569';
        el.style.background = '#f1f5f9';
        btn.style.background = '#eff6ff';
        btn.style.borderColor = '#bfdbfe';
        btn.style.color = '#3b82f6';
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }
}

function promptResetPassword(email) {
    if (!email) return;
    document.getElementById('modal-target-email').innerText = email;
    document.getElementById('reset-email').value = email;
    document.getElementById('password-modal').style.display = 'flex';
    setTimeout(() => {
        document.getElementById('password-modal').classList.add('active');
        document.getElementById('new_password_input').focus();
    }, 10);
}

function closePasswordModal() {
    document.getElementById('password-modal').classList.remove('active');
    setTimeout(() => {
        document.getElementById('password-modal').style.display = 'none';
        document.getElementById('new_password_input').value = '';
    }, 300);
}

function submitPasswordChange() {
    const pwd = document.getElementById('new_password_input').value;
    if (pwd.length < 8) {
        alert("⚠️ Keamanan Lemah! Password minimal harus 8 karakter.");
        return;
    }
    document.getElementById('reset-password').value = pwd;
    document.getElementById('reset-form').submit();
}

function toggleModalPwd() {
    const pwd = document.getElementById('new_password_input');
    const icon = document.getElementById('modalEyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        pwd.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}
</script>

<!-- MODAL GANTI PASSWORD PREMIUM -->
<div id="password-modal" style="display: none;">
    <div class="modal-content">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="background: #eff6ff; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <span style="font-size: 24px;">🔑</span>
            </div>
            <h3 style="font-family: 'Playfair Display', serif; color: #1e293b; font-size: 1.4rem; margin-bottom: 5px;">Ganti Password</h3>
            <p style="color: #64748b; font-size: 0.9rem;">Akun: <strong id="modal-target-email" style="color: #1a5c3a;"></strong></p>
        </div>
        
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Password Baru (Min. 8 Karakter)</label>
            <div style="position: relative;">
                <input type="password" id="new_password_input" placeholder="••••••••" style="width: 100%; padding: 12px 45px 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: 0.2s; outline: none;" onfocus="this.style.borderColor='#3b82f6'">
                <button type="button" onclick="toggleModalPwd()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8;">
                    <svg id="modalEyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button onclick="closePasswordModal()" style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; font-weight: 600; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f1f5f9'">Batal</button>
            <button onclick="submitPasswordChange()" style="flex: 2; padding: 12px; border-radius: 12px; border: none; background: linear-gradient(135deg, #1a5c3a, #2db366); color: white; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(26, 92, 58, 0.2); transition: 0.2s;" onmouseover="this.style.transform='translateY(-2px)'">Simpan Password</button>
        </div>
    </div>
</div>

<form id="reset-form" action="{{ route('admin.audit.resetPassword') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="email" id="reset-email">
    <input type="hidden" name="new_password" id="reset-password">
</form>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('auditChart');
    if (!ctx) return;

    // ==========================================
    // [TANDA: GRAFIK AUDIT KEAMANAN - SKRIP CHART.JS]
    // ==========================================
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Login Berhasil',
                    data: chartData.success,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981'
                },
                {
                    label: 'Login Gagal',
                    data: chartData.failed,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444',
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { family: 'Inter', size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { size: 13, weight: '700' },
                    bodyFont: { size: 12 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { stepSize: 1, precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
