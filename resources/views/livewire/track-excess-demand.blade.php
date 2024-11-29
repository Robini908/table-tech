
<div class=" overflow-x-auto">
    
    <!-- Error Message -->
    @if ($errorMessage)
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-md shadow-md flex items-center">
            <i class="fas fa-exclamation-circle mr-2 text-xl"></i> {{ $errorMessage }}
        </div>
    @endif


    <div class="mb-6 p-6 bg-white rounded-lg shadow-md overflow-x-auto">
        <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle mr-3 text-yellow-500 text-2xl"></i> Excess Demand Logs
        </h3>

        <!-- Informative Message Section -->
        <div class="mb-6 p-4 bg-yellow-50 text-yellow-700 rounded-md shadow-md">
            <i class="fas fa-info-circle mr-2 text-2xl"></i>
            <strong>The system is facing unexpected surges in demand...</strong>
            <ul class="list-inside list-disc mt-2">
                <li>Stock Data Errors: Outdated or inaccurate inventory data.</li>
                <li>Surprise Spikes in Demand: Unexpected surges due to events or trends.</li>
                <li>Supplier Delays: Late reorders or supply chain disruptions.</li>
                <li>Large Customer Orders: Bulk or urgent requests exceeding stock.</li>
            </ul>
        </div>

        <!-- Error Message -->
        @if ($errorMessage)
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-md shadow-md flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xl"></i> {{ $errorMessage }}
            </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="mb-4 flex justify-between items-center">
            <input type="text" placeholder="Search Logs..."
                class="p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 text-lg"
                wire:model.live="searchTerm">
            <button
                class="px-6 py-3 bg-yellow-500 text-white rounded-md shadow-md hover:bg-yellow-600 transition duration-200 disabled:opacity-50"
                wire:click="downloadCSV" @disabled(empty($excessDemandLogs))>
                <i class="fas fa-download mr-2"></i> Download Logs
            </button>
        </div>

        <!-- Excess Demand Logs Table with Fixed Height and Scroll -->
        @if (!empty($excessDemandLogs))
            <div class="overflow-y-auto max-h-96">
                <table class="min-w-full table-auto border-collapse text-sm text-gray-700" wire:poll.1s>
                    <thead>
                        <tr class="bg-gray-100 text-lg">
                            <th class="px-6 py-3 text-left font-medium">Product</th>
                            <th class="px-6 py-3 text-left font-medium">Sold Quantity</th>
                            <th class="px-6 py-3 text-left font-medium">Available Quantity</th>
                            <th class="px-6 py-3 text-left font-medium">Date</th>
                            <th class="px-6 py-3 text-left font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($excessDemandLogs as $log)
                            <tr class="border-b hover:bg-gray-50 transition duration-200">
                                <td class="px-6 py-4">{{ $log['product'] }}</td>
                                <td class="px-6 py-4">{{ $log['sold'] }}</td>
                                <td class="px-6 py-4">{{ $log['available'] }}</td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($log['date'])->format('l, F jS, Y \a\t h:i A') }}</td>
                                <td class="px-6 py-4 flex items-center">
                                    @if ($log['excessDemand'])
                                        <span class="text-red-600 font-semibold">Excess Demand</span>
                                        <!-- Info icon with tooltip for Excess Demand -->
                                        <i class="fas fa-info-circle text-red-600 ml-2" data-bs-toggle="tooltip"
                                            title="Sold quantity exceeds available stock due to high demand."></i>
                                    @else
                                        <span class="text-green-600 font-semibold">Normal</span>
                                        <!-- Info icon with tooltip for Normal -->
                                        <i class="fas fa-info-circle text-green-600 ml-2" data-bs-toggle="tooltip"
                                            title="Stock levels are within normal range."></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 bg-gray-100 text-gray-700 rounded-md shadow-md">
                <i class="fas fa-info-circle mr-2 text-yellow-500"></i> No excess demand logs found.
            </div>
        @endif
    </div>
</div>
