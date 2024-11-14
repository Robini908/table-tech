<div class="container mt-4">
    <h3 class="text-3xl font-semibold text-gray-800 mb-6">Product and Category Management</h3>

    <!-- Categories Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-xl font-semibold">Categories</h4>
            <!-- Add Category Button with Plus Icon -->
            <x-button class="bg-gray-50 text-gray-700 hover:bg-gray-100 px-4 py-2 rounded-lg flex items-center space-x-2" wire:click="openCategoryModal">
                <i class="fas fa-plus"></i>
                <span>Add Category</span>
            </x-button>
        </div>

        <!-- If there are no categories -->
        @if($categories->isEmpty())
            <div class="bg-gray-50 p-4 rounded-lg text-center shadow-md">
                <i class="fas fa-exclamation-circle text-gray-600 mr-2"></i>
                No categories available. <span class="font-bold">Please add a category.</span>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($categories as $category)
                    <div class="bg-gray-50 p-4 rounded-lg shadow-md">
                        <div class="flex justify-between items-center">
                            <h5 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h5>
                            
                            <div class="flex space-x-1">
                                <button class="text-blue-500 hover:text-blue-600"wire:click="editCategory({{ $category->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-600" wire:click="deleteCategory({{ $category->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Products Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-xl font-semibold">Products</h4>
            <!-- Add Product Button with Plus Icon -->
            <x-button class="bg-gray-50 text-gray-700 hover:bg-gray-100 px-4 py-2 rounded-lg flex items-center space-x-2" wire:click="openProductModal">
                <i class="fas fa-plus"></i>
                <span>Add Product</span>
            </x-button>
        </div>

        <!-- If there are no products -->
        @if($products->isEmpty())
            <div class="bg-gray-50 p-4 rounded-lg text-center shadow-md">
                <i class="fas fa-exclamation-circle text-gray-600 mr-2"></i>
                No products available. <span class="font-bold">Please add a product.</span>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($products as $product)
                    <div class="bg-gray-50 p-4 rounded-lg shadow-md">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <h5 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h5>
                                <p class="text-sm text-gray-600">Price: ${{ $product->price }}</p>
                                <p class="text-sm text-gray-600">Category: {{ $product->category->name ?? 'None' }}</p>
                            </div>
                            <div class="flex space-x-1">
                                <button class="text-blue-500 hover:text-blue-600" wire:click="editProduct({{ $product->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-600" wire:click="deleteProduct({{ $product->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Product Modal -->
    <x-modal wire:model="isProductModalOpen">
        <x-slot name="title">Add/Edit Product</x-slot>

        <x-slot name="content">
            <form>
                <div class="form-group mb-4">
                    <x-label for="product_name" class="text-gray-700">Product Name</x-label>
                    <x-input id="product_name" type="text" class="form-control" wire:model="product_name" />
                    @error('product_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-4">
                    <x-label for="product_price" class="text-gray-700">Price</x-label>
                    <x-input id="product_price" type="text" class="form-control" wire:model="product_price" />
                    @error('product_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-4">
                    <x-label for="product_description" class="text-gray-700">Description</x-label>
                    <textarea id="product_description" class="form-control" wire:model="product_description"></textarea>
                    @error('product_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-4">
                    <x-label for="product_category" class="text-gray-700">Category</x-label>
                    <select id="product_category" class="form-control" wire:model="product_category">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('product_category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-button class="bg-gray-300 text-gray-700 hover:bg-gray-400 px-4 py-2 rounded-lg" wire:click="$set('isProductModalOpen', false)">Cancel</x-button>
            <x-button class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg" wire:click="saveProduct">Save</x-button>
        </x-slot>
        <div class="flex space-x-1">
                                <button class="text-blue-500 hover:text-blue-600" wire:click="editProduct({{ $product->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-600" wire:click="deleteProduct({{ $product->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
    </x-modal>

    <!-- Category Modal -->
    <x-modal wire:model="isCategoryModalOpen">
        <x-slot name="title">Add/Edit Category</x-slot>

        <x-slot name="content">
            <form>
                <div class="form-group mb-4">
                    <x-label for="category_name" class="text-gray-700">Category Name</x-label>
                    <x-input id="category_name" type="text" class="form-control" wire:model="category_name" />
                    @error('category_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-button class="bg-gray-300 text-gray-700 hover:bg-gray-400 px-4 py-2 rounded-lg" wire:click="$set('isCategoryModalOpen', false)">Cancel</x-button>
            <x-button class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg" wire:click="saveCategory">Save</x-button>
        </x-slot>
    </x-modal>
</div>
