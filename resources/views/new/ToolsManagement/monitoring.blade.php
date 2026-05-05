<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Monitoring Kendaraan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        body {
            background: #f4f6f9;
            font-size: 18px;
        }

        .container-fluid {
            width: 100%;
            padding: 10px 25px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -8px;
        }

        [class*="col-"] {
            padding: 8px;
        }

        .col-md-3 {
            width: 25%;
        }

        .col-md-6 {
            width: 50%;
        }

        .d-flex {
            display: flex;
        }

        .w-100 {
            width: 100%;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .text-center {
            text-align: center;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mt-1 {
            margin-top: 5px;
        }

        .mr-2 {
            margin-right: 8px;
        }

        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .kpi-value {
            font-size: 40px;
            font-weight: bold;
        }

        .kpi-label {
            font-size: 14px;
            color: #6b7280;
        }

        .section-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 15px;
            height: auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }


        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .vehicle-card {
            background: #f9fafb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 6px solid #3b82f6;
        }

        .small {
            font-size: 14px;
            color: #6b7280;
        }

        .text-warning {
            color: #f59e0b;
        }

        .text-primary {
            color: #3b82f6;
        }

        .text-success {
            color: #22c55e;
        }

        .text-danger {
            color: #ef4444;
        }

        .text-info {
            color: #06b6d4;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }

        .badge-warning {
            background: #f59e0b;
        }

        .badge-primary {
            background: #3b82f6;
        }

        .badge-success {
            background: #22c55e;
        }

        .scroll-box {
            overflow: hidden;
        }

        .scroll-box::-webkit-scrollbar {
            width: 6px;
        }

        .scroll-box::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .scroll-box::-webkit-scrollbar-track {
            background: transparent;
        }



        .full-height {
            height: calc(100vh - 140px);
        }

        .big-clock {
            font-size: 48px;
            font-weight: 700;
        }

        .big-date {
            font-size: 16px;
            color: #6b7280;
        }

        .slide-in {
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(15px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .new-item {
            animation: highlight 1s ease-in-out 2;
        }

        @keyframes highlight {
            0% {
                background: #fff7ed;
            }

            50% {
                background: #fde68a;
            }

            100% {
                background: transparent;
            }
        }


        @keyframes highlight {
            0% {
                background: #fff7ed;
            }

            50% {
                background: #fde68a;
            }

            100% {
                background: #f9fafb;
            }
        }

        @media (max-width: 992px) {
            .col-md-3 {
                width: 50%;
            }

            .col-md-6 {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .col-md-3 {
                width: 100%;
            }
        }

        .marquee {
            position: relative;
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
        }

        .marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 12s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        thead th {
            position: sticky;
            top: 0;
            background: #f1f5f9;
            z-index: 1;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tfoot th {
            background: #f1f5f9;
            position: sticky;
            bottom: 0;
        }

        .table-scroll {
            height: 400px;
            overflow: hidden;
            position: relative;
        }

        .table-scroll-inner {
            display: block;
        }

        .table-scroll table {
            width: 100%;
            border-collapse: collapse;
        }

        #openOperator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: none;
            background: #111827;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        #operatorPanel {
            position: fixed;
            top: 0;
            right: -350px;
            width: 320px;
            height: 100%;
            background: #ffffff;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            z-index: 9999;
        }

        #operatorPanel.active {
            right: 0;
        }

        .operator-box {
            padding: 20px;
        }

        .operator-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: bold;
        }

        #operatorPanel input,
        #operatorPanel select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        #exportBtn {
            width: 100%;
            padding: 10px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container-fluid">

        <div class="text-center mb-2">
            <div id="bigClock" class="big-clock">00:00:00</div>
            <div id="bigDate" class="big-date">-</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value" id="totalVehicle">0</div>
                    <div class="kpi-label">Total Kendaraan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value text-warning" id="loading">0</div>
                    <div class="kpi-label">Proses Muat</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value text-primary" id="unloading">0</div>
                    <div class="kpi-label">Proses Bongkar</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value text-success" id="done">0</div>
                    <div class="kpi-label">Selesai</div>
                </div>
            </div>
        </div>

        <div class="row full-height">
            <div class="col-md-6 d-flex">
                <div class="section-card w-100">
                    <div class="section-title">
                        🚚 Inbound
                        <div id="inboundTitle" class="small"></div>
                    </div>
                    <div id="inboundList" class="table-scroll"></div>
                </div>

            </div>

            <div class="col-md-6 d-flex">
                <div class="section-card w-100">
                    <div class="section-title">
                        📦 Outbound
                        <div id="outboundTitle" class="small"></div>
                    </div>
                    <div id="outboundList" class="table-scroll"></div>
                </div>

            </div>
        </div>

    </div>

    <div id="operatorPanel">
        <div class="operator-box">
            <div class="operator-header">
                <span>⚙️ Operator Mode</span>
                <button id="closeOperator">✖</button>
            </div>
            <div class="filter-group">
                <label for="">Start Date</label>
                <input type="date" id="startDate" required value="{{ date('Y-m-01') }}">
            </div>
            <div class="filter-group">
                <label for="">End Date</label>
                <input type="date" id="endDate" required value="{{ date('Y-m-d') }}">
                <span class="text-muted">End Date</span>
            </div>
            <button id="exportBtn" onclick="exportData()">📤 Export CSV</button>
        </div>
    </div>

    <button id="openOperator">⚙️</button>


    <script>
        function isMakassar() {
            return window.location.pathname.includes('makassar');
        }

        document.getElementById('openOperator').onclick = () => {
            document.getElementById('operatorPanel').classList.add('active');
        };

        document.getElementById('closeOperator').onclick = () => {
            document.getElementById('operatorPanel').classList.remove('active');
        };

        function exportData() {
            let startDate = document.getElementById('startDate').value;
            let endDate = document.getElementById('endDate').value;
            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal belum lengkap',
                    text: 'Pastikan kedua tanggal terisi'
                });
                return;
            }

            if (startDate > endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal tidak valid',
                    text: 'Start Date tidak boleh lebih besar dari End Date'
                });
                return;
            }

            Swal.fire({
                title: 'Generating report...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.open(
                `{{ url('warehouse/gate-in/monitoring/exportData') }}/${startDate}/${endDate}/{{ $siteMaster->id }}`,
                '_blank');
            Swal.close();
        }


        let lastIds = [];

        function getDuration(start) {
            let now = getAdjustedNow().getTime();
            let diff = Math.floor((now - start) / 1000);
            let h = Math.floor(diff / 3600);
            let m = Math.floor((diff % 3600) / 60);
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
        }


        function renderList(data, target) {
            let header = `
        <tr style="background:#f1f5f9;">
            <th style="padding:8px;">Vehicle  No</th>
            <th>Driver</th>
            <th>Type</th>
            <th>Principal</th>
            <th>Duration</th>
        </tr>
    `;

            let table = `
    <table>
        <thead class="text-center">${header}</thead>
        <tbody>
    `;

            data.forEach(item => {
                let isNew = !lastIds.includes(item.id);
                let duration = getDuration(item.start_time);
                table += `
        <tr class="text-center ${isNew ? 'new-item' : ''}">
            <td style="padding:8px;"><strong>${item.no_mobil}</strong></td>
            <td>${item.supir}</td>
            <td>${item.type}</td>
            <td>${item.principal_name}</td>
            <td>⏱ ${duration}</td>
        </tr>
        `;
            });

            table += `
        </tbody>
    </table>
    `;

            let html = `
        <div class="table-scroll-inner">
            ${table}
            ${table}
        </div>
    `;

            let container = document.getElementById(target);

            if (data.length < 6) {
                container.innerHTML = table;
                return;
            }

            container.innerHTML = html;

            startAutoScroll(container);
        }


        function startAutoScroll(container) {
            let scrollSpeed = 0.3;
            let inner = container.querySelector('.table-scroll-inner');

            let scrollPos = 0;

            function step() {
                scrollPos += scrollSpeed;

                if (scrollPos >= inner.scrollHeight / 2) {
                    scrollPos = 0;
                }

                container.scrollTop = scrollPos;
                requestAnimationFrame(step);
            }

            requestAnimationFrame(step);
        }



        function loadMonitoring() {
            $.ajax({
                url: "{{ url('warehouse/gate-in/monitoring/list') }}/{{ $siteMaster->id }}",
                method: "GET",
                dataType: "json",
                success: function(res) {

                    document.body.style.opacity = 0.95;

                    setTimeout(() => {
                        let inboundText = principalSummaryText(res.inbound);
                        let outboundText = principalSummaryText(res.outbound);
                        document.getElementById('inboundTitle').innerHTML = `${inboundText || '0'}`;
                        document.getElementById('outboundTitle').innerHTML = `${outboundText || '0'}`;
                        renderList(res.inbound, 'inboundList');
                        renderList(res.outbound, 'outboundList');
                        document.getElementById('totalVehicle').innerText = res.total;
                        document.getElementById('loading').innerText = res.loading;
                        document.getElementById('unloading').innerText = res.unloading;
                        document.getElementById('done').innerText = res.done;
                        let combined = [
                            ...res.inbound,
                            ...res.outbound,
                            ...res.doneList
                        ];
                        lastIds = combined.map(x => x.id);
                        document.body.style.opacity = 1;
                    }, 200);
                },

                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal load data',
                        text: 'Cek koneksi / server'
                    });
                }
            });
        }

        function cleanName(name) {
            if (!name) return 'LAINNYA';
            return name
                .replace(/,?\s*PT\.?/gi, '')
                .replace(/\s+/g, ' ')
                .trim();
        }


        function getAdjustedNow() {
            let now = new Date();
            if (isMakassar()) {
                now.setHours(now.getHours() + 1);
            }

            return now;
        }

        function updateClock() {
            const now = getAdjustedNow();

            let day = String(now.getDate()).padStart(2, '0');
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let year = now.getFullYear();

            let hours = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');
            let seconds = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('bigClock').innerText = `${hours}:${minutes}:${seconds}`;
            document.getElementById('bigDate').innerText = `${day}-${month}-${year}`;
        }


        function principalSummaryText(data) {
            let map = {};
            data.forEach(item => {
                let key = cleanName(item.principal_name) || '-';
                map[key] = (map[key] || 0) + 1;
            });
            let text = Object.entries(map)
                .map(([name, total]) => `${name} ${total}`)
                .join(' • ');
            if (!text) return '-';
            if (text.length < 30) {
                return text;
            }
            return `<div class="marquee"><span>${text}</span></div>`;
        }

        loadMonitoring();
        setInterval(loadMonitoring, 60000);
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
