/**
 * AGINDU - Temperature Monitoring & IoT Simulator
 * Chart.js lokal, Polling 5s cerdas (otomatis jeda saat tab di-minimize / tidak aktif)
 */

(function () {
    let tempChart = null;
    let pollTimer = null;
    let isSimulating = false;
    let simTimer = null;

    window.initTemperatureMonitoring = function (config) {
        const batchId = config.batchId;
        const tempMin = parseFloat(config.tempMin);
        const tempMax = parseFloat(config.tempMax);
        const getUrl = config.getUrl;
        const simUrl = config.simUrl;
        const csrfToken = config.csrfToken;

        const ctx = document.getElementById('temperatureChart');
        if (!ctx) return;

        // Inisialisasi Chart.js
        tempChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Suhu (°C)',
                        data: [],
                        borderColor: '#176b45',
                        backgroundColor: 'rgba(23, 107, 69, 0.08)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#176b45',
                        fill: true
                    },
                    {
                        label: 'Batas Atas Aman',
                        data: [],
                        borderColor: '#f3b544',
                        borderWidth: 1.5,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false
                    },
                    {
                        label: 'Batas Bawah Aman',
                        data: [],
                        borderColor: '#23865a',
                        borderWidth: 1.5,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        grid: {
                            color: '#e4ebe2'
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7b71',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8
                        }
                    },
                    y: {
                        grid: {
                            color: '#e4ebe2'
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7b71',
                            callback: function (val) {
                                return val + ' °C';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { size: 12, family: 'Inter, sans-serif' },
                            color: '#172820'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#172820',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 6
                    }
                }
            }
        });

        // Fungsi fetch data dari API
        function fetchTemperatureData() {
            fetch(getUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.logs) return;
                updateChartWithData(data.logs, tempMin, tempMax);
                if (data.latest) {
                    updateMetrics(data.latest, tempMin, tempMax);
                }
            })
            .catch(err => console.error('Gagal mengambil data suhu:', err));
        }

        // Perbarui chart
        function updateChartWithData(logs, min, max) {
            const labels = [];
            const temps = [];
            const upperLimits = [];
            const lowerLimits = [];
            const pointColors = [];

            logs.forEach(item => {
                const timeStr = item.recorded_at.split(' ')[1] ? item.recorded_at.split(' ')[1].substring(0, 5) : item.recorded_at;
                labels.push(timeStr);
                const tempVal = parseFloat(item.temperature);
                temps.push(tempVal);
                upperLimits.push(max);
                lowerLimits.push(min);

                // Warna titik indikator status
                if (item.status === 'danger' || item.is_anomaly == 1) {
                    pointColors.push('#b3402f');
                } else if (item.status === 'warning') {
                    pointColors.push('#f3b544');
                } else {
                    pointColors.push('#176b45');
                }
            });

            tempChart.data.labels = labels;
            tempChart.data.datasets[0].data = temps;
            tempChart.data.datasets[0].pointBackgroundColor = pointColors;
            tempChart.data.datasets[1].data = upperLimits;
            tempChart.data.datasets[2].data = lowerLimits;
            tempChart.update('none');
        }

        // Perbarui angka metrik di kartu
        function updateMetrics(latest, min, max) {
            const curValEl = document.getElementById('metricCurrentTemp');
            const statusBadgeEl = document.getElementById('metricStatusBadge');
            const humValEl = document.getElementById('metricHumidity');
            const timeValEl = document.getElementById('metricLastTime');

            const val = parseFloat(latest.temperature);
            if (curValEl) curValEl.innerText = val.toFixed(1) + ' °C';
            if (humValEl && latest.humidity) humValEl.innerText = parseFloat(latest.humidity).toFixed(0) + ' %';
            if (timeValEl) timeValEl.innerText = latest.recorded_at;

            if (statusBadgeEl) {
                if (latest.status === 'danger' || latest.is_anomaly == 1) {
                    statusBadgeEl.className = 'badge badge-danger';
                    statusBadgeEl.innerHTML = '<span class="badge-dot"></span> Kritis / Di Luar Batas';
                } else if (latest.status === 'warning') {
                    statusBadgeEl.className = 'badge badge-amber';
                    statusBadgeEl.innerHTML = '<span class="badge-dot"></span> Waspada';
                } else {
                    statusBadgeEl.className = 'badge badge-lime';
                    statusBadgeEl.innerHTML = '<span class="badge-dot"></span> Normal & Optimal';
                }
            }
        }

        // Pemuatan data perdana
        fetchTemperatureData();

        // --------------------------------------------------------------------
        // POLLING CERDAS (5 Detik, Jeda jika tab tidak aktif / minimized)
        // --------------------------------------------------------------------
        function startPolling() {
            if (pollTimer) clearInterval(pollTimer);
            pollTimer = setInterval(function () {
                if (!document.hidden) {
                    fetchTemperatureData();
                }
            }, 5000);
        }

        function stopPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopPolling();
            } else {
                fetchTemperatureData();
                startPolling();
            }
        });

        startPolling();

        // --------------------------------------------------------------------
        // SIMULASI IoT (Pemicu Browser)
        // --------------------------------------------------------------------
        const simBtn = document.getElementById('btnToggleSimulate');
        const simIndicator = document.getElementById('simIndicator');

        if (simBtn) {
            simBtn.addEventListener('click', function () {
                if (!isSimulating) {
                    // Mulai simulasi
                    isSimulating = true;
                    simBtn.classList.remove('btn-secondary');
                    simBtn.classList.add('btn-danger');
                    simBtn.innerHTML = `
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="6" y="4" width="4" height="16"></rect>
                            <rect x="14" y="4" width="4" height="16"></rect>
                        </svg>
                        <span>Hentikan Simulasi</span>
                    `;
                    if (simIndicator) simIndicator.style.display = 'inline-flex';

                    // Kirim pembacaan simulasi awal
                    triggerSimulatePost();

                    // Jadwalkan simulasi tiap 5 detik jika tab aktif
                    simTimer = setInterval(function () {
                        if (!document.hidden && isSimulating) {
                            triggerSimulatePost();
                        }
                    }, 5000);
                } else {
                    // Hentikan simulasi
                    isSimulating = false;
                    clearInterval(simTimer);
                    simTimer = null;
                    simBtn.classList.remove('btn-danger');
                    simBtn.classList.add('btn-secondary');
                    simBtn.innerHTML = `
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                        <span>Mulai Simulasi IoT</span>
                    `;
                    if (simIndicator) simIndicator.style.display = 'none';
                }
            });
        }

        function triggerSimulatePost() {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);

            fetch(simUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    fetchTemperatureData();
                }
            })
            .catch(err => console.error('Gagal mengirim simulasi sensor:', err));
        }
    };
})();
