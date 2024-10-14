<div x-data="{ messages: @entangle('messages'), timeout: 3000 }" class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800">
    <x-alpine-messages/>
    <button wire:click="showCreateForm" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition duration-200">
        Add Supplier
    </button>

    @if($showForm)
        <form wire:submit.prevent="saveSupplier" class="mt-6 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">{{ $isEditing ? 'Edit' : 'Create' }} Supplier</h2>

            <div class="mb-4">
                <label class="block mb-1">Name</label>
                <input type="text" wire:model="name" class="border rounded w-full p-2" placeholder="Enter supplier name">
                @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Contact Info</label>
                <input type="text" wire:model="contact_info" class="border rounded w-full p-2" placeholder="Enter contact info">
                @error('contact_info') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Address</label>
                <input type="text" wire:model="address" class="border rounded w-full p-2" placeholder="Enter address">
                @error('address') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition duration-200">
                    {{ $isEditing ? 'Update' : 'Create' }} Supplier
                </button>
                <button type="button" wire:click="resetFields" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition duration-200">
                    Cancel
                </button>
            </div>
        </form>
    @else
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($suppliers as $supplier)
                <div class="bg-white border rounded-lg p-4 shadow-md hover:shadow-lg transition duration-200">
                    <h3 class="text-lg font-semibold">{{ $supplier->name }}</h3>
                    <p class="text-gray-600">Contact: {{ $supplier->contact_info }}</p>
                    <p class="text-gray-600">Address: {{ $supplier->address }}</p>
                    <div class="mt-4 flex space-x-2">
                        <button wire:click="editSupplier({{ $supplier->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded shadow hover:bg-yellow-600 transition duration-200">
                            Edit
                        </button>
                        <button wire:click="deleteSupplier({{ $supplier->id }})" class="bg-red-500 text-white px-2 py-1 rounded shadow hover:bg-red-600 transition duration-200">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
