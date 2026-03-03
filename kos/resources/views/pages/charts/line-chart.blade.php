@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Line Chart'" :title="'Charts'" :subtitle="'Visualisasi tren data dalam bentuk garis'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tren Pendapatan Bulanan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Statistik pertumbuhan pendapatan tahun 2026</p>
                </div>
            </div>
            
            <div id="lineChartMain" class="h-[400px]"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartElement = document.querySelector('#lineChartMain');
            if (chartElement) {
                const options = {
                    series: [{
                        name: "Pendapatan",
                        data: [15, 25, 20, 35, 30, 45, 40, 55, 50, 65, 60, 75]
                    }],
                    chart: {
                        height: 400,
                        type: 'line',
                        fontFamily: 'Outfit, sans-serif',
                        zoom: { enabled: false },
                        toolbar: { show: false }
                    },
                    colors: ['#465FFF'],
                    dataLabels: { enabled: false },
                    stroke: {
                        width: 4,
                        curve: 'smooth'
                    },
                    grid: {
                        borderColor: '#F1F5F9',
                        row: { opacity: 0.5 }
                    },
                    markers: {
                        size: 6,
                        colors: ['#465FFF'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: { size: 8 }
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        title: { text: 'Juta Rupiah (Rp)' }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return "Rp " + val + " Juta"
                            }
                        }
                    }
                };

                const chart = new ApexCharts(chartElement, options);
                chart.render();
            }
        });
    </script>
    @endpush
@endsection
