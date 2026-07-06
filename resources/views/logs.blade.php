<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Log Kebakaran CRITICAL</title>

<style>
:root {
    --bg:#020617;
    --card:rgba(255,255,255,.08);
    --border:rgba(255,255,255,.15);
    --text:#e5e7eb;
    --muted:#94a3b8;
    --danger:#ef4444;
    --accent:#38bdf8;
}

body {
    margin:0;
    font-family:"Segoe UI",Arial,sans-serif;
    background:radial-gradient(circle at top,#1e293b,#020617);
    color:var(--text);
}

.container {
    max-width:1100px;
    margin:30px auto;
    padding:0 16px;
}

.card {
    background:var(--card);
    border-radius:20px;
    padding:24px;
    box-shadow:0 25px 60px rgba(0,0,0,.45);
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
}

table {
    width:100%;
    margin-top:20px;
    border-collapse:separate;
    border-spacing:0 8px;
    font-size:13px;
}

td,th {
    padding:10px;
    text-align:center;
}

tbody tr {
    background:rgba(255,255,255,.06);
}

.badge {
    padding:5px 12px;
    border-radius:999px;
    background:rgba(239,68,68,.3);
    color:var(--danger);
    font-weight:700;
}

.pagination {
    display:flex;
    justify-content:space-between;
    margin-top:16px;
    font-size:13px;
}

button {
    background:rgba(255,255,255,.1);
    border:none;
    color:var(--text);
    padding:6px 12px;
    border-radius:8px;
    cursor:pointer;
}

button.active {
    background:var(--accent);
    color:#020617;
}
</style>
</head>

<body>

<div class="container">
<div class="card">

<div class="header">
    <h2>Log Kebakaran (CRITICAL)</h2>
  <div style="display: flex; gap: 10px; align-items: center; margin: 20px 0;">
    <button style="padding: 8px 16px; background-color: #38bdf8; border: none; border-radius: 5px; cursor: pointer;">
        <a href="/" style="color: #fff; text-decoration: none; font-weight: bold;">Kembali</a>
    </button>
    
    <button style="padding: 8px 16px; background-color: #fff; border: 2px solid #38bdf8; border-radius: 5px; cursor: pointer;">
        <a href="/export-pdf" style="color: #38bdf8; text-decoration: none; font-weight: bold;">Export PDF</a>
    </button>
</div>

</div>

<table>
<thead>
<tr>
    <th>No</th>
    <th>Waktu</th>
    <th>Suhu</th>
    <th>Kelembaban</th>
    <th>Api</th>
    <th>Status</th>
</tr>
</thead>
<tbody id="logBody">
<tr><td colspan="6">Loading...</td></tr>
</tbody>
</table>

<div class="pagination">
    <div id="info"></div>
    <div>
        <button id="prev">Prev</button>
        <span id="pages"></span>
        <button id="next">Next</button>
    </div>
</div>

</div>
</div>

<script>
let page = 1;
let lastPage = 1;

async function loadLogs() {
    const res = await fetch(`/api/logs/critical?page=${page}`);
    const json = await res.json();

    lastPage = json.last_page;
    let no = json.from ?? 1;
    let html = '';

    if (!json.data.length) {
        html = `<tr><td colspan="6">Tidak ada data</td></tr>`;
    } else {
        json.data.forEach(l => {
            const d = new Date(l.created_at);
            html += `
            <tr>
                <td>${no++}</td>
                <td>${d.toLocaleString('id-ID')}</td>
                <td>${l.temperature}</td>
                <td>${l.humidity}</td>
                <td>${l.flame ? 'TERDETEKSI' : 'AMAN'}</td>
                <td><span class="badge">${l.status}</span></td>
            </tr>`;
        });
    }

    document.getElementById('logBody').innerHTML = html;
    document.getElementById('info').innerText =
        `Showing ${json.from}–${json.to} of ${json.total}`;

    let pages = '';
    for (let i=1;i<=json.last_page;i++) {
        pages += `<button class="${i===page?'active':''}" onclick="gotoPage(${i})">${i}</button>`;
    }
    document.getElementById('pages').innerHTML = pages;

    document.getElementById('prev').disabled = page<=1;
    document.getElementById('next').disabled = page>=lastPage;
}

function gotoPage(p) {
    page = p;
    loadLogs();
}

document.getElementById('prev').onclick = () => {
    if (page>1) { page--; loadLogs(); }
};

document.getElementById('next').onclick = () => {
    if (page<lastPage) { page++; loadLogs(); }
};

loadLogs();
setInterval(loadLogs, 2000);
</script>

</body>
</html>
