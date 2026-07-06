<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Fire Monitoring Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --bg:#020617;
    --card:rgba(255,255,255,.08);
    --border:rgba(255,255,255,.15);
    --text:#e5e7eb;
    --muted:#94a3b8;
    --success:#22c55e;
    --warning:#f97316;
    --danger:#ef4444;
    --accent:#38bdf8;
}

* { box-sizing: border-box; }

body {
    margin:0;
    font-family:"Segoe UI",Arial,sans-serif;
    background:radial-gradient(circle at top,#1e293b,#020617);
    color:var(--text);
}

.container {
    max-width:900px;
    margin:40px auto;
    padding:0 16px;
}

.card {
    background:var(--card);
    backdrop-filter:blur(14px);
    border:1px solid var(--border);
    border-radius:20px;
    padding:26px;
    box-shadow:0 25px 60px rgba(0,0,0,.45);
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.status {
    padding:6px 16px;
    border-radius:999px;
    font-weight:700;
}

.NORMAL {
    background:rgba(34,197,94,.2);
    color:var(--success);
}

.WARNING {
    background:rgba(249,115,22,.2);
    color:var(--warning);
}

.CRITICAL {
    background:rgba(239,68,68,.25);
    color:var(--danger);
    animation:pulse 1.2s infinite;
}

@keyframes pulse {
    0% { box-shadow:0 0 0 0 rgba(239,68,68,.6); }
    70% { box-shadow:0 0 0 12px rgba(239,68,68,0); }
    100% { box-shadow:0 0 0 0 rgba(239,68,68,0); }
}

.metrics {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:18px;
    margin:26px 0;
}

.metric {
    background:rgba(255,255,255,.06);
    border-radius:16px;
    padding:18px;
    text-align:center;
    border:1px solid var(--border);
}

.metric p {
    margin:0;
    font-size:13px;
    color:var(--muted);
}

.metric span {
    font-size:30px;
    font-weight:700;
}

.footer {
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.btn {
    background:linear-gradient(135deg,#38bdf8,#0ea5e9);
    padding:10px 18px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
    color:#020617;
}
</style>
</head>

<body>

<div class="container">
<div class="card">

<div class="header">
    <h2>Fire Monitoring</h2>
    <span id="status" class="status">-</span>
</div>

<div class="metrics">
    <div class="metric">
        <p>Suhu (°C)</p>
        <span id="temp">-</span>
    </div>
    <div class="metric">
        <p>Kelembaban (%)</p>
        <span id="hum">-</span>
    </div>
    <div class="metric">
        <p>Sensor Api</p>
        <span id="flame">-</span>
    </div>
</div>

<div class="footer">
    <small id="last-update">Last update: -</small>
    <a href="/logs" class="btn">Lihat Log</a>
</div>

<canvas id="trendChart" height="120"></canvas>

</div>
</div>

<script>
let chart;

function initChart(labels, temp, hum) {
    const ctx = document.getElementById('trendChart');
    chart = new Chart(ctx, {
        type:'line',
        data:{
            labels,
            datasets:[
                {
                    label:'Suhu (°C)',
                    data:temp,
                    borderColor:'#ef4444',
                    tension:.3
                },
                {
                    label:'Kelembaban (%)',
                    data:hum,
                    borderColor:'#38bdf8',
                    tension:.3
                }
            ]
        }
    });
}

async function fetchLatest() {
    const res = await fetch('/api/latest');
    const d = await res.json();

    if (!d) return;

    document.getElementById('temp').innerText = d.temperature;
    document.getElementById('hum').innerText  = d.humidity;
    document.getElementById('flame').innerText = d.flame ? 'TERDETEKSI' : 'AMAN';

    const s = document.getElementById('status');
    s.innerText = d.status;
    s.className = 'status ' + d.status;

    document.getElementById('last-update').innerText =
        'Last update: ' + new Date().toLocaleString('id-ID');
}

async function fetchTrends() {
    const res = await fetch('/api/trends');
    const logs = await res.json();

    const labels = logs.map(l =>
        new Date(l.created_at).toLocaleTimeString('id-ID')
    );
    const temp = logs.map(l => l.temperature);
    const hum  = logs.map(l => l.humidity);

    if (!chart) initChart(labels,temp,hum);
    else {
        chart.data.labels = labels;
        chart.data.datasets[0].data = temp;
        chart.data.datasets[1].data = hum;
        chart.update();
    }
}

fetchLatest();
fetchTrends();

setInterval(fetchLatest, 1000);
setInterval(fetchTrends, 1000);
</script>

</body>
</html>
