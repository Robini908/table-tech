<div class="container mx-auto mt-5">
    @if(session()->has('message'))
        <div class="bg-green-500 text-white p-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Button to Add New Product -->
    <div class="flex justify-between mb-4">
        <button wire:click="$set('isOpen', true)" class="bg-blue-500 text-white p-2 rounded">Add New Product</button>
        
        <!-- Search and Filters -->
        <div class="flex space-x-4">
            <input wire:model="search" type="text" placeholder="Search by name..." class="p-2 border rounded w-1/4">
            
            <select wire:model="category" class="p-2 border rounded w-1/4">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model="status" class="p-2 border rounded w-1/4">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <input wire:model="price_range_min" type="number" placeholder="Min Price" class="p-2 border rounded w-1/4" min="0">
            <input wire:model="price_range_max" type="number" placeholder="Max Price" class="p-2 border rounded w-1/4" min="0">
        </div>
    </div>

    <!-- Show the Product Table only when 'isOpen' is false -->
    <div x-show="!isOpen" x-cloak>
        <table class="table-auto w-full border-collapse mb-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Product Name</th>
                    <th class="border p-2">Category</th>
                    <th class="border p-2">Quantity</th>
                    <th class="border p-2">Cost Price</th>
                    <th class="border p-2">Selling Price</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td class="border p-2">{{ $product->name }}</td>
                        <td class="border p-2">{{ $product->category->name }}</td>
                        <td class="border p-2">{{ $product->quantity }}</td>
                        <td class="border p-2">{{ $product->cost_price }}</td>
                        <td class="border p-2">{{ $product->selling_price }}</td>
                        <td class="border p-2">{{ ucfirst($product->status) }}</td>
                        <td class="border p-2">
                            <button wire:click="editProduct({{ $product->id }})" class="text-blue-500">Edit</button>
                            <button wire:click="deleteProduct({{ $product->id }})" class="text-red-500">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mb-4">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Product Form Card (Visible only when 'isOpen' is true) -->
    <div x-data="{ isOpen: @entangle('isOpen') }" x-show="isOpen" x-cloak>
        <div class="bg-white p-5 rounded shadow-lg">
            <form wire:submit.prevent="saveProduct">
                <div class="mb-4">
                    <label for="productName" class="text-sm font-semibold">Product Name</label>
                    <input wire:model="productName" id="productName" type="text" class="p-2 border rounded w-full">
                    @error('productName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="category_id" class="text-sm font-semibold">Category</label>
                    <select wire:model="category_id" id="category_id" class="p-2 border rounded w-full">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="text-sm font-semibold">Description</label>
                    <textarea wire:model="description" id="description" class="p-2 border rounded w-full"></textarea>
                </div>

                <div class="mb-4">
                    <label for="quantity" class="text-sm font-semibold">Quantity</label>
                    <input wire:model="quantity" type="number" min="0" class="p-2 border rounded w-full">
                </div>

                <div class="mb-4">
                    <label for="cost_price" class="text-sm font-semibold">Cost Price</label>
                    <input wire:model="cost_price" type="number" min="0" class="p-2 border rounded w-full">
                </div>

                <div class="mb-4">
                    <label for="selling_price" class="text-sm font-semibold">Selling Price</label>
                    <input wire:model="selling_price" type="number" min="0" class="p-2 border rounded w-full">
                </div>

                <div class="mb-4">
                    <label for="status_select" class="text-sm font-semibold">Status</label>
                    <select wire:model="status_select" id="status_select" class="p-2 border rounded w-full">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="productImages" class="text-sm font-semibold">Images</label>
                    <input type="file" wire:model="productImages" multiple class="p-2 border rounded w-full">
                    @error('productImages.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between mt-4">
                    <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Save Product</button>
                    <button type="button" @click="isOpen = false" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
