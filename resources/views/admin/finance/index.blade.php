@extends('admin.layout')

@section('page_title', 'Laporan Pendapatan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/finance/index.css') }}">
@endpush

@section('content')
<!-- FILTER BAR -->
<div class="filter-bar no-print" style="background: white; padding: 1.5rem; border-radius: 1.25rem; border: 2px solid #10b981; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <form method="GET" style="display: flex; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap;">
        <!-- Filter Kategori -->
        <div style="flex: 1; min-width: 250px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #065f46; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">Kategori Paket:</label>
            <select name="type" onchange="this.form.submit()" style="width: 100%; height: 48px; padding: 0 1rem; border-radius: 0.75rem; border: 1px solid #d1d5db; background-color: #f9fafb; font-weight: 600; color: #111827; cursor: pointer; outline: none;">
                <option value="">— Semua Kategori —</option>
                <option value="haji" {{ request('type') == 'haji' ? 'selected' : '' }}>🕋 Khusus Haji</option>
                <option value="umrah" {{ request('type') == 'umrah' ? 'selected' : '' }}>🕌 Khusus Umrah</option>
            </select>
        </div>

        <!-- Filter Waktu -->
        <div style="flex: 1; min-width: 250px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #065f46; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">Periode Waktu:</label>
            <select name="time" onchange="this.form.submit()" style="width: 100%; height: 48px; padding: 0 1rem; border-radius: 0.75rem; border: 1px solid #d1d5db; background-color: #f9fafb; font-weight: 600; color: #111827; cursor: pointer; outline: none;">
                <option value="">— Semua Waktu —</option>
                <option value="7days" {{ request('time') == '7days' ? 'selected' : '' }}>📅 7 Hari Terakhir</option>
                <option value="1month" {{ request('time') == '1month' ? 'selected' : '' }}>📅 1 Bulan Terakhir</option>
                <option value="3months" {{ request('time') == '3months' ? 'selected' : '' }}>📅 3 Bulan Terakhir</option>
                <option value="6months" {{ request('time') == '6months' ? 'selected' : '' }}>📅 6 Bulan Terakhir</option>
                <option value="1year" {{ request('time') == '1year' ? 'selected' : '' }}>📅 1 Tahun Terakhir</option>
            </select>
        </div>
        
        @if(request('type') || request('time'))
        <div style="padding-bottom: 0.75rem;">
            <a href="{{ route('admin.finance.index') }}" style="color: #ef4444; font-size: 0.875rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                <span>✕</span> Reset Filter
            </a>
        </div>
        @endif
    </form>
</div>

<div class="finance-stats no-print">
    <!-- Total Income -->
    <div class="f-card">
        <div class="f-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="f-label">Estimasi Total Pendapatan</div>
        <div class="f-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        <div class="f-sub">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="18 15 12 9 6 15"/></svg>
            Dari pendaftaran yang dikonfirmasi
        </div>
    </div>

    <!-- Umrah Income -->
    <div class="f-card">
        <div class="f-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 7v14M21 7v14M2 3h20M10 7v14M14 7v14"/></svg>
        </div>
        <div class="f-label">Pendapatan Umrah</div>
        <div class="f-value">Rp {{ number_format($incomeByCategory['umrah'], 0, ',', '.') }}</div>
        <div class="f-sub">Target tercapai 85%</div>
    </div>

    <!-- Haji Income -->
    <div class="f-card">
        <div class="f-icon bg-gold">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="f-label">Pendapatan Haji</div>
        <div class="f-value text-gold">Rp {{ number_format($incomeByCategory['haji'], 0, ',', '.') }}</div>
        <div class="f-sub text-gold">Pendapatan Musiman</div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h2 class="card-title">Riwayat Transaksi Masuk</h2>
        <div class="export-group no-print">
            <button onclick="exportToPDF()" class="btn-export btn-pdf">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                PDF
            </button>
            <button onclick="exportToExcel()" class="btn-export btn-excel">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Excel
            </button>
            <button onclick="exportToWord()" class="btn-export btn-word">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Word
            </button>
        </div>
    </div>
    
    <div class="scrollable-history">
        <table class="f-table">
            <thead style="position: sticky; top: 0; z-index: 5; background: #f9fafb;">
                <tr>
                    <th>TANGGAL</th>
                    <th>REF</th>
                    <th>JEMAAH</th>
                    <th>PAKET</th>
                    <th>STATUS</th>
                    <th>NOMINAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($confirmedRegs as $reg)
                @php
                    $package = $packages->firstWhere('name', $reg['paket'] ?? '');
                    $priceStr = $package['price'] ?? '0';
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($reg['created_at'] ?? now())->format('d M Y') }}</td>
                    <td style="font-family: 'Courier New', Courier, monospace; font-weight: 700; color: #64748b; font-size: 0.75rem;">
                        #{{ strtoupper(substr($reg['id'] ?? '---', 0, 8)) }}
                    </td>
                    <td style="font-weight: 600;">{{ $reg['nama_lengkap'] ?? 'N/A' }}</td>
                    <td>{{ $reg['paket'] ?? 'N/A' }}</td>
                    <td><span class="status-pill">{{ $reg['status'] ?? 'Confirmed' }}</span></td>
                    <td style="font-weight: 700; color: #10b981;">Rp {{ $priceStr }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #6b7280;">
                        Belum ada data pendaftaran yang dikonfirmasi. Pendapatan akan muncul di sini setelah status pendaftaran diubah menjadi "Confirmed".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportToPDF() {
        window.print();
    }

    function exportToExcel() {
        let table = document.querySelector(".f-table").cloneNode(true);
        
        // Remove sticky style and set borders for excel
        table.style.border = "1px solid #000";
        table.querySelectorAll("th, td").forEach(cell => {
            cell.style.border = "1px solid #000";
            cell.style.padding = "5px";
        });

        @php
            $xmlPrefix = '<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
        @endphp
        let header = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="UTF-8">
                <!--[if gte mso 9]>{!! $xmlPrefix !!}<![endif]-->
                <style>
                    .status-pill { background: #f0fdf4; color: #16a34a; padding: 2px 8px; border-radius: 10px; font-weight: bold; }
                </style>
            </head>
            <body>
                <h2 style="text-align:center;">Laporan Pendapatan - {{ $settings['site_name'] ?? 'PT UMB' }}</h2>
                <p>Tanggal Cetak: {{ date('d M Y H:i') }}</p>
                ${table.outerHTML}
            </body>
            </html>`;

        let blob = new Blob([header], { type: "application/vnd.ms-excel" });
        let url = URL.createObjectURL(blob);
        let link = document.createElement("a");
        link.href = url;
        link.download = "Laporan_Keuangan_{{ date('Y-m-d') }}.xls";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToWord() {
        let table = document.querySelector(".f-table").outerHTML;
        let header = "<h2 style='text-align:center;'>Laporan Keuangan - {{ $settings['site_name'] ?? 'PT UMB' }}</h2>";
        let content = header + table;
        
        let blob = new Blob(['\ufeff', content], {
            type: 'application/msword'
        });
        
        let url = URL.createObjectURL(blob);
        let link = document.createElement("a");
        link.href = url;
        link.download = "Laporan_Keuangan_{{ date('Y-m-d') }}.doc";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endpush
