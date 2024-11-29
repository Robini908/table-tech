 <!-- Resellable Items -->
 <div class="mb-6 p-6 bg-white rounded-lg shadow-lg border border-gray-200" wire:poll.1s>
    <!-- Section Header -->
    <h3 class="text-3xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-box-open mr-3 text-purple-500"></i> Resellable Items
    </h3>

    <!-- Informational Message -->
    @if (session()->has('message'))
        <p class="mb-4 text-red-600 font-semibold">{{ session('message') }}</p>
    @else
        <p class="mb-4 text-gray-600">Below is the list of products that are available for resale. Restock is
            recommended for low stock items to ensure continuous availability.</p>

        <!-- Additional Info for No Action Needed -->
        @foreach ($resellableItems as $item)
            @if (!$item['restock_needed'])
                <p class="text-green-600 mb-4 text-sm">No restocking needed for
                    <strong>{{ $item['product'] }}</strong> at the moment. Stock is sufficient, and the product
                    is in
                    good condition for resale.
                </p>
            @endif
        @endforeach

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm text-gray-700 border-separate border-spacing-2">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Quantity Available</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Expiry Date</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resellableItems as $item)
                        <tr class="border-b">
                            <td class="px-6 py-4 font-medium">{{ $item['product'] }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-lg font-semibold {{ $item['quantity'] <= 10 ? 'text-red-500' : 'text-green-600' }}">
                                    {{ $item['quantity'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 text-sm font-medium
                                {{ $item['quantity'] <= 10 ? 'bg-red-200 text-red-700' : 'bg-green-200 text-green-700' }} rounded-full">
                                    <i
                                        class="fas fa-{{ $item['quantity'] <= 10 ? 'exclamation-circle' : 'check-circle' }} mr-2"></i>
                                    {{ $item['quantity'] <= 10 ? 'Low Stock' : 'Sufficient Stock' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $item['expiry'] }}</td>
                            <td class="px-6 py-4">
                                @if ($item['restock_needed'])
                                    <button
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">
                                        Restock Soon
                                    </button>
                                @else
                                    <button
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                        No Action Needed
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>