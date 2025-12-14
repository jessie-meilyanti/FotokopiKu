<?php
// ...existing code...
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Penjualan 12 Bulan</title>
  <link rel="stylesheet" href="../assets/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Admin - Dashboard</h1>
      <nav>
        <a href="dashboard.php">Hasil Penjualan 12 Bulan</a>
        <a href="orders.php">Pesanan Users</a>
        <!-- keranjang dihapus -->
      </nav>
    </div>

    <div class="card">
      <h2>Hasil Penjualan (12 bulan terakhir)</h2>
      <canvas id="salesChart" width="800" height="300"></canvas>
    </div>

    <div class="card">
      <h2>Pesanan Masuk (Realtime)</h2>
      <div id="ordersContainer" class="orders-list"></div>
    </div>
  </div>

<script>
let ctx = document.getElementById('salesChart').getContext('2d');
let chart = new Chart(ctx, {
  type: 'line',
  data: { labels: [], datasets: [{ label:'Total Penjualan', data: [], borderColor:'#2463eb', backgroundColor:'rgba(36,99,235,0.08)', fill:true }] },
  options: { responsive:true, maintainAspectRatio:false }
});

function statusClass(st){
  if (!st) return 'status-pending';
  st = st.toLowerCase();
  if (st === 'processing') return 'status-processing';
  if (st === 'completed' || st === 'done' || st === 'shipped') return 'status-completed';
  if (st === 'cancelled' || st === 'canceled') return 'status-cancelled';
  return 'status-pending';
}

async function loadSales(){
  try {
    const res = await fetch('../api/admin/sales.php');
    const j = await res.json();
    if (j.labels) {
      chart.data.labels = j.labels;
      chart.data.datasets[0].data = j.totals;
      chart.update();
    }
  } catch(e){ console.error(e); }
}

async function loadOrders(){
  try {
    const res = await fetch('../api/admin/orders.php?limit=100');
    const j = await res.json();
    if (!j.success) return;
    const cont = document.getElementById('ordersContainer');
    cont.innerHTML = '';
    j.orders.forEach(o=>{
      const div = document.createElement('div');
      div.className = 'card fade-in';
      const badge = `<span class="badge ${statusClass(o.status)}">${o.status}</span>`;
      div.innerHTML = `
        <div><strong>#${o.id}</strong> ${badge}</div>
        <div>${o.customer_name} ${o.tracking_code ? '- ' + o.tracking_code : ''}</div>
        <div>Rp ${Number(o.total).toLocaleString()}</div>
        <div style="margin-top:8px"><small>${o.created_at}</small></div>
      `;
      cont.appendChild(div);
      requestAnimationFrame(()=> div.classList.add('show'));
    });
  } catch(e){ console.error(e); }
}

// initial + polling every 5s
loadSales();
loadOrders();
setInterval(()=>{ loadSales(); loadOrders(); }, 5000);
</script>
</body>
</html>