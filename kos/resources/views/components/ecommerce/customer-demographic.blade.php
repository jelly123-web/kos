@props(['countries' => []])

@php
    $defaultCountries = [
        [
            'name' => 'USA',
            'flag' => './images/country/country-01.svg',
            'customers' => '2,379',
            'percentage' => 79
        ],
        [
            'name' => 'France',
            'flag' => './images/country/country-02.svg',
            'customers' => '589',
            'percentage' => 23
        ],
    ];
    
    $countriesList = !empty($countries) ? $countries : $defaultCountries;
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] sm:p-6 shadow-sm">
    <div class="flex justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white/90">
                Customers Demographic
            </h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                Number of customer based on country
            </p>
        </div>
        <x-common.dropdown-menu />
    </div>

    <div class="my-6 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 px-4 py-6 dark:border-slate-800 dark:bg-gray-900 sm:px-6">
        <div id="mapOne" class="h-[212px] w-full"></div>
    </div>

    <div class="space-y-5">
        @foreach($countriesList as $country)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 flex items-center justify-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($country['name']) }}&background=EEF2FF&color=465FFF&size=32" alt="{{ $country['name'] }}" />
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white/90">
                            {{ $country['name'] }}
                        </p>
                        <span class="block text-xs text-slate-500 dark:text-gray-400">
                            {{ $country['customers'] }} Customers
                        </span>
                    </div>
                </div>

                <div class="flex w-full max-w-[140px] items-center gap-3">
                    <div class="relative block h-1.5 w-full max-w-[100px] rounded-full bg-slate-100 dark:bg-gray-800">
                        <div 
                            class="absolute left-0 top-0 h-full rounded-full bg-primary"
                            style="width: {{ $country['percentage'] }}%"
                        ></div>
                    </div>
                    <p class="text-sm font-bold text-slate-800 dark:text-white/90">
                        {{ $country['percentage'] }}%
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mapElement = document.querySelector('#mapOne');
            if (mapElement) {
                const mapOne = new jsVectorMap({
                    selector: "#mapOne",
                    map: "world",
                    zoomButtons: false,
                    regionStyle: {
                        initial: {
                            fontFamily: "Outfit",
                            fill: "#D9D9D9",
                        },
                        hover: {
                            fillOpacity: 1,
                            fill: "#465FFF",
                        },
                    },
                    markers: [
                        { name: "Egypt", coords: [26.8206, 30.8025] },
                        { name: "United Kingdom", coords: [55.3781, 3.436] },
                        { name: "United States", coords: [37.0902, -95.7129] },
                    ],
                    markerStyle: {
                        initial: {
                            strokeWidth: 1,
                            fill: "#465FFF",
                            fillOpacity: 1,
                            r: 4,
                        },
                        hover: {
                            fill: "#465FFF",
                            fillOpacity: 1,
                        },
                    },
                });
            }
        });
    </script>
    @endpush
</div>
