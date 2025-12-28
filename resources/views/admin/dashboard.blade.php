<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📊 Dasbor</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Selamat datang di Fotokopiku!</p>
            </div>
            <div class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border-l-4 border-green-500 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">💰</div>
                    <div class="text-xs bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded font-medium">Total</div>
                </div>
                <div class="text-2xl font-bold mb-1 text-green-600 dark:text-green-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Pendapatan</div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border-l-4 border-blue-500 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">📦</div>
                    <div class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded font-medium">Pesanan</div>
                </div>
                <div class="text-2xl font-bold mb-1 text-blue-600 dark:text-blue-400">{{ number_format($totalOrders) }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Pesanan</div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border-l-4 border-yellow-500 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">⏳</div>
                    <div class="text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded font-medium">Menunggu</div>
                </div>
                <div class="text-2xl font-bold mb-1 text-yellow-600 dark:text-yellow-400">{{ $ordersByStatus['pending'] ?? 0 }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Menunggu Konfirmasi</div>
            </div>

            <!-- Completed Orders -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border-l-4 border-purple-500 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">✅</div>
                    <div class="text-xs bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-2 py-1 rounded font-medium">Selesai</div>
                </div>
                <div class="text-2xl font-bold mb-1 text-purple-600 dark:text-purple-400">{{ $ordersByStatus['completed'] ?? 0 }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Pesanan Selesai</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span>📈</span>
                    <span>Penjualan Tahun 2025</span>
                </h2>
                @if($monthlySales->isNotEmpty())
                    <div class="relative" style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
                    <script>
                        (function(){
                            const isDark = document.documentElement.classList.contains('dark');
                            const ctx = document.getElementById('salesChart');
                            if(!ctx) return;
                            
                            // Format bulan Indonesia lengkap
                            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            const monthNumbers = @json($monthlySales->pluck('month')->map(fn($m) => intval(\Carbon\Carbon::parse($m)->format('m'))));
                            const months = monthNumbers.map(n => monthNames[n - 1]);
                            const revenues = @json($monthlySales->pluck('revenue'));
                            const orders = @json($monthlySales->pluck('orders'));
                            
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: months,
                                    datasets: [{
                                        label: 'Pendapatan (Rp)',
                                        data: revenues,
                                        backgroundColor: 'rgba(99, 102, 241, 0.75)',
                                        borderColor: 'rgba(99, 102, 241, 1)',
                                        borderWidth: 2,
                                        borderRadius: 6,
                                        yAxisID: 'y'
                                    }, {
                                        label: 'Pesanan',
                                        data: orders,
                                        type: 'line',
                                        borderColor: 'rgba(16, 185, 129, 1)',
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        borderWidth: 2.5,
                                        tension: 0.4,
                                        fill: true,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2,
                                        yAxisID: 'y1'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            labels: {
                                                color: isDark ? '#E5E7EB' : '#1F2937',
                                                font: { 
                                                    size: 11,
                                                    weight: '600'
                                                },
                                                padding: 12,
                                                usePointStyle: true
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1F2937' : '#FFFFFF',
                                            titleColor: isDark ? '#F3F4F6' : '#111827',
                                            bodyColor: isDark ? '#D1D5DB' : '#374151',
                                            borderColor: isDark ? '#374151' : '#E5E7EB',
                                            borderWidth: 1,
                                            padding: 10,
                                            displayColors: true,
                                            titleFont: {
                                                size: 12,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 11
                                            },
                                            callbacks: {
                                                label: function(context) {
                                                    let label = context.dataset.label || '';
                                                    if (label) label += ': ';
                                                    if (context.parsed.y !== null) {
                                                        if(context.datasetIndex === 0) {
                                                            label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                                        } else {
                                                            label += context.parsed.y + ' order';
                                                        }
                                                    }
                                                    return label;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            type: 'linear',
                                            position: 'left',
                                            ticks: {
                                                color: isDark ? '#D1D5DB' : '#4B5563',
                                                font: {
                                                    size: 9
                                                },
                                                padding: 6,
                                                callback: function(value) {
                                                    if(value >= 1000000) {
                                                        return 'Rp' + (value/1000000).toFixed(0) + 'jt';
                                                    }
                                                    return 'Rp' + (value/1000) + 'k';
                                                }
                                            },
                                            grid: {
                                                color: isDark ? 'rgba(55, 65, 81, 0.5)' : 'rgba(229, 231, 235, 0.7)',
                                                drawBorder: false
                                            }
                                        },
                                        y1: {
                                            type: 'linear',
                                            position: 'right',
                                            ticks: {
                                                color: isDark ? '#D1D5DB' : '#4B5563',
                                                font: {
                                                    size: 9
                                                },
                                                padding: 6
                                            },
                                            grid: {
                                                drawOnChartArea: false,
                                                drawBorder: false
                                            }
                                        },
                                        x: {
                                            ticks: {
                                                color: isDark ? '#D1D5DB' : '#4B5563',
                                                font: {
                                                    size: 9,
                                                    weight: '500'
                                                },
                                                padding: 4,
                                                maxRotation: 45,
                                                minRotation: 45
                                            },
                                            grid: {
                                                display: false,
                                                drawBorder: false
                                            }
                                        }
                                    }
                                }
                            });
                        })();
                    </script>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data penjualan</p>
                @endif
            </div>

            <!-- Top Products -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span>🏆</span>
                    <span>Produk Terlaris</span>
                </h2>
                <div class="space-y-3">
                    @forelse($topProducts as $index => $item)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900 transition">
                            <div class="text-2xl font-bold text-gray-400 dark:text-gray-600 w-8">
                                #{{ $index + 1 }}
                            </div>
                            <img src="{{ $item->product?->thumbnail ?: '/images/product-placeholder.svg' }}" 
                                 alt="{{ $item->product?->name }}"
                                 class="w-12 h-12 rounded-lg object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white truncate">
                                    {{ $item->product?->name ?? 'Produk Terhapus' }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($item->total_qty) }} terjual
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Belum ada data produk</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Cities -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span>🌆</span>
                    <span>Kota Terbanyak</span>
                </h2>
                <div class="space-y-3">
                    @forelse($topCities as $city)
                        @php
                            $maxOrders = $topCities->first()->total ?? 1;
                            $percentage = $maxOrders > 0 ? ($city->total / $maxOrders) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $city->shipping_city ?? 'Ambil Sendiri' }}</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $city->total }} pesanan</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Belum ada data kota</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span>🕐</span>
                    <span>Pesanan Terbaru</span>
                </h2>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white truncate">
                                    {{ $order->recipient_name ?? $order->user?->name ?? 'Guest' }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </div>
                                <div class="text-xs">
                                    @php
                                        $statusMeta = [
                                            'pending' => ['label' => 'Menunggu', 'class' => 'text-yellow-600 dark:text-yellow-400'],
                                            'processing' => ['label' => 'Diproses', 'class' => 'text-blue-600 dark:text-blue-400'],
                                            'completed' => ['label' => 'Selesai', 'class' => 'text-green-600 dark:text-green-400'],
                                            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'text-red-600 dark:text-red-400'],
                                        ];
                                        $meta = $statusMeta[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'text-gray-600 dark:text-gray-300'];
                                    @endphp
                                    <span class="{{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Belum ada pesanan</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
