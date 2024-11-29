<?php

namespace App\Livewire;


use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\ExcessDemand;

class ResallableItemsStock extends Component
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



    public function mount()
    {
        // Load products to ensure you can access them for price lookup
        $this->products = Product::all();
        $this->stocks = Stock::with('product')->get();
        $this->sales = Sale::whereDate('created_at', Carbon::today())->get();
        $this->dateFilter = Carbon::today()->toDateString();
        $this->trackResellableItems();
        
    }



    public function render()
    {
        // Filter logs if a search term is entered
        $filteredLogs = empty($this->searchTerm)
            ? $this->excessDemandLogs
            : array_filter(
                $this->excessDemandLogs,
                fn($log) =>
                stripos($log['product'], $this->searchTerm) !== false
            );

        return view('livewire.resallable-items-stock', [
            'excessDemandLogs' => $filteredLogs,
        ]);
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


    
}
