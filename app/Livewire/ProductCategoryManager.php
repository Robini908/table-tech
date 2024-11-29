<?php


namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Database\QueryException;

use App\Models\Category;
use Jantinnerezo\LivewireAlert\LivewireAlert;  // Import LivewireAlert

class ProductCategoryManager extends Component
{
    use LivewireAlert;  // Use the LivewireAlert trait

    public $products;
    public $product;

    public $categories;
    public $product_id, $product_name, $product_price, $product_description, $product_category;
    public $category_id, $category_name;
    public $isProductModalOpen = false;
    public $isCategoryModalOpen = false;

    protected $rules = [
        'product_name' => 'required|string|max:255',
        'product_price' => 'required|numeric',
        'product_description' => 'nullable|string',
        'product_category' => 'required|exists:categories,id',  // Ensure the category exists
        'category_name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->fetchData();
        
    }

    public function fetchData()
    {
        $this->products = Product::with('stocks', 'sales', 'category')->get();
        $this->categories = Category::all();
    }

    public function openProductModal()
    {
        $this->reset(['product_id', 'product_name', 'product_price', 'product_description', 'product_category']);
        $this->isProductModalOpen = true;
    }

    public function closeProductModal()
    {
        $this->isProductModalOpen = false;
    }

    public function openCategoryModal()
    {
        $this->reset(['category_id', 'category_name']);
        $this->isCategoryModalOpen = true;
    }

    public function closeCategoryModal()
    {
        $this->isCategoryModalOpen = false;
    }

   

    public function saveProduct()
    {
        // Validate product input
        $this->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string|max:500',
            'product_category' => 'required|exists:categories,id', // Ensure the category is selected and exists
        ]);
    
        try {
            // Save or update the product
            Product::updateOrCreate(
                ['id' => $this->product_id],  // Check if the product exists
                [
                    'name' => $this->product_name,
                    'price' => $this->product_price,
                    'description' => $this->product_description,
                    'category_id' => $this->product_category,  // Ensure this is assigned to the correct category
                ]
            );
    
            $this->alert('success', 'Product saved successfully!', [
                'toast' => true, // Display as a toast alert
                'position' => 'top-end', // Position of the toast
                'timer' => 3000, // Duration of the toast
            ]);
    
            $this->isProductModalOpen = false;
            $this->fetchData();
        } catch (QueryException $e) {
            // Catch database query exceptions
            $this->alert('error', 'Database error occurred: ' . $e->getMessage(), [
                'toast' => false, // Display as a non-toast alert
            ]);
        } catch (\Exception $e) {
            // Catch any other exceptions
            $this->alert('error', 'An error occurred while saving the product. Please try again. ' . $e->getMessage(), [
                'toast' => false, // Display as a non-toast alert
            ]);
        }
    }
    
    

    public function saveCategory()
    {
        try {
            // Validate and save the category
            $this->validate(['category_name' => 'required|string|max:255']);

            Category::updateOrCreate(
                ['id' => $this->category_id],  // Check if category exists
                ['name' => $this->category_name]
            );

            $this->alert('success', 'Category saved successfully!', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 3000,
            ]);

            $this->isCategoryModalOpen = false;
            $this->fetchData();
        } catch (\Exception $e) {
            // In case of error, show an alert
            $this->alert('error', 'An error occurred while saving the category. Please try again.', [
                'toast' => false,
            ]);
        }
    }

    public function editProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->product_id = $product->id;
            $this->product_name = $product->name;
            $this->product_price = $product->price;
            $this->product_description = $product->description;
            $this->product_category = $product->category_id;
            $this->isProductModalOpen = true;
        } catch (\Exception $e) {
            $this->alert('error', 'Error loading product details. Please try again.', [
                'toast' => false,
            ]);
        }
    }

    public function editCategory($id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->category_id = $category->id;
            $this->category_name = $category->name;
            $this->isCategoryModalOpen = true;
        } catch (\Exception $e) {
            $this->alert('error', 'Error loading category details. Please try again.', [
                'toast' => false,
            ]);
        }
    }

    public function deleteProduct($id)
    {
        try {
            Product::find($id)->delete();
            $this->alert('success', 'Product deleted successfully!', [
                'toast' => true,
            ]);
            $this->fetchData();
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred while deleting the product. Please try again.', [
                'toast' => false,
            ]);
        }
    }

    public function deleteCategory($id)
    {
        try {
            Category::find($id)->delete();
            $this->alert('success', 'Category deleted successfully!', [
                'toast' => true,
            ]);
            $this->fetchData();
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred while deleting the category. Please try again.', [
                'toast' => false,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.product-category-manager');
    }
}
