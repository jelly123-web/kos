@extends('layouts.app')

@section('content')
  @php $isTenant = auth()->user()?->role === 'tenant'; @endphp
  @if($isTenant)
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <p class="text-gray-500 text-sm">Nomor Kamar</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $tenant?->room?->number ?? '-' }}</p>
          <p class="text-gray-500 text-sm mt-1">Tipe: {{ $tenant?->room?->name ?? '-' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <p class="text-gray-500 text-sm">Status Sewa</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $leaseStatus ?? 'Aktif' }}</p>
          <p class="text-gray-500 text-sm mt-1">Sisa hari: {{ $daysLeft !== null ? $daysLeft.' hari' : '-' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <p class="text-gray-500 text-sm">Harga / Bulan</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
            @php $price = $tenant?->room?->price; @endphp
            {{ $price ? 'Rp '.number_format($price,0,',','.') : '-' }}
          </p>
          <p class="text-gray-500 text-sm mt-1">Mulai: {{ $tenant?->start_date ? \Illuminate\Support\Carbon::parse($tenant->start_date)->format('d M Y') : '-' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <p class="text-gray-500 text-sm">Tagihan Bulan Ini</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Rp {{ number_format($currentMonthDue ?? 0,0,',','.') }}</p>
          <p class="text-gray-500 text-sm mt-1">Tunggakan: Rp {{ number_format($overdue ?? 0,0,',','.') }}</p>
        </div>
      </div>
      <div class="col-span-12">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Keuangan Saya (6 Bulan)</h3>
          </div>
          <div id="tenantFinanceChart" class="h-60"></div>
        </div>
      </div>
    </div>
    @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const el = document.querySelector('#tenantFinanceChart');
        if (!el) return;
        const options = {
          series: [
            { name: 'Lunas', data: @json($paidSeries ?? []) },
            { name: 'Belum Lunas', data: @json($unpaidSeries ?? []) },
          ],
          colors: ['#10b981', '#ef4444'],
          chart: { type: 'bar', height: 260, toolbar: { show: false } },
          plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
          dataLabels: { enabled: false },
          xaxis: { categories: @json($labels ?? []) },
          yaxis: { labels: { formatter: function (val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } } },
          tooltip: { y: { formatter: function (val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } } },
          legend: { position: 'top', horizontalAlign: 'left' }
        };
        window.tenantFinanceChart = new ApexCharts(el, options);
        window.tenantFinanceChart.render();
      });
    </script>
    @endpush
  @else
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <x-ecommerce.ecommerce-metrics />
        <x-ecommerce.monthly-sale />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <x-ecommerce.monthly-target />
      </div>
      <div class="col-span-12">
        <x-ecommerce.statistics-chart />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <x-ecommerce.customer-demographic />
      </div>
    </div>
  @endif
@endsection
