<?php

namespace App\Livewire;


use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Product;
use Livewire\Component;
use App\Models\ExcessDemand;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class StockManager extends Component
{

    use LivewireAlert;
    public $stocks = [];
    public $productName = null;

    public $sales = [];
    public $startDate;
    public $endDate;


    public $quantity;
    public $errorMessage;

    public $monthlyTrend = [];
    public $totalAmount;

    public $showSaleForm = false;
    public $isSaleButtonDisabled = false;

    public $price_per_unit;
    public $output_per_unit;
    public $available_servings;

    public $products;
    public $product;

    public $dateFilter;


    public $profitLoss = 0;
    public $excessDemandLogs = [];
    public $profitBreakdown = [];
    public $recentSales = [];
    public $resellableItems = [];
    public $newStockQuantity = 0;
    public $product_id;
    public $stock_id;
    public $editingStock = false;

    protected $listeners = ['stockUpdated'];

    protected $rules = [
        'quantity' => 'required|integer|min:1|max:10000',  // Ensures quantity is at least 1 and no more than 10,000
        'price_per_unit' => 'required|numeric|min:0.01',   // Ensures price is a positive number greater than 0
    ];


    public function mount()
    {
        // Fetch products without stock
        $this->loadProducts();
        $this->stocks = Stock::with('product')->get();
        $this->sales = Sale::whereDate('created_at', Carbon::today())->get();
        $this->dateFilter = Carbon::today()->toDateString();

        $this->price_per_unit = 0;
        $this->available_servings = 0;
    }


    public function loadProducts()
    {
        if ($this->editingStock) {
            // Load all products during editing
            $this->products = Product::all();
        } else {
            // Load only products without stock when adding
            $this->products = Product::whereDoesntHave('stocks')->get();
        }
    }

    public function filterStockByDate()
    {
        $this->validate([
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ]);

        // If no date range is selected, just fetch all stocks
        if (!$this->startDate && !$this->endDate) {
            $this->stocks = Stock::all();
            return;
        }

        // Filter stocks by date range
        $this->stocks = Stock::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();
    }


    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->stocks = Stock::all();  // Or you can leave it to show all stocks without filtering
    }




    public function render()
    {
        $stockedProductIds = Stock::pluck('product_id')->toArray();

        return view('livewire.stock-manager', [
            'products' => Product::whereNotIn('id', $stockedProductIds)->get(),
            'stocks' => $this->stocks,
            'sales' => $this->sales,

        ]);
    }


    protected function hasSufficientCapacity($newQuantity)
    {
        $currentStock = Stock::sum('quantity');
        $maxCapacity = 10000; // Example max capacity for the warehouse.
        return ($currentStock + $newQuantity) <= $maxCapacity;
    }

    protected function isConsistentPrice($productId, $pricePerUnit)
    {
        $existingStock = Stock::where('product_id', $productId)->latest()->first();
        return !$existingStock || $existingStock->price_per_unit === $pricePerUnit;
    }

    protected function isValidStockQuantity($quantity)
    {
        return $quantity > 0;
    }

    protected function isDuplicateStock($productId)
    {
        return Stock::where('product_id', $productId)->exists();
    }


    
    public function addStock()
{
    $product = Product::find($this->product_id);
    $this->price_per_unit = $product ? $product->price : 0;
    $productName = $product ? $product->name : 'Product'; // Default to 'Product' if not found

    // Check if stock already exists for the product
    if ($this->isDuplicateStock($this->product_id)) {
        $this->alert('error', 'Stock for "' . $productName . '" already exists.', [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
        return;
    }

    // Validate stock quantity is greater than zero
    if (!$this->isValidStockQuantity($this->newStockQuantity)) {
        $this->alert('error', 'Stock quantity for "' . $productName . '" must be greater than zero.', [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
        return;
    }

    // Ensure price consistency with previous stock entries
    if (!$this->isConsistentPrice($this->product_id, $this->price_per_unit)) {
        $this->alert('error', 'Price per unit for "' . $productName . '" is inconsistent with previous stock entries.', [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
        return;
    }

    // Check if the addition exceeds storage capacity
    if (!$this->hasSufficientCapacity($this->newStockQuantity)) {
        $this->alert('error', 'Adding stock for "' . $productName . '" exceeds storage capacity.', [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
        return;
    }

    // Validate input fields
    $validated = $this->validate([
        'product_id' => 'required|exists:products,id',
        'newStockQuantity' => 'required|numeric|min:1',
        'price_per_unit' => 'required|numeric|min:0',
        'output_per_unit' => 'required|numeric|min:1',
    ]);

    // Save stock entry with error handling
    try {
        $stock = Stock::create([
            'product_id' => $this->product_id,
            'quantity' => $this->newStockQuantity,
            'price_per_unit' => $this->price_per_unit,
            'output_per_unit' => $this->output_per_unit,
            'available_servings' => $this->newStockQuantity * $this->output_per_unit,
        ]);

        // Reset fields and update stock status
        $this->resetInputFields();
        $this->updateStockStatus();

        // Success notification
        $this->alert('success', 'Stock for "' . $productName . '" added successfully!', [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
    } catch (QueryException $e) {
        Log::error('Database error while adding stock: ' . $e->getMessage());
        $this->alert('error', 'An error occurred while adding stock: ' . $e->getMessage(), [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
    } catch (\Exception $e) {
        Log::error('Error while adding stock: ' . $e->getMessage());
        $this->alert('error', 'An error occurred: ' . $e->getMessage(), [
            'position' => 'center',
            'timer' => 30000,
            'toast' => false,
            'showConfirmButton' => true,
        ]);
    }
}



    

    public function deleteStock($stockId)
    {
        Stock::find($stockId)->delete();
        $this->updateStockStatus();
        $this->alert('success', 'Stock deleted successfully!');
    }

    public function updateStockStatus()
    {
        $this->stocks = Stock::with('product')->get();
        $this->dispatch('$refresh'); // Notify the frontend to re-render
    }



    public function recordSale($stockId)
    {
        try {
            $stock = Stock::find($stockId);

            if ($stock) {
                $this->stock_id = $stock->id;
                $this->product_id = $stock->product_id;
                $this->product = $stock->product;
                $this->price_per_unit = $stock->price_per_unit;
                $this->quantity = 1;  // Default quantity
                $this->showSaleForm = true;
                $this->calculateTotalAmount();  // Dynamically calculate the total amount
            } else {
                throw new \Exception('Stock not found.');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error: ' . $e->getMessage(), ['toast' => false]);
        }
    }

    public function createSale()
    {
        try {
            $this->validate();

            // Check if quantity to be sold exceeds available stock
            $stock = Stock::find($this->stock_id);
            if ($this->quantity > $stock->quantity) {
                $this->alert('error', 'Sale quantity exceeds available stock!', ['toast' => true]);
                return; // Exit if stock is insufficient
            }

            // Dynamically calculate total price before saving
            $this->calculateTotalAmount();

            // Create Sale Record with stock_id
            $sale = Sale::create([
                'stock_id' => $this->stock_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'price_per_unit' => $this->price_per_unit,
                'total_price' => $this->totalAmount,
            ]);

            // Deduct stock after sale
            $this->deductStock($sale);

            // Reset form after successful sale
            $this->resetForm();

            // Hide form after successful sale
            $this->showSaleForm = false;

            // Success message
            $this->alert('success', 'Sale successfully recorded!', ['toast' => true]);
        } catch (\Exception $e) {
            // Handle database or other errors
            $this->alert('error', 'Error: ' . $e->getMessage(), ['toast' => false]);
        }
    }

    public function updated($propertyName)
    {
        // Dynamically calculate totalAmount
        if ($propertyName === 'quantity' || $propertyName === 'price_per_unit') {
            $this->calculateTotalAmount();
        }
    }

    public function calculateTotalAmount()
    {
        if ($this->quantity && $this->price_per_unit) {
            $this->totalAmount = $this->quantity * $this->price_per_unit;
        } else {
            $this->totalAmount = 0;
        }
    }

    public function resetForm()
    {
        // Reset all fields to their initial state
        $this->stock_id = $this->product_id = $this->quantity = $this->price_per_unit = $this->totalAmount = null;
        $this->product = null; // Reset the product selection
        $this->showSaleForm = false; // Hide the form after reset
    }

    public function deductStock($sale)
    {
        try {
            $stock = Stock::find($sale->stock_id);

            if ($stock && $sale->quantity <= $stock->quantity) {
                // Deduct stock and update available servings
                $stock->quantity -= $sale->quantity;
                $stock->available_servings -= $sale->quantity * $stock->output_per_unit;
                $stock->save();

                // Disable sales if stock is depleted
                if ($stock->quantity == 0) {
                    $this->alert('warning', 'Stock is fully depleted! Needs restocking.', ['toast' => true]);
                    // Disable the sale button if stock is depleted
                    $this->isSaleButtonDisabled = true;
                }
            } else {
                // Handle excess demand logic
                $excessQuantity = $sale->quantity - $stock->quantity;

                ExcessDemand::create([
                    'product_id' => $sale->product_id,
                    'sale_id' => $sale->id,
                    'requested_quantity' => $sale->quantity,
                    'available_servings' => $stock->available_servings,
                    'excess_quantity' => $excessQuantity,
                ]);

                // Update stock to zero when it's exhausted
                $stock->quantity = 0;
                $stock->available_servings = 0;
                $stock->save();
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error deducting stock: ' . $e->getMessage(), ['toast' => false]);
        }
    }








    public function filterSalesByDate($date)
    {
        $this->dateFilter = $date;
        $this->sales = Sale::whereDate('created_at', $date)->get();
        $this->updateStockStatus();
        $this->calculateProfitLoss();
        $this->trackExcessDemand();
    }

    public function stockUpdated()
    {
        $this->stocks = Stock::with('product')->get();
        $this->updateStockStatus();
    }


    public function resetInputFields()
    {
        $this->product_id = null;
        $this->newStockQuantity = null;
        $this->price_per_unit = null;
        $this->output_per_unit = null;
        $this->available_servings = null;
        $this->editingStock = false;
    }
}
