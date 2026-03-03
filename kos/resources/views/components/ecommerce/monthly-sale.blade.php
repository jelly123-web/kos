<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white px-5 pt-5 sm:px-6 sm:pt-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">
            Monthly Sales
        </h3>
        <x-common.dropdown-menu />
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar mt-4">
        <div id="chartOne" class="-ml-5 h-full min-w-[690px] pl-2 xl:min-w-full"></div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartElement = document.querySelector('#chartOne');
            if (chartElement) {
                const chartOneOptions = {
                    series: [{
                        name: "Sales",
                        data: [168, 385, 201, 298, 187, 195, 291, 110, 215, 390, 280, 112],
                    }, ],
                    colors: ["#465fff"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        type: "bar",
                        height: 180,
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "39%",
                            borderRadius: 5,
                            borderRadiusApplication: "end",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 4,
                        colors: ["transparent"],
                    },
                    xaxis: {
                        categories: [
                            "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                        ],
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
                        x: {
                            show: false,
                        },
                        y: {
                            formatter: function(val) {
                                return val;
                            },
                        },
                    },
                };

                const chart = new ApexCharts(chartElement, chartOneOptions);
                chart.render();
            }
        });
    </script>
    @endpush
</div>


