<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | {{ \App\Models\Setting::getValue('app_name', 'Kos Management System') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @php $primary = \App\Models\Setting::getValue('primary_color', '#465FFF'); @endphp
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $primary }}',
                        'bg-gray': '#F9FAFB',
                        brand: {
                            50: '#EEF2FF',
                            100: '#E0E7FF',
                            200: '#C7D2FE',
                            300: '#A5B4FC',
                            400: '#818CF8',
                            500: '{{ $primary }}',
                            600: '{{ $primary }}',
                            700: '#3142B2',
                            800: '#28358E',
                            900: '#212B74',
                            950: '#141A46',
                        },
                    },
                    boxShadow: {
                        'card': '0px 1px 3px rgba(0, 0, 0, 0.1), 0px 1px 2px rgba(0, 0, 0, 0.06)',
                    }
                },
            },
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- jsVectorMap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark', 'bg-gray-900');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark', 'bg-gray-900');
            }
        })();
    </script>
    
    <!-- Custom Styles -->
    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Sidebar Styles to match TailAdmin */
        .menu-item {
            @apply flex items-center gap-3 px-4 py-3 font-medium transition-all duration-200 rounded-lg;
        }
        .menu-item-inactive {
            @apply text-slate-600 hover:text-primary hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/5;
        }
        .menu-item-active {
            @apply text-primary bg-slate-50 dark:text-white dark:bg-white/5;
        }
        .menu-item-icon-inactive {
            @apply text-slate-400 group-hover:text-primary;
        }
        .menu-item-icon-active {
            @apply text-primary;
        }
        .menu-item-text {
            @apply text-[15px];
        }

        .menu-dropdown-item {
            @apply flex items-center px-4 py-2 text-[14px] font-medium transition-all duration-200 rounded-lg;
        }
        .menu-dropdown-item-inactive {
            @apply text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white;
        }
        .menu-dropdown-item-active {
            @apply text-primary dark:text-white;
        }

        .menu-dropdown-badge {
            @apply px-2 py-0.5 text-[10px] font-bold uppercase rounded-full;
        }
        .menu-dropdown-badge-inactive {
            @apply bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400;
        }
        .menu-dropdown-badge-active {
            @apply bg-primary text-white;
        }

        .menu-dropdown-badge-pro {
            @apply px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-brand-500 text-white;
        }
    </style>
</head>

<body
    class="bg-[#F9FAFB] dark:bg-slate-950 font-sans"
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('layouts.app-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>

    </div>

</body>

<script>
// capture geolocation once and send to backend
(function() {
    const sentKey = 'geo_sent_at';
    const last = localStorage.getItem(sentKey);
    const need = !last || (Date.now() - parseInt(last, 10)) > 3600*1000; // 1 hour
    if (!need) return;
    if (!('geolocation' in navigator)) return;
    navigator.geolocation.getCurrentPosition(function(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        fetch('{{ route('track.geo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
            },
            body: JSON.stringify({ lat, lng })
        }).then(() => {
            localStorage.setItem(sentKey, String(Date.now()));
        }).catch(() => {});
    }, function() {}, { enableHighAccuracy: false, maximumAge: 600000, timeout: 8000 });
})();
</script>

@stack('scripts')

</html>
