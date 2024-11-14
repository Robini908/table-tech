<?php

namespace App\Livewire;


use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\ExcessDemand;

class StockManagement extends Component
{

    use LivewireAlert;
    public $stocks = [];
    public $sales = [];

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
        $this->products = Product::all();
        $this->stocks = Stock::with('product')->get();
        $this->sales = Sale::whereDate('created_at', Carbon::today())->get();
        $this->dateFilter = Carbon::today()->toDateString();
        $this->updateStockStatus();
        $this->calculateProfitLoss();
        $this->trackExcessDemand();
        $this->trackResellableItems();
    }

    public function render()
    {
        return view('livewire.stock-management');
    }




    public function addStock()
    {
        $validated = $this->validate([
            'product_id' => 'required|exists:products,id',
            'newStockQuantity' => 'required|numeric|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'output_per_unit' => 'required|numeric|min:1',
        ]);

        Stock::create([
            'product_id' => $this->product_id,
            'quantity' => $this->newStockQuantity,
            'price_per_unit' => $this->price_per_unit,
            'output_per_unit' => $this->output_per_unit,
            'available_servings' => $this->newStockQuantity * $this->output_per_unit,
        ]);

        $this->resetInputFields();
        $this->updateStockStatus();
        session()->flash('message', 'Stock added successfully!');
    }

    public function editStock($stockId)
    {
        $stock = Stock::find($stockId);
        $this->stock_id = $stock->id;
        $this->product_id = $stock->product_id;
        $this->newStockQuantity = $stock->quantity;
        $this->price_per_unit = $stock->price_per_unit;
        $this->output_per_unit = $stock->output_per_unit;
        $this->available_servings = $stock->available_servings;
        $this->editingStock = true;
    }

    public function updateStock()
    {
        $validated = $this->validate([
            'newStockQuantity' => 'required|numeric|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'output_per_unit' => 'required|numeric|min:1',
        ]);

        $stock = Stock::find($this->stock_id);
        $stock->update([
            'quantity' => $this->newStockQuantity,
            'price_per_unit' => $this->price_per_unit,
            'output_per_unit' => $this->output_per_unit,
            'available_servings' => $this->newStockQuantity * $this->output_per_unit,
        ]);

        $this->resetInputFields();
        $this->editingStock = false;
        $this->updateStockStatus();
        session()->flash('message', 'Stock updated successfully!');
    }
    public function deleteStock($stockId)
    {
        Stock::find($stockId)->delete();
        $this->updateStockStatus();
        session()->flash('message', 'Stock deleted successfully!');
    }

    public function updateStockStatus()
    {
        $this->stocks = Stock::with('product')->get();
    }

    public function calculateProfitLoss()
    {
        $this->profitLoss = 0;  // Total profit/loss
        $this->profitBreakdown = [];  // Detailed profit breakdown by product
        $this->recentSales = [];  // Recent sales data
        $monthlyData = [];  // Monthly data aggregation
    
        foreach ($this->sales as $sale) {
            // Check if sale has a related product
            $product = $sale->product;  // Access product via the relationship
    
            if ($product) {
                // Find the corresponding stock for the product
                $stock = Stock::where('product_id', $product->id)->first();
    
                if ($stock) {
                    // Calculate profit for the product sold
                    $profit = $sale->quantity * ($sale->price_per_unit - $product->price);
                    $this->profitLoss += $profit;
    
                    // Profit Breakdown for each product
                    $this->profitBreakdown[] = [
                        'name' => $product->name,
                        'profit' => number_format($profit, 2),
                    ];
    
                    // Recent Sales Data (Product, Amount)
                    $this->recentSales[] = [
                        'product_name' => $product->name,
                        'sale_amount' => number_format($profit, 2),
                    ];
    
                    // Monthly Profit Trend Data (Aggregating by Month)
                    $month = $sale->created_at->format('Y-m'); // Format as Year-Month
                    if (!isset($monthlyData[$month])) {
                        $monthlyData[$month] = 0;
                    }
                    $monthlyData[$month] += $profit;
                }
            }
        }
    
        // Sort monthly data by date
        ksort($monthlyData);
    
        // Prepare data for charts (optional: pass it to view or any other processing)
        $this->monthlyTrend = array_map(function ($month, $amount) {
            return ['month' => $month, 'amount' => $amount];
        }, array_keys($monthlyData), $monthlyData);
        
        // Limit the recentSales to 10 items here
        $this->recentSales = collect($this->recentSales)->take(10);
    
        // Display a logical alert for clarity
        $this->alert('info', 'Profit & Loss Calculation Completed', [
            'text' => 'The profit and loss has been successfully calculated for the recent sales, including a breakdown by product and monthly trend data.',
        ]);
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

    public function recordSale($stockId)
    {
        try {
            $stock = Stock::find($stockId);
    
            if ($stock) {
                $this->stock_id = $stock->id;
                $this->product_id = $stock->product_id;
                $this->product = $stock->product;
                $this->price_per_unit = $stock->price_per_unit;
                $this->quantity = 1;
                $this->showSaleForm = true;
                $this->calculateTotalAmount();
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
    



    // Reset form method


    // public function trackExcessDemand()
    // {
    //     try {
    //         $this->excessDemandLogs = [];

    //         // Check if there are sales to process
    //         if (!$this->sales || $this->sales->isEmpty()) {
    //             throw new \Exception("No sales data available to track excess demand.");
    //         }

    //         foreach ($this->sales as $sale) {
    //             // Access product directly from sale relationship
    //             $product = $sale->product; // Access related product

    //             if ($product) {
    //                 // Find the corresponding stock for the product
    //                 $stock = Stock::where('product_id', $product->id)->first();

    //                 if (!$stock) {
    //                     // If no stock data is found for the product, skip it
    //                     continue;
    //                 }

    //                 // Determine if there is excess demand
    //                 $excessDemand = $product->quantity_sold > $stock->quantity;

    //                 if ($excessDemand) {
    //                     // Log excess demand scenario
    //                     $this->excessDemandLogs[] = [
    //                         'product' => $product->name,
    //                         'requested' => $product->quantity_sold,
    //                         'available' => $stock->quantity,
    //                         'date' => $sale->created_at,
    //                         'excessDemand' => true, // Flag for excess demand
    //                     ];
    //                 } else {
    //                     // Log normal demand scenario
    //                     $this->excessDemandLogs[] = [
    //                         'product' => $product->name,
    //                         'requested' => $product->quantity_sold,
    //                         'available' => $stock->quantity,
    //                         'date' => $sale->created_at,
    //                         'excessDemand' => false, // No excess demand
    //                     ];
    //                 }
    //             }
    //         }

    //         // Optional: Sort logs by date, with the most recent first
    //         usort($this->excessDemandLogs, function ($a, $b) {
    //             return strtotime($b['date']) - strtotime($a['date']);
    //         });

    //         // If no excess demand logs found, handle the case
    //         if (empty($this->excessDemandLogs)) {
    //             throw new \Exception("No excess demand found in the sales data.");
    //         }
    //     } catch (\Exception $e) {
    //         // Handle errors and display messages to the user
    //         $this->errorMessage = $e->getMessage();
    //         $this->excessDemandLogs = [];
    //     }
    // }

    public function trackExcessDemand()
{
    try {
        $this->excessDemandLogs = [];

        if (!$this->sales || $this->sales->isEmpty()) {
            throw new \Exception("No sales data available to track excess demand.");
        }

        foreach ($this->sales as $sale) {
            $product = $sale->product;

            if ($product) {
                $stock = Stock::where('product_id', $product->id)->first();

                if (!$stock) {
                    continue;
                }

                $excessDemand = $product->quantity_sold > $stock->quantity;

                $this->excessDemandLogs[] = [
                    'product' => $product->name,
                    'sold' => $product->quantity_sold,  // Renamed 'requested' to 'sold'
                    'available' => $stock->quantity,
                    'date' => $sale->created_at,
                    'excessDemand' => $excessDemand,
                ];
            }
        }

        usort($this->excessDemandLogs, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        if (empty($this->excessDemandLogs)) {
            throw new \Exception("No excess demand found in the sales data.");
        }
    } catch (\Exception $e) {
        $this->errorMessage = $e->getMessage();
        $this->excessDemandLogs = [];
    }
}






    public function trackResellableItems()
    {
        $this->resellableItems = [];
    
        foreach ($this->stocks as $stock) {
            // Ensure stock quantity is non-negative and check product expiry
            if ($stock->quantity >= 0) {
                $status = $this->getStockStatus($stock);
                $restockNeeded = $this->checkRestockNeeded($stock);
    
                // Add products with positive stock, including additional data for restocking
                if ($stock->quantity > 0) {
                    $this->resellableItems[] = [
                        'product' => $stock->product->name,
                        'quantity' => $stock->quantity,
                        'status' => $status,
                        'restock_needed' => $restockNeeded,
                        'restock_date' => $restockNeeded ? $this->getRestockDate($stock) : null,
                        'expiry' => $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : 'N/A', // Handle expiry
                    ];
                }
            }
        }
    
        // Check if no resellable items are found and show a message
        if (empty($this->resellableItems)) {
            session()->flash('message', 'No resellable items available at the moment.');
        }
    }
    

    // Helper method to determine stock status
    private function getStockStatus($stock)
    {
        if ($stock->quantity == 0) {
            return 'No Stock';
        } elseif ($stock->quantity <= 10) {
            return 'Low Stock';
        } else {
            return 'Sufficient Stock';
        }
    }

    // Helper method to check if restocking is needed based on quantity
    private function checkRestockNeeded($stock)
    {
        return $stock->quantity <= 10;
    }

    // Helper method to calculate restock date (example: 5 days from today)
    private function getRestockDate($stock)
    {
        return now()->addDays(5)->format('Y-m-d'); // Placeholder for actual restock date logic
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
