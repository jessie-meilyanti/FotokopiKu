<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Dashboard Admin</h1>

        <div class="bg-white rounded-lg p-6 shadow">
            <h2 class="font-semibold mb-2">Penjualan 12 Bulan Terakhir</h2>
            <!-- add base64-encoded JSON to avoid Blade syntax inside JS -->
            <canvas id="salesChart" height="120"
                data-months="{{ base64_encode(json_encode($months ?? [])) }}"
                data-values="{{ base64_encode(json_encode($data ?? [])) }}"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function safeParseBase64Json(b64) {
            try {
                return b64 ? JSON.parse(atob(b64)) : [];
            } catch (e) {
                return [];
            }
        }

        function coerceNumericArray(arr) {
            if (!Array.isArray(arr)) return [];
            return arr.map(v => {
                const n = Number(v);
                return Number.isFinite(n) ? n : 0;
            });
        }

        function initChart() {
            const canvas = document.getElementById('salesChart');
            if (!canvas || typeof Chart === 'undefined') return;

            const months = safeParseBase64Json(canvas.dataset.months);
            let data = safeParseBase64Json(canvas.dataset.values);
            data = coerceNumericArray(data);

            const ctx = canvas.getContext ? canvas.getContext('2d') : null;
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.08)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }
    </script>
</x-app-layout>
