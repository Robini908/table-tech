<div class="p-5 bg-gray-50">
    <!-- Messages -->
    <div class="mb-4">
        @foreach($messages as $message)
            <div class="alert alert-{{ $message['type'] }} p-3 mb-4 rounded bg-gray-100">
                {{ $message['text'] }}
            </div>
        @endforeach
    </div>

    <!-- Sales Form -->
    @if($showForm)
        <form wire:submit.prevent="saveSale" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-6 rounded shadow-md">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" wire:model="date" class="mt-1 p-2 border border-gray-300 rounded w-full" required>
                @error('date') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Item Name</label>
                <input type="text" wire:model="item_name" class="mt-1 p-2 border border-gray-300 rounded w-full" required>
                @error('item_name') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Stock</label>
                <select wire:model="stock_id" class="mt-1 p-2 border border-gray-300 rounded w-full" required>
                    <option value="">Select Stock</option>
                    @foreach($stocks as $stock)
                        <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                    @endforeach
                </select>
                @error('stock_id') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Quantity Sold</label>
                <input type="number" wire:model="quantity_sold" class="mt-1 p-2 border border-gray-300 rounded w-full" required min="1" wire:change="calculateTotals">
                @error('quantity_sold') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                <input type="text" wire:model="total_amount" class="mt-1 p-2 border border-gray-300 rounded w-full" readonly>
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Profit</label>
                <input type="text" wire:model="profit" class="mt-1 p-2 border border-gray-300 rounded w-full" readonly>
            </div>

            <div class="form-group col-span-1 md:col-span-3">
                <label class="flex items-center mt-3">
                    <input type="checkbox" wire:model="is_returned" class="mr-2">
                    Item Returned
                </label>
            </div>

            <div class="form-group col-span-1 md:col-span-3">
                <button type="submit" class="mt-3 bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                    @if($isEditing) Update Sale @else Add Sale @endif
                </button>
                <button type="button" class="mt-3 bg-gray-500 text-white p-2 rounded hover:bg-gray-600" wire:click="resetFields">Cancel</button>
            </div>
        </form>
    @endif

    <div class="mt-5">
        <button class="bg-green-500 text-white p-2 rounded hover:bg-green-600" wire:click="showCreateForm">Add New Sale</button>
    </div>

    <!-- Sales List -->
    @if(!$showForm && !$sales->isEmpty())
        <div class="mt-5 bg-white p-6 rounded shadow-md">
            <h2 class="text-lg font-bold">Sales Records</h2>
            <table class="min-w-full border-collapse border border-gray-200 mt-3">
                <thead>
                    <tr>
                        <th class="border border-gray-300 p-2">Date</th>
                        <th class="border border-gray-300 p-2">Item Name</th>
                        <th class="border border-gray-300 p-2">Stock</th>
                        <th class="border border-gray-300 p-2">Quantity Sold</th>
                        <th class="border border-gray-300 p-2">Total Amount</th>
                        <th class="border border-gray-300 p-2">Profit</th>
                        <th class="border border-gray-300 p-2">Returned</th>
                        <th class="border border-gray-300 p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td class="border border-gray-300 p-2">{{ $sale->date }}</td>
                            <td class="border border-gray-300 p-2">{{ $sale->item_name }}</td>
                            <td class="border border-gray-300 p-2">{{ $sale->stock->name }}</td>
                            <td class="border border-gray-300 p-2">{{ $sale->quantity_sold }}</td>
                            <td class="border border-gray-300 p-2">{{ $sale->total_amount }}</td>
                            <td class="border border-gray-300 p-2">{{ $sale->profit }}</td>
                            <td class="border border-gray-300 p-2">{{ $sale->is_returned ? 'Yes' : 'No' }}</td>
                            <td class="border border-gray-300 p-2">
                                <button wire:click="editSale({{ $sale->id }})" class="bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600">Edit</button>
                                <button wire:click="deleteSale({{ $sale->id }})" class="bg-red-500 text-white p-1 rounded hover:bg-red-600">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif(!$showForm && $sales->isEmpty())
        <div class="mt-5">
            <p class="text-gray-500">No sales records found. Please add a sale.</p>
        </div>
    @endif
</div>
