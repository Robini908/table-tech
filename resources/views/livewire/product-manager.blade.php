<div class="container mt-4">
    @if ($isCreating || $isEditing)
        <div class="container mx-auto mt-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-gray-700 mb-6">{{ $isEditing ? 'Edit Product' : 'Add Product' }}</h2>
                <form wire:submit.prevent="saveProduct" class="p-3 border rounded shadow-sm bg-light">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" wire:model="name" class="form-control">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                
                        <!-- Price -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" id="price" wire:model="price" step="0.01" class="form-control">
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                
                        <!-- Description -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" wire:model="description" rows="2" class="form-control"></textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                
                        <!-- Category -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id" class="form-label">Category</label>
                                <select id="category_id" wire:model="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                
                        <!-- Product Images -->
                        <div class="col-6d">
                            <div class="form-group">
                                <label for="productImages" class="form-label">Product Images</label>
                                <x-filepond::upload 
                                    wire:model="productImages" 
                                    max-files="5" 
                                    allow-multiple="true" 
                                    class="form-control" />
                                @error('productImages.*')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                
                        <!-- Action Buttons -->
                        <div class="col-12 text-end">
                            <button type="button" wire:click="cancel" class="btn btn-secondary btn-sm me-2">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    @else
        <div>
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 mb-0">Product List</h2>
                <button wire:click="toggleCreate" class="btn btn-success">Add Product</button>
            </div>

            <!-- No Products Found -->
            @if ($products->isEmpty())
                <div class="alert alert-warning text-center" role="alert">
                    <i class="fas fa-exclamation-circle fa-2x text-warning mb-2"></i>
                    <h5 class="mt-2">No products found!</h5>
                    <p>Click the "Add Product" button to create your first product.</p>
                </div>
            @else
                <!-- Product Cards -->
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <!-- Product Images Carousel -->
                                <div id="productCarousel{{ $product->id }}" class="carousel slide"
                                    data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @forelse ($product->getMedia('images') as $key => $media)
                                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                                <img src="{{ $media->getUrl() }}" class="d-block w-100"
                                                    alt="Product Image">
                                            </div>
                                        @empty
                                            <div class="carousel-item active">
                                                <img src="/images/default-placeholder.png" class="d-block w-100"
                                                    alt="No Image Available">
                                            </div>
                                        @endforelse
                                    </div>

                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <!-- Product Info -->
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-muted">Category: {{ $product->category->name ?? 'N/A' }}
                                    </p>
                                    <p class="card-text fw-bold">Price: ${{ number_format($product->price, 2) }}</p>
                                    <p class="card-text">{{ $product->description }}</p>
                                    <div class="d-flex justify-content-between">
                                        <button wire:click="toggleEdit({{ $product->id }})"
                                            class="btn btn-sm btn-primary">Edit</button>
                                        <button wire:click="deleteProduct({{ $product->id }})"
                                            class="btn btn-sm btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
