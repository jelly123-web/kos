<div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden h-full">
    <div class="px-5 pb-8 pt-5 dark:bg-gray-900 sm:px-6 sm:pt-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white/90">
                    Monthly Target
                </h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Target you’ve set for each month
                </p>
            </div>
            <x-common.dropdown-menu />
        </div>
        <div class="relative mt-8 flex justify-center items-center">
            {{-- Chart --}}
            <div id="chartTwo" class="w-full max-w-[330px]"></div>
            
            {{-- Custom Labels inside chart --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center pt-10">
                <span class="text-[36px] font-bold text-slate-800 dark:text-white">75.55%</span>
                <span class="mt-2 rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-600 dark:bg-green-500/15 dark:text-green-500">+10%</span>
            </div>
        </div>
        <p class="mx-auto mt-6 w-full max-w-[380px] text-center text-[15px] leading-relaxed text-slate-500 dark:text-slate-400">
            You earn $3287 today, it's higher than last month. Keep up your good work!
        </p>
    </div>

    <div class="flex items-center justify-center gap-2 px-4 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50">
        <div class="flex-1 text-center">
            <p class="mb-1 text-[13px] font-medium text-slate-500 dark:text-slate-400">Target</p>
            <div class="flex items-center justify-center gap-1 font-bold text-slate-800 dark:text-white text-lg">
                $20K
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M12 19L5 12M12 19L19 12" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="h-10 w-px bg-slate-200 dark:bg-slate-800"></div>

        <div class="flex-1 text-center">
            <p class="mb-1 text-[13px] font-medium text-slate-500 dark:text-slate-400">Revenue</p>
            <div class="flex items-center justify-center gap-1 font-bold text-slate-800 dark:text-white text-lg">
                $20K
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19V5M12 5L5 12M12 5L19 12" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="h-10 w-px bg-slate-200 dark:bg-slate-800"></div>

        <div class="flex-1 text-center">
            <p class="mb-1 text-[13px] font-medium text-slate-500 dark:text-slate-400">Today</p>
            <div class="flex items-center justify-center gap-1 font-bold text-slate-800 dark:text-white text-lg">
                $20K
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19V5M12 5L5 12M12 5L19 12" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartElement = document.querySelector('#chartTwo');
            if (chartElement) {
                const chartTwoOptions = {
                    series: [75.55],
                    colors: ["#465FFF"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        type: "radialBar",
                        height: 330,
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
        });
    </script>
    @endpush
</div>
