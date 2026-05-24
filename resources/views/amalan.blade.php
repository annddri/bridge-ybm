<x-header title="Spiritual Tracker - Bridge" css="css/amalan.css"></x-header>
<script>
    const body = document.querySelector('body');

    body.setAttribute('style', "--accent-color: {{ $accent_color }};")
</script>

<x-sidebar 
    :u="$u" 
    :role-user="$role_user" 
    :foto-path="$foto_path" 
></x-sidebar>

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

<x-footer></x-footer>