<?php

namespace App\Livewire;


use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\ExcessDemand;

class TrackExcessDemand extends Component
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
        $this->updateStockStatus();

        $this->trackExcessDemand();
    }
    public function trackExcessDemand()
    {
        try {
            $this->excessDemandLogs = [];

            // Fetch sales with related product data
            $sales = Sale::with('product')->get();

            if ($sales->isEmpty()) {
                throw new \Exception("No sales data available to track excess demand.");
            }

            foreach ($sales as $sale) {
                $product = $sale->product;

                if ($product) {
                    // Fetch stock quantity for the product
                    $stock = Stock::where('product_id', $product->id)->first();

                    if (!$stock) {
                        continue; // Skip if no stock record exists
                    }

                    $excessDemand = $sale->quantity > $stock->quantity;

                    // Add log if demand exceeds availability
                    $this->excessDemandLogs[] = [
                        'product' => $product->name,
                        'sold' => $sale->quantity,
                        'available' => $stock->quantity,
                        'date' => $sale->created_at,
                        'excessDemand' => $excessDemand,
                    ];
                }
            }

            // Sort logs by date, descending
            usort($this->excessDemandLogs, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

            if (empty($this->excessDemandLogs)) {
                throw new \Exception("No excess demand found in the sales data.");
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->excessDemandLogs = [];
        }
    }

    public function downloadCSV()
    {
        // Generate CSV data from excess demand logs
        $csvData = [];
        $csvData[] = ['Product', 'Sold Quantity', 'Available Quantity', 'Date', 'Status'];
        foreach ($this->excessDemandLogs as $log) {
            $csvData[] = [
                $log['product'],
                $log['sold'],
                $log['available'],
                $log['date']->format('Y-m-d H:i:s'),
                $log['excessDemand'] ? 'Excess Demand' : 'Normal'
            ];
        }

        // Write CSV to output
        $fileName = 'excess_demand_logs_' . now()->format('Y_m_d_His') . '.csv';
        $csvFile = fopen('/mnt/data/' . $fileName, 'w');
        foreach ($csvData as $line) {
            fputcsv($csvFile, $line);
        }
        fclose($csvFile);

        return response()->download('/mnt/data/' . $fileName);
    }


    public function stockUpdated()
    {
        $this->stocks = Stock::with('product')->get();
        $this->updateStockStatus();
    }
    public function updateStockStatus()
    {
        $this->stocks = Stock::with('product')->get();
    }

    public function render()
    {
        return view('livewire.track-excess-demand');
    }
}
