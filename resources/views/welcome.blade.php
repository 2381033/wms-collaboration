<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>Site‑plan Lurus (tanpa miring)</title>
    <link rel="icon" href="http://example.com/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --plot: 30px;
            --plan: 950px;
        }

        html,
        body {
            touch-action: manipulation;
            overflow-x: auto;
        }

        .header-title {
            font-size: 2rem;
            font-weight: 700;
            color: #0077b6;
            text-align: center;
            padding: 1rem 0;
            background-color: #ffffffaa;
            margin: 0 auto 1rem auto;
            border-bottom: 2px solid #dce3eb;
            letter-spacing: 2px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .plan-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .layout-wrapper {
            width: max-content;
            min-width: 100%;
        }

        .scroll-area {
            flex: 1 1 0;
        }

        /* === Block Style === */
        .kavling-block {
            background: #fff;
            border: none;
            padding: 8px;
            position: relative;
            margin-top: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease-in-out;
        }

        .kavling-block:hover {
            transform: translateY(-2px);
        }

        /* Label modern */
        .block-label {
            position: absolute;
            top: -14px;
            left: 10px;
            background: #0077b6;
            color: #fff;
            padding: 3px 10px;
            font-weight: 600;
            font-size: 13px;
            border-radius: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* === Grid === */
        .kavling-grid {
            display: grid;
            gap: 4px;
        }

        .kavling-plot {
            border: 1px solid #b2dfdb;
            aspect-ratio: 1;
            min-height: var(--plot);
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.2s ease-in-out;
        }

        .kavling-plot:hover {
            background-color: #b2ebf2;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            z-index: 10;
        }

        .status-available {
            color: #eee
        }

        /* Grid helpers */
        .grid-cols-1 {
            grid-template-columns: repeat(1, var(--plot));
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, var(--plot));
        }

        .grid-cols-5 {
            grid-template-columns: repeat(5, var(--plot));
        }

        .grid-cols-10 {
            grid-template-columns: repeat(10, var(--plot));
        }

        .grid-cols-12 {
            grid-template-columns: repeat(12, var(--plot));
        }

        .grid-cols-13 {
            grid-template-columns: repeat(13, var(--plot));
        }

        .grid-cols-17 {
            grid-template-columns: repeat(17, var(--plot));
        }

        .equal-width {
            width: calc(var(--plot)*17 + 12px);
        }

        .equal-width .grid-cols-10 {
            grid-template-columns: repeat(10, 1fr);
        }

        .equal-width .grid-cols-12 {
            grid-template-columns: repeat(12, 1fr);
        }

        .equal-width .grid-cols-13 {
            grid-template-columns: repeat(13, 1fr);
        }

        .equal-width .grid-cols-17 {
            grid-template-columns: repeat(17, 1fr);
        }

        /* Smaller grid */
        .kecilkan-grid .kavling-plot {
            font-size: 8px;
            min-height: 20px;
        }

        /* Cluster border fixes */
        .cluster-d6>.kavling-block:first-child {
            border-right: none;
            padding-right: 0;
        }

        .cluster-d6>.kavling-block:last-child {
            border-left: none;
            padding-left: 0;
        }

        /* === Modern & Elegant Modal Style === */
        #modalDetailWarga .modal-content {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(200, 200, 200, 0.3);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease-in-out;
        }

        #modalDetailWarga .modal-header {
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0;
        }

        #modalDetailWarga .modal-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: #0077b6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #modalDetailWarga .modal-body {
            padding-top: 1rem;
        }

        .info-group {
            background: #f8fafc;
            border-radius: 14px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .info-group .label {
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .info-group .value {
            color: #222;
            font-weight: 600;
            font-size: 1rem;
        }

        .btn-close {
            background-color: #eee;
            border-radius: 50%;
            padding: 0.4rem;
        }

        @media (max-width: 576px) {
            .modal-dialog {
                margin: 1rem;
            }

            .modal-content {
                width: 100%;
                border-radius: 12px;
            }

            .info-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
        }

        @media (max-width: 576px) {
            .kavling-plot {
                font-size: 9px;
                min-height: 24px;
            }
        }

        @media (max-width: 768px) {
            .plan-wrapper {
                zoom: 48%;
                transform-origin: unset !important;
            }

            body {
                overflow-x: auto;
            }
        }

        .legend-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            font-size: 14px;
            font-weight: 500;
            max-width: 700px;
            margin: 0 auto;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .legend-item:hover {
            background: #e9ecef;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            display: inline-block;
            border-radius: 4px;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
        }
    </style>

    <?php
    // 1. Koneksi database
    $conn = new mysqli('localhost', 'root', '', 'db_mkt');
    if ($conn->connect_error) {
        die('Koneksi gagal: ' . $conn->connect_error);
    }
    
    // 2. Ambil data warga
    $sql = 'SELECT block, number, name, sold FROM master_warga';
    $result = $conn->query($sql);
    $warga = [];
    
    while ($row = $result->fetch_assoc()) {
        $key = $row['block'] . '.' . $row['number'];
        $warga[$key] = $row;
    }
    
    function renderPlot($blok, $nomor, $warga)
    {
        $key = $blok . '.' . $nomor;
        $data = $warga[$key] ?? null;
        $nama = $data['name'] ?? 'Belum terisi';
        $sold = $data && strtoupper($data['sold']) === 'YA';
        // Atur warna berdasarkan kondisi
        if (strtolower($nama) === 'ready stock') {
            $color = 'bg-info text-dark'; // biru muda
        } elseif (!$data) {
            $color = 'bg-danger'; // merah jika belum ada data
        } elseif ($sold) {
            $color = 'bg-success'; // hijau
        } else {
            $color = 'bg-danger'; // fallback merah
        }
    
        $nama_escaped = htmlspecialchars($nama, ENT_QUOTES);
    
        echo "<div class='kavling-plot $color' onclick=\"showModal('$blok', '$nomor', '$nama_escaped')\">$nomor</div>";
    }
    
    ?>
</head>

<body class="p-3">
    <div class="header-title">SAPA KALITA</div>
    <div class="container-fluid">
        <div class="legend-container d-flex gap-3 mb-4 justify-content-center align-items-center flex-wrap">
            <div class="legend-item">
                <span class="legend-color bg-success"></span> Terisi
            </div>
            <div class="legend-item">
                <span class="legend-color bg-info border text-dark"></span> Ready Stock
            </div>
            <div class="legend-item">
                <span class="legend-color bg-danger"></span> Belum Terisi
            </div>
        </div>


        <div class="plan-wrapper">
            <div class="layout-wrapper">
                <div class="scroll-area">
                    <div class="d-flex gap-4">
                        <div style="width:180px; margin-right: 80px;">
                            <div class="kavling-block" style="max-width:180px; margin-top:520px;">
                                <div class="block-label">BLOK D7</div>
                                <!-- baris 1‑10 -->
                                <div class="kavling-grid grid-cols-5">
                                    <?php for($i=5;$i>=1;$i--):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D7', $i, $warga); ?></div>
                                    <?php endfor; ?>
                                    <?php for($i=6;$i<=10;$i++):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D7', $i, $warga); ?></div>
                                    <?php endfor; ?>
                                </div>
                                <!-- baris 11‑20 -->
                                <div class="kavling-grid grid-cols-5 mt-3">
                                    <?php for($i=15;$i>10;$i--):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D7', $i, $warga); ?></div>
                                    <?php endfor; ?>
                                    <?php for($i=16;$i<=20;$i++):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D7', $i, $warga); ?></div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>



                        <!-- ---------- KOLOM TENGAH (blok‑blok utama lurus) ---------- -->
                        <div class="d-flex flex-column">

                            <!-- BLOK D3 (sejajar D2 kecil) -->
                            <div class="d-flex align-items-start gap-4 cluster-d6">
                                <div class="kavling-block equal-width">
                                    <div class="block-label">BLOK D3</div>
                                    <div class="kavling-grid grid-cols-10">
                                        <?php for($i=10;$i>=1;$i--):?>
                                        <div class="kavling-plot status-available"><?php renderPlot('D3', $i, $warga); ?></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="kavling-block" style="margin-left: 80px;">
                                    <div class="block-label">BLOK D2</div>
                                    <div class="row gx-0">
                                        <div class="kavling-grid grid-cols-2" style="max-width:60px;">
                                            <?php for($row=0;$row<6;$row++):?>
                                            <div class="kavling-plot status-available"><?php renderPlot('D2', 7 + $row, $warga); ?></div>
                                            <div class="kavling-plot status-available"><?php renderPlot('D2', 6 - $row, $warga); ?></div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOK D4 -->
                            <div class="kavling-block equal-width" style="margin-top:-100px;">
                                <div class="block-label">BLOK D4</div>
                                <div class="kavling-grid grid-cols-13">
                                    <?php for($i=14;$i<=26;$i++):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D4', $i, $warga); ?></div>
                                    <?php endfor; ?>
                                    <?php for($i=13;$i>=1;$i--):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D4', $i == 13 ? '12A' : $i, $warga); ?></div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- BLOK D5 dan RTH kanan -->
                            <br>
                            <div class="d-flex align-items-start gap-4 cluster-d5 mr-4">
                                <div class="kavling-block equal-width">
                                    <div class="block-label">BLOK D5</div>
                                    <div class="kavling-grid grid-cols-12">
                                        <?php for($i=13;$i<=24;$i++):?>
                                        <div class="kavling-plot status-available"><?php renderPlot('D5', $i == 13 ? '12A' : $i, $warga); ?></div>
                                        <?php endfor; ?>
                                        <?php for($i=12;$i>=1;$i--):?>
                                        <div class="kavling-plot status-available"><?php renderPlot('D5', $i, $warga); ?></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== D6 + D1 kecil (sejajar) ===== -->
                            <div class="d-flex align-items-start gap-4 cluster-d6">
                                <div class="kavling-block equal-width mt-4">
                                    <div class="block-label">BLOK D6</div>
                                    <div class="kavling-grid grid-cols-13">
                                        <?php for($i=13;$i>=1;$i--):?>
                                        <div class="kavling-plot status-available"><?php renderPlot('D6', $i == 13 ? '12A' : $i, $warga); ?></div>
                                        <?php endfor; ?>
                                        <?php for($i=14;$i<=26;$i++):?>
                                        <div class="kavling-plot status-available"><?php renderPlot('D6', $i, $warga); ?></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="kavling-block kecilkan-grid" style="background:#f5f5f5; margin-left: 80px;">
                                    <div class="block-label">BLOK D1</div>
                                    <div class="row gx-0">
                                        <div class="col-6">
                                            <div class="kavling-grid grid-cols-1" style="max-width:92px;">
                                                <?php for($i=46;$i>=43;$i--):?>
                                                <div class="kavling-plot status-available"><?php renderPlot('D1', $i, $warga); ?></div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="kavling-grid grid-cols-1" style="max-width:25px;">
                                                <?php for($i=1;$i<=8;$i++):?>
                                                <div class="kavling-plot status-available"><?php renderPlot('D1', $i, $warga); ?></div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOK D1 BESAR -->
                            <div class="kavling-block equal-width" style="margin-top:-85px;width: 650px">
                                <div class="block-label">BLOK D1</div>
                                <div class="kavling-grid grid-cols-17">
                                    <?php for($i=26;$i<=42;$i++):?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D1', $i, $warga); ?></div>
                                    <?php endfor; ?>
                                    <?php for($i=25; $i>=9; $i--): ?>
                                    <div class="kavling-plot status-available"><?php renderPlot('D1', $i == 13 ? '12A' : $i, $warga); ?></div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                        </div><!-- /kolom tengah -->
                    </div><!-- /flex dua kolom -->
                </div><!-- /scroll-area -->
            </div><!-- /layout-wrapper -->
        </div><!-- /layout-wrapper -->

        <!-- Modal Modern -->
        <div class="modal fade" id="modalDetailWarga" tabindex="-1" aria-labelledby="modalDetailLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modern-modal p-4">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalDetailLabel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-person-circle text-primary" viewBox="0 0 16 16">
                                <path d="M11 10a2 2 0 1 0-6 0c0 1.105.895 2 2 2s2-.895 2-2z" />
                                <path fill-rule="evenodd"
                                    d="M8 1a7 7 0 1 1 0 14A7 7 0 0 1 8 1zM3 8a5 5 0 1 0 10 0A5 5 0 0 0 3 8z" />
                            </svg>
                            Informasi Rumah
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="info-group">
                            <span class="label">Blok:</span>
                            <span class="value" id="modalBlok"></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Nomor:</span>
                            <span class="value" id="modalNomor"></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Nama:</span>
                            <span class="value" id="modalNama"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div><!-- /container -->
    <script>
        function showModal(blok, nomor, nama) {
            document.getElementById('modalBlok').innerText = blok;
            document.getElementById('modalNomor').innerText = nomor;
            document.getElementById('modalNama').innerText = nama;
            var modal = new bootstrap.Modal(document.getElementById('modalDetailWarga'));
            modal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
