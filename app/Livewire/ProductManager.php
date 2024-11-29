<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

class ProductManager extends Component
{
    use WithFileUploads;
    use WithFilePond;

    public $products;
    public $name;
    public $product_id;
    public $price;
    public $description;
    public $category_id;
    public $categories;
    public $productImages = [];
    public $isCreating = false;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'productImages.*' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->products = Product::with('category')->get();
        $this->categories = Category::all();
    }

    public function render()
    {
        return view('livewire.product-manager', [
            'products' => $this->products,
            'categories' => $this->categories,
        ]);
    }

    public function toggleCreate()
    {
        $this->resetForm();
        $this->isCreating = true;
        $this->isEditing = false;
    }

    public function toggleEdit($id)
    {
        $this->resetForm();
        $product = Product::findOrFail($id);
        $this->product_id = $product->id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->isEditing = true;
        $this->isCreating = false;
    }

    public function resetForm()
    {
        $this->reset(['name', 'price', 'description', 'category_id', 'productImages', 'product_id']);
    }

    public function saveProduct()
    {
        $this->validate();

        $product = Product::updateOrCreate(
            ['id' => $this->product_id],
            [
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'category_id' => $this->category_id,
            ]
        );

        // Handle product images
        if (!empty($this->productImages)) {
            foreach ($this->productImages as $image) {
                $product->addMedia($image->getRealPath())->toMediaCollection('images');
            }
        }

        $this->resetForm();
        $this->isCreating = false;
        $this->isEditing = false;

        $this->fetchData(); // Refresh product list
    }


    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        $this->fetchData();
    }

    public function cancel()
    {
        $this->resetForm();
        $this->isCreating = false;
        $this->isEditing = false;
    }
}
