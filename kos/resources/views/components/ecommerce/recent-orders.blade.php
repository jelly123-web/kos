@props(['products' => []])

@php
    $defaultProducts = [
        [
            'name' => 'Macbook pro 13"',
            'variants' => 2,
            'image' => '/images/product/product-01.jpg',
            'category' => 'Laptop',
            'price' => '$2399.00',
            'status' => 'Delivered',
        ],
        [
            'name' => 'Apple Watch Ultra',
            'variants' => 1,
            'image' => '/images/product/product-02.jpg',
            'category' => 'Watch',
            'price' => '$879.00',
            'status' => 'Pending',
        ],
        [
            'name' => 'iPhone 15 Pro Max',
            'variants' => 2,
            'image' => '/images/product/product-03.jpg',
            'category' => 'SmartPhone',
            'price' => '$1869.00',
            'status' => 'Delivered',
        ],
        [
            'name' => 'iPad Pro 3rd Gen',
            'variants' => 2,
            'image' => '/images/product/product-04.jpg',
            'category' => 'Electronics',
            'price' => '$1699.00',
            'status' => 'Canceled',
        ],
        [
            'name' => 'Airpods Pro 2nd Gen',
            'variants' => 1,
            'image' => '/images/product/product-05.jpg',
            'category' => 'Accessories',
            'price' => '$240.00',
            'status' => 'Delivered',
        ],
    ];
    
    $productsList = !empty($products) ? $products : $defaultProducts;
    
    // Helper function for status classes
    $getStatusClasses = function($status) {
        $baseClasses = 'rounded-full px-3 py-1 text-xs font-medium';
        
        return match($status) {
            'Delivered' => $baseClasses . ' bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400',
            'Pending' => $baseClasses . ' bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
            'Canceled' => $baseClasses . ' bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400',
            default => $baseClasses . ' bg-slate-50 text-slate-600 dark:bg-slate-500/10 dark:text-slate-400',
        };
    };
@endphp

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white px-4 pb-3 pt-4 dark:border-slate-800 dark:bg-white/[0.03] sm:px-6 shadow-sm">
    <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Recent Orders</h3>
        </div>

        <div class="flex items-center gap-3">
            <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                <svg class="text-slate-400" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 6H17M5 10H15M8 14H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Filter
            </button>

            <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                See all
            </button>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="min-w-full">
            <thead>
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <th class="py-4 text-left">
                        <p class="font-semibold text-slate-500 text-xs uppercase tracking-wider dark:text-slate-400">Products</p>
                    </th>
                    <th class="py-4 text-left">
                        <p class="font-semibold text-slate-500 text-xs uppercase tracking-wider dark:text-slate-400">Category</p>
                    </th>
                    <th class="py-4 text-left">
                        <p class="font-semibold text-slate-500 text-xs uppercase tracking-wider dark:text-slate-400">Price</p>
                    </th>
                    <th class="py-4 text-left">
                        <p class="font-semibold text-slate-500 text-xs uppercase tracking-wider dark:text-slate-400">Status</p>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                @foreach($productsList as $product)
                <tr>
                    <td class="py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-lg overflow-hidden bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($product['name']) }}&background=EEF2FF&color=465FFF" alt="{{ $product['name'] }}" class="h-full w-full object-cover">
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-white text-sm">{{ $product['name'] }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $product['variants'] }} Variants</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4">
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $product['category'] }}</p>
                    </td>
                    <td class="py-4">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $product['price'] }}</p>
                    </td>
                    <td class="py-4">
                        <span class="{{ $getStatusClasses($product['status']) }}">
                            {{ $product['status'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>