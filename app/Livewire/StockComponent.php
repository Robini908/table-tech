<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Exception;

class StockComponent extends Component
{
    use WithFileUploads;

    public $stocks = [];
    public $name;
    public $quantity;
    public $unit;
    public $cost_price;
    public $selling_price;
    public $expiry_date;
    public $supplier_id;
    public $batch_number;
    public $image;
    public $estimated_output;  // New property for estimated output
    public $demand_trend = []; // Array to track demand trends

    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;
    public $isCreating = false;
    public $messages = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'unit' => 'required|string|max:50',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'expiry_date' => 'nullable|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'batch_number' => 'nullable|string|max:100',
        'image' => 'nullable|image|max:1024',
        'estimated_output' => 'required|integer|min:1',  // New validation rule for output
    ];

    public function mount()
    {
        $this->generateBatchNumber();
        $this->stocks = Stock::with('supplier')->get();
    }

    public function generateBatchNumber()
    {
        $datePart = now()->format('Ymd');
        $count = Stock::whereDate('created_at', now())->count();
        $this->batch_number = "{$datePart}-" . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $suppliers = Supplier::all();
        return view('livewire.stock-component', compact('suppliers'));
    }

    public function saveStock()
    {
        $this->validate();

        try {
            $path = $this->image ? $this->image->store('images/stocks') : null;

            if ($this->isEditing) {
                $stock = Stock::find($this->editingId);
                if ($this->image && $stock->image) {
                    Storage::delete($stock->image);
                }
                $path = $path ?: $stock->image;

                $stock->update([
                    'name' => $this->name,
                    'quantity' => $this->quantity,
                    'unit' => $this->unit,
                    'cost_price' => $this->cost_price,
                    'selling_price' => $this->selling_price,
                    'expiry_date' => $this->expiry_date,
                    'supplier_id' => $this->supplier_id,
                    'estimated_output' => $this->estimated_output,
                    'image' => $path,
                ]);
                $this->messages[] = ['id' => uniqid(), 'text' => "{$this->name} has been updated!", 'type' => 'success'];
            } else {
                Stock::create([
                    'name' => $this->name,
                    'quantity' => $this->quantity,
                    'unit' => $this->unit,
                    'cost_price' => $this->cost_price,
                    'selling_price' => $this->selling_price,
                    'expiry_date' => $this->expiry_date,
                    'supplier_id' => $this->supplier_id,
                    'batch_number' => $this->batch_number,
                    'estimated_output' => $this->estimated_output,
                    'image' => $path,
                ]);
                $this->messages[] = ['id' => uniqid(), 'text' => "{$this->name} has been added!", 'type' => 'success'];
            }

            $this->resetFields();
            $this->stocks = Stock::with('supplier')->get();
            $this->showForm = false;

        } catch (QueryException $e) {
            $this->messages[] = ['id' => uniqid(), 'text' => 'Database error: ' . $e->getMessage(), 'type' => 'error'];
        } catch (Exception $e) {
            $this->messages[] = ['id' => uniqid(), 'text' => 'An error occurred: ' . $e->getMessage(), 'type' => 'error'];
        }
    }

    // Method to handle deductions upon sale
    public function recordSale($platesSold)
    {
        $stock = Stock::find($this->editingId);
        if ($stock && $platesSold <= $stock->estimated_output) {
            $remainingPlates = $stock->estimated_output - $platesSold;
            $this->messages[] = ['id' => uniqid(), 'text' => "Remaining plates: {$remainingPlates}", 'type' => 'info'];

            // Deduct quantity and update estimated output
            $stock->quantity -= $platesSold;
            $stock->estimated_output = $remainingPlates;
            $stock->save();

            if ($remainingPlates <= 0) {
                $this->messages[] = ['id' => uniqid(), 'text' => "Stock depleted. Please reorder or adjust demand.", 'type' => 'warning'];
            }
        }
    }

    // Reset fields after saving
    public function resetFields()
    {
        $this->name = '';
        $this->quantity = '';
        $this->unit = '';
        $this->cost_price = '';
        $this->selling_price = '';
        $this->expiry_date = '';
        $this->supplier_id = '';
        $this->generateBatchNumber();
        $this->image = null;
        $this->estimated_output = '';
        $this->isEditing = false;
        $this->editingId = null;
        $this->showForm = false;
        $this->isCreating = false;
    }
}
