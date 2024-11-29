<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Sale;
use Livewire\Component;

class SalesComponent extends Component
{
    public $sales = [];
    public $products;
    public $stocks;
    public $product_id;
    public $stock_id;
    public $quantity;
    public $price_per_unit;
    public $total_price;
    public $isSelling = false; // Toggle for new sale form
    public $isEditingSale = false; // Toggle for editing sale form
    public $sale_id;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'stock_id' => 'required|exists:stocks,id',
        'quantity' => 'required|numeric|min:1',
        'price_per_unit' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->products = Product::all();
        $this->stocks = Stock::all();
    }

    public function render()
    {
        $this->sales = Sale::with('product', 'stock')->get();
        return view('livewire.sales-component');
    }

    public function storeSale()
    {
        $this->validate();

        // Calculate total price
        $this->total_price = $this->quantity * $this->price_per_unit;

        // Create sale
        $sale = Sale::create([
            'product_id' => $this->product_id,
            'stock_id' => $this->stock_id,
            'quantity' => $this->quantity,
            'price_per_unit' => $this->price_per_unit,
            'total_price' => $this->total_price,
        ]);

        // Update stock quantity
        $stock = Stock::find($this->stock_id);
        if ($stock) {
            $stock->quantity -= $this->quantity;
            $stock->save();
        }

        session()->flash('message', 'Sale created successfully!');
        $this->resetForm();
        $this->isSelling = false; // Close form after creating the sale
    }

    public function editSale($saleId)
    {
        $sale = Sale::find($saleId);
        if ($sale) {
            $this->sale_id = $sale->id;
            $this->product_id = $sale->product_id;
            $this->stock_id = $sale->stock_id;
            $this->quantity = $sale->quantity;
            $this->price_per_unit = $sale->price_per_unit;
            $this->total_price = $sale->total_price;
            $this->isEditingSale = true; // Toggle to edit sale form
        }
    }

    public function updateSale()
    {
        $this->validate();

        $sale = Sale::find($this->sale_id);
        if ($sale) {
            // Recalculate total price
            $this->total_price = $this->quantity * $this->price_per_unit;

            $sale->update([
                'product_id' => $this->product_id,
                'stock_id' => $this->stock_id,
                'quantity' => $this->quantity,
                'price_per_unit' => $this->price_per_unit,
                'total_price' => $this->total_price,
            ]);

            // Update stock quantity
            $stock = Stock::find($this->stock_id);
            if ($stock) {
                $stock->quantity -= $this->quantity;
                $stock->save();
            }

            session()->flash('message', 'Sale updated successfully!');
            $this->resetForm();
            $this->isEditingSale = false; // Close form after editing
        }
    }

    public function deleteSale($saleId)
    {
        $sale = Sale::find($saleId);
        if ($sale) {
            // Restore stock before deleting sale
            $stock = Stock::find($sale->stock_id);
            if ($stock) {
                $stock->quantity += $sale->quantity;
                $stock->save();
            }

            $sale->delete();

            session()->flash('message', 'Sale deleted successfully!');
        }
    }

    public function resetForm()
    {
        $this->product_id = '';
        $this->stock_id = '';
        $this->quantity = '';
        $this->price_per_unit = '';
        $this->total_price = '';
        $this->sale_id = null;
    }

    public function createNewSale()
    {
        $this->isSelling = true; // Open the form to create a new sale
        $this->resetForm();
    }
}

