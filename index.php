<?php
// ...existing code...
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order - Fotokopi</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Fotokopi - Order</h1>
      <nav>
        <a href="index.php">Home</a>
        <a href="index.php#history">Riwayat Pesanan Saya</a>
      </nav>
    </div>

    <div class="card">
      <h2>Buat Order</h2>
      <form id="orderForm">
        <label>Nama<br><input name="name" required></label><br><br>
        <label>Alamat<br><textarea name="address"></textarea></label><br><br>
        <label>Total (Rp)<br><input name="total" type="number" step="0.01" value="0"></label><br><br>
        <label><input type="checkbox" name="shipping" value="1"> Kirim (dengan tracking)</label><br><br>
        <button type="submit" class="primary btn-hover" id="btnSubmit">Kirim Order</button>
      </form>
      <div id="orderResult" style="margin-top:12px"></div>
    </div>

    <div id="history" class="card">
      <h2>Riwayat Pesanan Saya</h2>
      <div id="historyList" class="orders-list"></div>
    </div>

    <div class="card">
      <h2>Track Order</h2>
      <input id="trackingInput" placeholder="Masukkan tracking code atau order id">
      <button id="btnTrack" class="btn-hover">Track</button>
      <pre id="trackResult" style="margin-top:12px"></pre>
    </div>
  </div>

<script>
// helper localStorage key
const LS_KEY = 'fotokopi_my_orders';

function loadMyOrders() {
  try {
    return JSON.parse(localStorage.getItem(LS_KEY) || '[]');
  } catch(e){ return []; }
}
function saveMyOrders(arr) { localStorage.setItem(LS_KEY, JSON.stringify(arr)); }

function renderHistory() {
  const container = document.getElementById('historyList');
  container.innerHTML = '';
  const ids = loadMyOrders();
  if (!ids.length) { container.innerHTML = '<div class="fade-in show">Belum ada pesanan.</div>'; return; }

  ids.forEach(o => {
    const el = document.createElement('div');
    el.className = 'card fade-in';
    el.innerHTML = `
      <div><strong>#${o.id}</strong> — ${o.created_at ? o.created_at : ''}</div>
      <div>${o.name || ''} ${o.tracking_code ? '- ' + o.tracking_code : ''}</div>
      <div style="margin-top:8px">
        <button class="btn-hover" data-action="refresh" data-id="${o.id}">Refresh</button>
        <button class="btn-hover" data-action="reorder" data-id="${o.id}">Checkout Lagi</button>
        <button class="btn-hover" data-action="cancel" data-id="${o.id}">Batalkan</button>
      </div>
      <pre id="hist-${o.id}" style="margin-top:8px"></pre>
    `;
    container.appendChild(el);
    // reveal
    requestAnimationFrame(()=> el.classList.add('show'));
  });
}

async function fetchOrderAndShow(id, targetPre) {
  try {
    const res = await fetch('api/track.php?order_id='+encodeURIComponent(id));
    const j = await res.json();
    targetPre.textContent = JSON.stringify(j, null, 2);
    return j;
  } catch(e){ targetPre.textContent = 'Error'; return null; }
}

document.getElementById('historyList').addEventListener('click', async function(e){
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.dataset.action;
  const id = btn.dataset.id;
  const pre = document.getElementById('hist-'+id);
  if (action === 'refresh') {
    btn.disabled = true;
    await fetchOrderAndShow(id, pre);
    btn.disabled = false;
  } else if (action === 'cancel') {
    if (!confirm('Batalkan order #' + id + '?')) return;
    btn.disabled = true;
    const res = await fetch('api/cancel_order.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({order_id: id})
    });
    const j = await res.json();
    alert(j.message || JSON.stringify(j));
    btn.disabled = false;
  } else if (action === 'reorder') {
    // panggil place_order dengan data dari original order
    btn.disabled = true;
    const old = loadMyOrders().find(x=>String(x.id)===String(id));
    if (!old) { alert('Data order tidak ditemukan'); btn.disabled=false; return; }
    const fd = new FormData();
    fd.append('name', old.name || '');
    fd.append('address', old.address || '');
    fd.append('total', old.total || 0);
    if (old.shipping) fd.append('shipping','1');
    const res = await fetch('api/place_order.php',{method:'POST', body:fd});
    const j = await res.json();
    if (j.success && j.order) {
      // store new order id
      const arr = loadMyOrders();
      arr.unshift({id:j.order.id, name:j.order.customer_name, tracking_code:j.order.tracking_code, shipping:j.order.shipping, total:j.order.total, created_at:j.order.created_at, address:j.order.address});
      saveMyOrders(arr);
      renderHistory();
      alert('Order dibuat ulang: #' + j.order.id);
    } else {
      alert('Gagal: ' + (j.message || JSON.stringify(j)));
    }
    btn.disabled = false;
  }
});

document.getElementById('orderForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const btn = document.getElementById('btnSubmit');
  btn.disabled = true;
  btn.style.opacity = '0.85';
  // gentle animation
  btn.classList.add('btn-hover');
  const fd = new FormData(e.target);
  const res = await fetch('api/place_order.php', { method:'POST', body: fd });
  const j = await res.json();
  if (j.success && j.order) {
    // simpan ke localHistory
    const arr = loadMyOrders();
    arr.unshift({id:j.order.id, name:j.order.customer_name, tracking_code:j.order.tracking_code, shipping:j.order.shipping, total:j.order.total, created_at:j.order.created_at, address:j.order.address});
    saveMyOrders(arr);
    renderHistory();
    document.getElementById('orderResult').textContent = 'Order berhasil: #' + j.order.id + (j.order.tracking_code ? ' Tracking: ' + j.order.tracking_code : '');
  } else {
    document.getElementById('orderResult').textContent = 'Gagal: ' + (j.message || JSON.stringify(j));
  }
  btn.disabled = false;
  btn.style.opacity = '';
  setTimeout(()=>btn.classList.remove('btn-hover'),250);
});

document.getElementById('btnTrack').addEventListener('click', async function(){
  const q = document.getElementById('trackingInput').value.trim();
  if (!q) return;
  const url = 'api/track.php?tracking_code=' + encodeURIComponent(q);
  const res = await fetch(url);
  const j = await res.json();
  document.getElementById('trackResult').textContent = JSON.stringify(j, null, 2);
});

// init
renderHistory();
</script>
</body>
</html>