<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Fire Monitoring</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0 0;
            font-size: 11px;
        }

        .meta {
            margin-bottom: 15px;
        }

        .meta table {
            width: 100%;
        }

        .meta td {
            padding: 2px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 11px;
        }

        table.data th {
            background-color: #eaeaea;
            font-weight: bold;
        }

        .status-normal {
            font-weight: bold;
            color: #198754;
        }

        .status-warning {
            font-weight: bold;
            color: #fd7e14;
        }

        .status-critical {
            font-weight: bold;
            color: #dc3545;
        }

        .footer {
            margin-top: 20px;
            text-align: justify;
        }

        .footer h4 {
            margin-bottom: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h2>Laporan Monitoring Kebakaran</h2>
        <p>Smart Fire Detection IoT</p>
    </div>

    <!-- META INFO -->
    <div class="meta">
        <table>
            <tr>
                <td width="20%">Tanggal Cetak</td>
                <td width="2%">:</td>
                <td>{{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }}</td>
            </tr>
            <tr>
                <td>Sumber Data</td>
                <td>:</td>
                <td>ESP8266 + DHT22 + Flame Sensor</td>
            </tr>
        </table>
    </div>

    <!-- DATA TABLE -->
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Suhu (°C)</th>
                <th>Kelembaban (%)</th>
                <th>Flame</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div>{{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</div>
                        <small>{{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }}</small>
                    </td>
                    <td>{{ $log->temperature }}</td>
                    <td>{{ $log->humidity }}</td>
                    <td>{{ $log->flame ? 'TERDETEKSI' : 'AMAN' }}</td>
                    <td class="
                        @if(strtoupper($log->status) === 'NORMAL') status-normal
                        @elseif(strtoupper($log->status) === 'WARNING') status-warning
                        @else status-critical
                        @endif
                    ">
                        {{ strtoupper($log->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER / KESIMPULAN -->
    <div class="footer">
        <h4>Kesimpulan</h4>
        <p>
        Berdasarkan hasil monitoring yang tercatat, sistem Smart Fire Detection IoT
        mendeteksi kondisi CRITICAL (potensi kebakaran) secara real-time.
        Sistem secara otomatis mengklasifikasikan status lingkungan, dan laporan ini
        menampilkan kejadian dengan status <strong>Critical</strong> sebagai alat
        pendukung pencegahan dini kebakaran.
        </p>
    </div>

</body>
</html>
