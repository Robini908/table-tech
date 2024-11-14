<div x-data="{
    messages: @entangle('messages'),
    timeout: 3000,
    openEditModal: false,
    openAddModal: false
}">
    <x-alpine-messages />



    <!-- Add Stock Button -->
    <button @click="openAddModal = true" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        Add New Stock
    </button>

    <!-- Add/Edit Stock Modal -->
    <div x-show="openAddModal || openEditModal" @click.away="openAddModal = openEditModal = false"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/2">
            <form wire:submit.prevent="saveStock">
                <h2 class="text-lg font-bold mb-4" x-text="openEditModal ? 'Edit Stock' : 'Add New Stock'"></h2>

              

                <!-- Input Fields -->
                <div class="mb-4">
                    <input type="text" wire:model="name" placeholder="Name" class="w-full p-2 border rounded"
                        required />
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="number" wire:model="quantity" placeholder="Quantity" class="w-full p-2 border rounded"
                        required />
                    @error('quantity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="text" wire:model="unit" placeholder="Unit" class="w-full p-2 border rounded"
                        required />
                    @error('unit')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="number" wire:model="cost_price" placeholder="Cost Price"
                        class="w-full p-2 border rounded" required />
                    @error('cost_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="number" wire:model="selling_price" placeholder="Selling Price"
                        class="w-full p-2 border rounded" required />
                    @error('selling_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="date" wire:model="expiry_date" placeholder="Expiry Date"
                        class="w-full p-2 border rounded" />
                    @error('expiry_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="text" wire:model="batch_number" placeholder="Batch Number"
                        class="w-full p-2 border rounded" />
                    @error('batch_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                <button type="button" @click="openAddModal = openEditModal = false"
                    class="bg-red-500 text-white px-4 py-2 rounded ml-2">Cancel</button>
            </form>

        </div>
    </div>

    <!-- Stock Table -->
    <table class="min-w-full bg-white mt-4 border rounded shadow-md">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 border">Name</th>
                <th class="py-2 px-4 border">Quantity</th>
                <th class="py-2 px-4 border">Unit</th>
                <th class="py-2 px-4 border">Cost Price</th>
                <th class="py-2 px-4 border">Selling Price</th>
                <th class="py-2 px-4 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $stock)
                <tr>
                    <td class="py-2 px-4 border">{{ $stock->name }}</td>
                    <td class="py-2 px-4 border">{{ $stock->quantity }}</td>
                    <td class="py-2 px-4 border">{{ $stock->unit }}</td>
                    <td class="py-2 px-4 border">{{ $stock->cost_price }}</td>
                    <td class="py-2 px-4 border">{{ $stock->selling_price }}</td>
                    <td class="py-2 px-4 border">
                        <button @click="openEditModal = true" wire:click="editStock({{ $stock->id }})"
                            class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="deleteStock({{ $stock->id }})"
                            class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    {{-- {{ $stocks->links() }} --}}
</div>
