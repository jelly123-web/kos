<div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden h-full">
    <div class="px-5 pb-8 pt-5 dark:bg-gray-900 sm:px-6 sm:pt-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white/90">
                    Target Pemasukan Bulanan
                </h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Progres realisasi pemasukan dari pembayaran penghuni
                </p>
            </div>
            <x-common.dropdown-menu />
        </div>
        <div class="relative mt-6 flex justify-center items-center">
            <div id="chartTwo" class="w-full max-w-[260px]"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center pt-10">
                <span id="mtg-percent" class="text-[36px] font-bold text-slate-800 dark:text-white">0%</span>
                <span id="mtg-diff" class="mt-2 rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-600 dark:bg-green-500/15 dark:text-green-500">0%</span>
            </div>
        </div>
        <p class="mx-auto mt-6 w-full max-w-[380px] text-center text-[15px] leading-relaxed text-slate-500 dark:text-slate-400">
            Realisasi pemasukan bulan ini
        </p>
    </div>

    <div class="flex items-center justify-center gap-2 px-4 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50">
        <div class="flex-1 text-center">
            <p class="mb-1 text-[13px] font-medium text-slate-500 dark:text-slate-400">Target</p>
            <div class="flex items-center justify-center gap-1 font-bold text-slate-800 dark:text-white text-lg">
                <span id="mtg-target">Rp 0</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M12 19L5 12M12 19L19 12" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="h-10 w-px bg-slate-200 dark:bg-slate-800"></div>

        <div class="flex-1 text-center">
            <p class="mb-1 text-[13px] font-medium text-slate-500 dark:text-slate-400">Revenue</p>
            <div class="flex items-center justify-center gap-1 font-bold text-slate-800 dark:text-white text-lg">
                <span id="mtg-revenue">Rp 0</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19V5M12 5L5 12M12 5L19 12" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="h-10 w-px bg-slate-200 dark:bg-slate-800"></div>

        <div class="flex-1 text-center">
            <p class="mb-1 text-[13px] font-medium text-slate-500 dark:text-slate-400">Today</p>
            <div class="flex items-center justify-center gap-1 font-bold text-slate-800 dark:text-white text-lg">
                <span id="mtg-today">Rp 0</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19V5M12 5L5 12M12 5L19 12" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const chartElement = document.querySelector('#chartTwo');
            try {
                const res = await fetch('{{ route('dashboard.metrics.summary') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const d = res.ok ? await res.json() : { month_target: 0, month_revenue: 0, progress_percent: 0, today_revenue: 0 };
                const nf = new Intl.NumberFormat('id-ID');
                const percent = d.progress_percent || 0;
                const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
                setText('mtg-target', 'Rp ' + nf.format(d.month_target || 0));
                setText('mtg-revenue', 'Rp ' + nf.format(d.month_revenue || 0));
                setText('mtg-today', 'Rp ' + nf.format(d.today_revenue || 0));
                setText('mtg-percent', percent + '%');
                const diffEl = document.getElementById('mtg-diff'); if (diffEl) diffEl.textContent = percent + '%';
                if (chartElement) {
                    const chartTwoOptions = {
                    series: [percent],
                    colors: ["#465FFF"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        type: "radialBar",
                        height: 260,
                        sparkline: {
                            enabled: true,
                        },
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            hollow: {
                                size: "80%",
                            },
                            track: {
                                background: "#E4E7EC",
                                strokeWidth: "100%",
                                margin: 5,
                            },
                            dataLabels: {
                                show: false,
                            },
                        },
                    },
                    fill: {
                        type: "solid",
                        colors: ["#465FFF"],
                    },
                    stroke: {
                        lineCap: "round",
                    },
                    labels: ["Progress"],
                    };

                    const chart = new ApexCharts(chartElement, chartTwoOptions);
                    chart.render();
                }
            } catch (e) {}
        });
    </script>
    @endpush
</div>
