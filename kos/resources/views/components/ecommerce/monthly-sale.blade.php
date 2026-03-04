<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white px-5 pt-5 sm:px-6 sm:pt-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">
            Pembayaran Lunas Bulanan
        </h3>
        <x-common.dropdown-menu />
    </div>

    <div class="max-w-full mt-4">
        <div id="chartOne" class="h-44 w-full"></div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const chartElement = document.querySelector('#chartOne');
            if (chartElement) {
                let labels = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
                let paid = [];
                try {
                    const res = await fetch('{{ route('dashboard.charts.payments') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (res.ok) {
                        const data = await res.json();
                        labels = data.labels || labels;
                        paid = data.paid || [];
                    }
                } catch (e) {}
                const chartOneOptions = {
                    series: [{
                        name: "Lunas",
                        data: paid,
                    }, ],
                    colors: ["#465fff"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        type: "bar",
                        height: 140,
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "12%",
                            borderRadius: 3,
                            borderRadiusApplication: "end",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ["transparent"],
                    },
                    xaxis: {
                        categories: labels,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: "left",
                        fontFamily: "Outfit",
                        markers: {
                            radius: 99,
                        },
                    },
                    yaxis: {
                        title: false,
                    },
                    grid: {
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        x: { show: false },
                        y: { formatter: function(val){ return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } },
                    },
                };

                const chart = new ApexCharts(chartElement, chartOneOptions);
                chart.render();
            }
        });
    </script>
    @endpush
</div>
