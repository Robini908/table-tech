<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class ProductManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $category = '';
    public $categories;
    public $status = '';
    public $price_range_min = 0;
    public $price_range_max = 1000;
    public $productId;
    public $productName, $category_id, $description, $quantity, $cost_price, $selling_price, $status_select, $productImages = [];
    public $deleteConfirmation = false;
    public $isOpen = false; // Add the $isOpen property to manage modal visibility

    protected $rules = [
        'productName' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable|string|max:1000',
        'quantity' => 'required|integer|min:0',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'status_select' => 'required|in:active,inactive',
        'productImages.*' => 'nullable|image|max:1024', // Optional images, maximum size 1MB each
    ];

    public function mount()
{
    $this->categories = Category::all(); // Fetch categories
    $this->status = '';  // Default empty status filter
    $this->category = '';  // Default empty category filter
    $this->price_range_min = 0;  // Default price min
    $this->price_range_max = 1000; // Default price max
}




    public function addProduct()
    {
        $this->isOpen = true;  // Show the form when adding a new product
    }

    public function cancel()
    {
        $this->isOpen = false;  // Hide the form when cancelling
    }

    public function render()
{
    $products = Product::query()
        ->when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->when($this->category, function ($query) {
            return $query->where('category_id', $this->category);
        })
        ->when($this->status, function ($query) {
            return $query->where('status', $this->status);
        })
        ->whereBetween('selling_price', [$this->price_range_min, $this->price_range_max])
        ->paginate(10); // Ensure pagination is applied correctly

    return view('livewire.product-management', compact('products'));
}


    public function saveProduct()
    {
        $this->validate();

        $product = Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'name' => $this->productName,
                'category_id' => $this->category_id,
                'description' => $this->description,
                'quantity' => $this->quantity,
                'cost_price' => $this->cost_price,
                'selling_price' => $this->selling_price,
                'status' => $this->status_select,
            ]
        );

        if ($this->productImages) {
            foreach ($this->productImages as $image) {
                $product->addMedia($image->getRealPath())->toMediaCollection('product_images');
            }
        }

        session()->flash('message', $this->productId ? 'Product updated successfully.' : 'Product added successfully!');
        $this->resetForm();
        $this->isOpen = false; // Close the modal after saving
    }

    public function editProduct($productId)
    {
        $product = Product::findOrFail($productId);

        $this->productId = $product->id;
        $this->productName = $product->name;
        $this->category_id = $product->category_id;
        $this->description = $product->description;
        $this->quantity = $product->quantity;
        $this->cost_price = $product->cost_price;
        $this->selling_price = $product->selling_price;
        $this->status_select = $product->status;
        $this->isOpen = true; // Open the modal when editing a product
    }

    public function deleteProduct($productId)
    {
        $this->deleteConfirmation = true;
        $this->productId = $productId;
    }

    public function confirmDelete()
    {
        Product::find($this->productId)->delete();
        session()->flash('message', 'Product deleted successfully!');
        $this->deleteConfirmation = false;
    }

    public function cancelDelete()
    {
        $this->deleteConfirmation = false;
    }

    public function resetForm()
    {
        $this->productId = null;
        $this->productName = '';
        $this->category_id = '';
        $this->description = '';
        $this->quantity = '';
        $this->cost_price = '';
        $this->selling_price = '';
        $this->status_select = 'active';
        $this->productImages = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }
}
