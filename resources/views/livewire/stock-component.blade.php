<div x-data="{ showForm: @entangle('showForm'), messages: @entangle('messages'), timeout: 3000 }"
     class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800">
    <!-- Feedback Messages -->
    <x-alpine-messages />

    <!-- Add Stock Button -->
    <button @click="showForm = true" class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Add New Stock
    </button>

    <!-- Form for Creating/Editing Stock -->
    <template x-if="showForm">
        <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md">
            <form wire:submit.prevent="saveStock" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="block text-gray-700">Name</label>
                        <input type="text" wire:model="name" class="mt-1 p-2 border border-gray-300 rounded w-full" required>
                        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <!-- Additional form fields here... -->
                    <div class="form-group">
                        <label class="block text-gray-700">Image</label>
                        <input type="file" wire:model="image" class="mt-1 p-2 border border-gray-300 rounded w-full" accept="image/*">
                        @error('image') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-4 flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        <span x-text="$wire.isEditing ? 'Update Stock' : 'Add Stock'"></span>
                    </button>
                    <button type="button" @click="showForm = false" wire:click="resetFields"
                            class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </template>

    <!-- Stock List Display (Visible when showForm is false) -->
    <template x-if="!showForm">
        <div class="mt-4">
            <h3 class="text-xl font-bold">Stock List</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                @forelse ($stocks as $stock)
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <img src="{{ $stock->image ? Storage::url($stock->image) : asset('default_image.jpg') }}"
                             alt="{{ $stock->name }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h4 class="text-lg font-semibold">{{ $stock->name }}</h4>
                            <p class="text-gray-700">Quantity: {{ $stock->quantity }}</p>
                            <p class="text-gray-700">Unit: {{ $stock->unit }}</p>
                            <p class="text-gray-700">Cost Price: {{ number_format($stock->cost_price, 2) }}</p>
                            <p class="text-gray-700">Selling Price: {{ number_format($stock->selling_price, 2) }}</p>
                            <p class="text-gray-700">Expiry Date: {{ $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : 'N/A' }}</p>
                            <p class="text-gray-700">Supplier: {{ $stock->supplier->name }}</p>
                        </div>
                        <div class="flex justify-between p-4 border-t">
                            <button wire:click="editStock({{ $stock->id }})" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Edit
                            </button>
                            <button wire:click="deleteStock({{ $stock->id }})" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-4">No stocks found.</div>
                @endforelse
            </div>
        </div>
    </template>
</div>
