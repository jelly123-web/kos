<div class="rounded-2xl border border-slate-200 bg-white px-5 pb-5 pt-5 dark:border-slate-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 shadow-sm">
    <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:justify-between items-start">
        <div class="w-full">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">
                Statistik Pemasukan (Lunas)
            </h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Total pemasukan dari pembayaran penghuni per bulan
            </p>
        </div>

        <div class="flex items-center w-full gap-3 sm:justify-end">
            <div x-data="{ selected: 'overview' }"
                class="inline-flex items-center gap-1 rounded-lg bg-slate-50 p-1 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                @php
                    $options = [
                        ['value' => 'overview', 'label' => 'Overview'],
                        ['value' => 'sales', 'label' => 'Sales'],
                        ['value' => 'revenue', 'label' => 'Revenue'],
                    ];
                @endphp

                @foreach ($options as $option)
                    <button @click="selected = '{{ $option['value'] }}'"
                        :class="selected === '{{ $option['value'] }}' ?
                            'bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-sm border border-slate-100 dark:border-slate-700' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                        class="px-4 py-1.5 text-sm font-medium rounded-md transition-all">
                        {{ $option['label'] }}
                    </button>
                @endforeach
            </div>

            <div x-data="{
                init() {
                    flatpickr(this.$refs.datepicker, {
                        mode: 'range',
                        static: true,
                        monthSelectorType: 'static',
                        dateFormat: 'M j',
                        defaultDate: [new Date(Date.now() - 6 * 24 * 60 * 60 * 1000), new Date()],
                    })
                }
            }" class="relative min-w-[160px]">
                <input x-ref="datepicker" 
                    class="h-10 w-full rounded-lg border border-slate-200 bg-white pl-10 pr-4 text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400" 
                    placeholder="Select dates" readonly />
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="text-slate-400" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66683 1.54199V3.00033H12.5835V2.29199C12.5835 1.54199 12.9193 1.54199 13.3335 1.54199C13.7477 1.54199 14.0835 1.87778 14.0835 2.29199V3.00033L15.4168 3.00033C16.5214 3.00033 17.4168 3.89576 17.4168 5.00033V15.8337C17.4168 16.9382 16.5214 17.8337 15.4168 17.8337H4.5835C3.47893 17.8337 2.5835 16.9382 2.5835 15.8337V5.00033C2.5835 3.89576 3.47893 3.00033 4.5835 3.00033L5.91683 3.00033V2.29199C5.91683 1.54199 6.25262 1.54199 6.66683 1.54199ZM4.0835 5.00033V6.75033H15.9168V5.00033C15.9168 4.72418 15.693 4.50033 15.4168 4.50033H13.3335H6.66683H4.5835C4.30735 4.50033 4.0835 4.72418 4.0835 5.00033ZM15.9168 8.25033H4.0835V15.8337C4.0835 16.1098 4.30735 16.3337 4.5835 16.3337H15.4168C15.693 16.3337 15.9168 16.1098 15.9168 15.8337V8.25033Z" fill="currentColor" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-full">
        <div id="chartThree" class="w-full h-56"></div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const chartElement = document.querySelector('#chartThree');
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
                const chartThreeOptions = {
                    series: [{ name: "Lunas", data: paid }],
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: "left",
                    },
                    colors: ["#10B981"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        height: 220,
                        type: "area",
                        toolbar: {
                            show: false,
                        },
                    },
                    fill: {
                        gradient: {
                            enabled: true,
                            opacityFrom: 0.55,
                            opacityTo: 0,
                        },
                    },
                    stroke: {
                        curve: "straight",
                        width: ["1.5", "1.5"],
                    },
                    markers: {
                        size: 0,
                    },
                    labels: {
                        show: false,
                        position: "top",
                    },
                    grid: {
                        xaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    tooltip: {
                        x: {
                            format: "dd MMM yyyy",
                        },
                    },
                    xaxis: {
                        type: "category",
                        categories: labels,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                        tooltip: false,
                    },
                    yaxis: {
                        labels: { formatter: function(val){ return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } },
                        title: {
                            style: {
                                fontSize: "0px",
                            },
                        },
                    },
                };

                const chart = new ApexCharts(chartElement, chartThreeOptions);
                chart.render();
            }
        });
    </script>
    @endpush
</div>
