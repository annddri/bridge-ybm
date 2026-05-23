<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spiritual Tracker - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --bg-light: #f8fafc;
            --navy-theme: #063255;
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Navigasi Sidebar Terintegrasi Premium */
        .sidebar { 
            width: 280px; height: 100vh; position: fixed; top: 0; left: 0; 
            background: linear-gradient(180deg, #063255 0%, #041f35 100%); 
            color: var(--sidebar-text); padding-top: 10px; z-index: 1000; overflow-y: auto; 
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-brand { text-align: center; padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .brand-logo { width: 75px; height: 75px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.2); margin-bottom: 12px; object-fit: cover; }
        
        .nav-link { color: var(--sidebar-text); padding: 11px 25px; display: flex; align-items: center; transition: all 0.2s ease; font-size: 0.92rem; text-decoration: none; border-left: 4px solid transparent; }
        .nav-link i { width: 24px; margin-right: 12px; font-size: 1.05rem; opacity: 0.8; }
        .nav-link:hover { color: #fff; background-color: var(--sidebar-hover); padding-left: 28px; }
        .nav-link.active { color: #fff; background-color: rgba(13, 110, 253, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }

        /* Layout Konten Utama */
        .main-content { margin-left: 280px; padding: 35px 30px; transition: all 0.3s ease; }
        .card-custom { border: 1px solid rgba(6, 50, 85, 0.08); border-radius: 20px; background: #ffffff; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important; padding: 30px; }
        
        .header-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            border: 1px solid #e2e8f0 !important;
        }

        .select-bulan { min-width: 140px; }
        .select-tahun { min-width: 95px; }

        .badge-progress-total {
            background-color: var(--navy-theme) !important;
            color: #ffffff !important;
            font-weight: 600;
        }
        
        /* Modifikasi Kontainer Tabel Spreadsheet-Style */
        .table-container {
            max-height: 72vh;
            overflow: auto;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.04);
            background: white;
        }

        .table-amalan { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-amalan thead th {
            position: sticky;
            top: 0;
            background: var(--navy-theme) !important;
            color: white;
            z-index: 30;
            padding: 14px 8px;
            border: 1px solid #041f35;
            font-weight: 600;
        }

        /* Kolom Sticky Aktivitas & Target */
        .sticky-col-1 {
            position: sticky;
            left: 0;
            background-color: white !important;
            z-index: 20;
            min-width: 240px;
            border-right: 1px solid #eef2f5 !important;
            box-shadow: 3px 0 6px rgba(0,0,0,0.02);
            padding-left: 15px !important;
        }

        .sticky-col-2 {
            position: sticky;
            left: 240px;
            background-color: #f8fafc !important;
            z-index: 19;
            min-width: 90px;
            border-right: 2px solid #e2e8f0 !important;
            text-align: center;
        }

        thead th.sticky-col-1 { z-index: 40; left: 0; }
        thead th.sticky-col-2 { z-index: 39; left: 240px; }

        .form-check-input { width: 1.2rem; height: 1.2rem; cursor: pointer; transition: all 0.2s; }
        .form-check-input:checked { background-color: var(--accent-color); border-color: var(--accent-color); }
        
        /* Desain Aesthetic Ceklis Shalat Horizontal */
        .shalat-badge-container { cursor: pointer; user-select: none; margin: 0 1px; }
        .shalat-char-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 16px; height: 16px; font-size: 0.58rem; font-weight: 700; border-radius: 4px;
            background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; transition: all 0.15s ease;
        }
        .shalat-badge-container input:checked + .shalat-char-badge {
            background-color: var(--accent-color); color: #ffffff; border-color: var(--accent-color);
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.35);
        }

        .percent-badge { background: #e2f0d9; color: #385723; padding: 6px 12px; border-radius: 20px; font-weight: bold; }
        .alert-saved { position: fixed; bottom: 25px; right: 25px; display: none; z-index: 9999; border-radius: 10px; }

        @media (max-width: 991.98px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar-brand { display: flex; align-items: center; text-align: left; padding: 15px; }
            .brand-logo { width: 45px; height: 45px; margin-bottom: 0; margin-right: 15px; }
            .logout-link { border-top: none; margin-top: 0; padding-top: 11px !important; }
        }
    </style>
</head>
<body style="--accent-color: {{ $accent_color }};">

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6">{{ $u->name }}</h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;">{{ $u->role }}</small>
        </div>
    </div>

    <div class="mt-3">
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profil Saya</a>
        
        {{-- <?php if ($role_user != 'awardee'): ?>
        <a href="data_awardee.php" class="nav-link"><i class="fas fa-users"></i> Data Awardee</a>
        <?php endif; ?> --}}

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">Fitur Monitoring</div>
        <a href="amalan.php" class="nav-link active"><i class="fas fa-pray"></i> Spiritual Tracker</a>
        <a href="tahfidz.php" class="nav-link"><i class="fas fa-book-quran"></i> Tahfidz Tracker</a>
        <a href="akademik.php" class="nav-link"><i class="fas fa-graduation-cap"></i> Akademik</a>
        <a href="keaktifan.php" class="nav-link"><i class="fas fa-award"></i> Portofolio</a>
        <a href="masyarakat.php" class="nav-link"><i class="fas fa-people-group"></i> Sosial Masyarakat</a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">Fitur Asrama</div>
        <a href="inventaris.php" class="nav-link"><i class="fas fa-boxes-stacked"></i> Inventaris Asrama</a>
        <a href="keuangan.php" class="nav-link"><i class="fas fa-wallet"></i> Keuangan Asrama</a>
        <a href="perizinan.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Perizinan Asrama</a>
        
        <a href="logout.php" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        
            <div class="card header-card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center p-4">
                    <div>
                        <h4 class="fw-bold m-0 text-uppercase" style="color: var(--navy-theme); letter-spacing: 0.5px;"><i class="fas fa-pray me-2 text-primary"></i>Spiritual Tracker</h4>
                        <p class="text-muted small m-0 mt-1">Input Amalan Yaumiyah Bulanan Berbasis Spreadsheet</p>
                        
                        <div class="mt-2">
                            <span class="badge badge-progress-total p-2 rounded-3 fs-6 shadow-sm">
                                <i class="fas fa-chart-line me-1 text-info"></i> Total Progress Bulan Ini: 
                                <span id="grand-total-pct">0.0%</span>
                            </span>
                        </div>
                    </div>
                    <form class="d-flex gap-2 mt-3 mt-md-0">
                        <select name="bulan" class="form-select form-select-sm select-bulan shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                        <select name="tahun" class="form-select form-select-sm select-tahun shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                            @for ($y = $tahun - 2; $y <= $tahun + 2; $y++)
                                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        <a href="/dashboard" class="btn btn-dark btn-sm px-4 rounded-pill shadow-sm fw-semibold">Kembali</a>
                    </form>
                </div>
            </div>

            <div class="table-container border">
                <table class="table table-amalan align-middle m-0">
                    <thead>
                        <tr>
                            <th class="sticky-col-1">Aktivitas</th>
                            <th class="sticky-col-2">Target</th>
                            <?php for($d=1; $d<=$jumlah_hari; $d++) echo "<th class='text-center'>$d</th>"; ?>
                            <th class="text-center">Total %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list_amalan as $key => $attr)
                            <tr>
                                <td class="sticky-col-1 fw-bold text-dark">{{ $attr['nama'] }}</td>

                                <td class="sticky-col-2 fw-bold text-primary">
                                    {{ $attr['target'] }}
                                    <span style="font-size: 0.6rem; color: #999;">{{ $attr['unit'] }}</span>
                                </td>

                                @php
                                    $total_input = 0;
                                    $hari_aktif = 0;
                                @endphp

                                @for ($d = 1; $d <= $jumlah_hari; $d++)
                                    @php
                                        $val = $data_db[$key][$d] ?? '';
                                        if ($val !== '') {
                                            $total_input += (int) $val;
                                            $hari_aktif++;
                                        }
                                        $tgl_full = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                                    @endphp

                                    <td class="p-1 text-center border-end border-light">
                                        @if ($key === 'shalat_5_waktu')
                                            <div class="d-flex justify-content-center align-items-center gap-1 px-1" style="min-width: 95px;">
                                                @php
                                                    $sh_label = ['S','D','A','M','I'];
                                                    $sh_full_name = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
                                                @endphp

                                                @for ($s = 0; $s < 5; $s++)
                                                    <label class="shalat-badge-container" title="{{ $sh_full_name[$s] }}">
                                                        <input type="checkbox"
                                                            class="shalat-mini-check d-none"
                                                            data-tgl="{{ $tgl_full }}"
                                                            data-kolom="{{ $key }}"
                                                            data-tipe="{{ $attr['tipe'] }}"
                                                            data-target="{{ $attr['target'] }}"
                                                            {{ $val !== '' && $val > $s ? 'checked' : '' }}>
                                                        <span class="shalat-char-badge">{{ $sh_label[$s] }}</span>
                                                    </label>
                                                @endfor
                                            </div>
                                        @else
                                            <div class="d-flex justify-content-center">
                                                <input type="checkbox"
                                                    class="form-check-input amalan-check"
                                                    data-tgl="{{ $tgl_full }}"
                                                    data-kolom="{{ $key }}"
                                                    data-tipe="{{ $attr['tipe'] }}"
                                                    data-target="{{ $attr['target'] }}"
                                                    value="1"
                                                    {{ $val == 1 ? 'checked' : '' }}>
                                            </div>
                                        @endif
                                    </td>
                                @endfor

                                <td class="text-center fw-bold bg-light" style="min-width: 80px;">
                                    <span class="percent-badge" id="pct-{{ $key }}">
                                        @php
                                            if ($hari_aktif > 0) {
                                                $p = ($attr['tipe'] == 'harian')
                                                    ? ($total_input / $hari_aktif) / $attr['target'] * 100
                                                    : ($total_input / $attr['target']) * 100;

                                                echo round(min($p, 100), 1) . '%';
                                            } else {
                                                echo '0%';
                                            }
                                        @endphp
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>


        <div id="notif" class="alert alert-success alert-saved shadow-lg py-2 px-4 border-0 text-white bg-success">
            <i class="fas fa-check-circle me-2"></i> Data amalan berhasil diperbarui!
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    @if ($role_user == 'mahasiswa')
    // Hitung grand total awal pas halaman dibuka pertama kali
    hitungGrandTotalAwal();

    // 1. UPDATE AMALAN YAUMIYAH BIASA VIA AJAX
    $('.amalan-check').on('change', function() {
        const input = $(this);
        const tgl = input.data('tgl');
        const kolom = input.data('kolom');
        const nilai = input.is(':checked') ? 1 : 0;

        kirimData(tgl, kolom, nilai);
    });

    // 2. UPDATE SHALAT 5 WAKTU HORIZONTAL VIA AJAX
    $('.shalat-mini-check').on('change', function() {
        const input = $(this);
        const tgl = input.data('tgl');
        const kolom = input.data('kolom');
        const group = input.closest('.d-flex').find('.shalat-mini-check');

        let totalCeklis = 0;

        group.each(function() {
            if ($(this).is(':checked')) totalCeklis++;
        });

        kirimData(tgl, kolom, totalCeklis);
    });

    function kirimData(tgl, kolom, nilai) {
        $.ajax({
            url: "{{ route('amalan.update') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                tanggal: tgl,
                kolom: kolom,
                nilai: nilai
            },
            success: function(response) {
                $('#notif').fadeIn().delay(800).fadeOut();
                updatePercentage(kolom);
            },
            error: function(xhr) {
                alert('Gagal menyimpan data.');
                console.log(xhr.responseText);
            }
        });
    }

    function updatePercentage(kolom) {
        let total = 0; let count = 0; let target = 0; let tipe = '';
        if(kolom === 'shalat_5_waktu') {
            $('.shalat-mini-check').each(function() {
                target = parseInt($(this).data('target'));
                tipe = $(this).data('tipe');
            });
            $('.table-amalan tbody tr:first-child .d-flex').each(function() {
                count++;
                let subTotal = 0;
                $(this).find('.shalat-mini-check').each(function() { if($(this).is(':checked')) subTotal++; });
                total += subTotal;
            });
        } else {
            $(`.amalan-check[data-kolom="${kolom}"]`).each(function() {
                count++;
                if($(this).is(':checked')) total += 1;
                target = parseInt($(this).data('target'));
                tipe = $(this).data('tipe');
            });
        }
        if(count > 0) {
            let pct = (tipe === 'harian') ? (total / count) / target * 100 : (total / target) * 100;
            $(`#pct-${kolom}`).text(Math.min(pct, 100).toFixed(1) + '%');
        }
        recalcGrandTotal();
    }

    function recalcGrandTotal() {
        let sumAllpct = 0;
        $('.percent-badge').each(function() { sumAllpct += parseFloat($(this).text()) || 0; });
        let grandTotal = sumAllpct / 8;
        $('#grand-total-pct').text(grandTotal.toFixed(1) + '%');
    }

    function hitungGrandTotalAwal() { recalcGrandTotal(); }
    <?php else: ?>
    // FILTER LIVE SEARCH SISI KEPALA ASRAMA
    document.getElementById('searchAwardee').addEventListener('keyup', function(){
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll('#tabelAmalan tbody tr');
        rows.forEach(row => {
            let nama = row.cells[1] ? row.cells[1].innerText : '';
            if(nama.toUpperCase().indexOf(filter) > -1) { row.style.style.display = ""; } 
            else { row.style.display = "none"; }
        });
    });
    @endif
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>