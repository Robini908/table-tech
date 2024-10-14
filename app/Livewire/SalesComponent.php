<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Sale;
use App\Models\Stock; // Assuming Stock is in the same namespace
use Illuminate\Database\QueryException; 
use Exception; 

class SalesComponent extends Component
{
    use WithFileUploads;

    public $sales = [];
    public $date;
    public $item_name;
    public $stock_id;
    public $quantity_sold;
    public $total_amount = 0;
    public $profit = 0;
    public $is_returned = false;
    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;
    public $messages = [];

    protected $rules = [
        'date' => 'required|date',
        'item_name' => 'required|string|max:255',
        'stock_id' => 'required|exists:stocks,id',
        'quantity_sold' => 'required|integer|min:1',
        'total_amount' => 'required|numeric|min:0',
        'profit' => 'required|numeric|min:0',
        'is_returned' => 'boolean',
    ];

    public function mount()
    {
        $this->sales = Sale::with('stock')->get(); // Adjust for the relationship if necessary
    }

    public function render()
    {
        $stocks = Stock::all(); // Assuming you want to show all stocks for selection
        return view('livewire.sales-component', compact('stocks'));
    }

    public function showCreateForm()
    {
        $this->resetFields();
        $this->showForm = true;
        $this->isEditing = false;
    }

    public function editSale($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $this->date = $sale->date;
            $this->item_name = $sale->item_name;
            $this->stock_id = $sale->stock_id;
            $this->quantity_sold = $sale->quantity_sold;
            $this->total_amount = $sale->total_amount;
            $this->profit = $sale->profit;
            $this->is_returned = $sale->is_returned;

            $this->isEditing = true;
            $this->editingId = $id;
            $this->showForm = true;
        }
    }

    public function calculateTotals()
    {
        // Assuming you have some logic to calculate total_amount and profit
        // For example:
        $stock = Stock::find($this->stock_id);
        if ($stock) {
            $this->total_amount = $this->quantity_sold * $stock->selling_price; // Or your pricing logic
            $this->profit = $this->total_amount - ($this->quantity_sold * $stock->cost_price); // Or your cost logic
        }
    }

    public function saveSale()
    {
        $this->validate();

        try {
            if ($this->isEditing) {
                $sale = Sale::find($this->editingId);
                $sale->update([
                    'date' => $this->date,
                    'item_name' => $this->item_name,
                    'stock_id' => $this->stock_id,
                    'quantity_sold' => $this->quantity_sold,
                    'total_amount' => $this->total_amount,
                    'profit' => $this->profit,
                    'is_returned' => $this->is_returned,
                ]);

                $this->messages[] = ['id' => uniqid(), 'text' => "{$this->item_name} has been updated successfully!", 'type' => 'success'];
            } else {
                Sale::create([
                    'date' => $this->date,
                    'item_name' => $this->item_name,
                    'stock_id' => $this->stock_id,
                    'quantity_sold' => $this->quantity_sold,
                    'total_amount' => $this->total_amount,
                    'profit' => $this->profit,
                    'is_returned' => $this->is_returned,
                ]);

                $this->messages[] = ['id' => uniqid(), 'text' => "{$this->item_name} has been added successfully!", 'type' => 'success'];
            }

            $this->resetFields();
            $this->sales = Sale::with('stock')->get();
            $this->showForm = false;

        } catch (QueryException $e) {
            $this->messages[] = ['id' => uniqid(), 'text' => 'Database error: ' . $e->getMessage(), 'type' => 'error'];
        } catch (Exception $e) {
            $this->messages[] = ['id' => uniqid(), 'text' => 'An error occurred: ' . $e->getMessage(), 'type' => 'error'];
        }
    }

    public function deleteSale($id)
    {
        try {
            $sale = Sale::find($id);
            if ($sale) {
                $sale->delete();
                $this->sales = Sale::with('stock')->get();
                $this->messages[] = ['id' => uniqid(), 'text' => "{$sale->item_name} has been deleted successfully!", 'type' => 'success'];
            }
        } catch (QueryException $e) {
            $this->messages[] = ['id' => uniqid(), 'text' => 'Database error: ' . $e->getMessage(), 'type' => 'error'];
        } catch (Exception $e) {
            $this->messages[] = ['id' => uniqid(), 'text' => 'An error occurred: ' . $e->getMessage(), 'type' => 'error'];
        }
    }

    public function resetFields()
    {
        $this->date = '';
        $this->item_name = '';
        $this->stock_id = '';
        $this->quantity_sold = '';
        $this->total_amount = 0;
        $this->profit = 0;
        $this->is_returned = false;
        $this->isEditing = false;
        $this->editingId = null;
        $this->showForm = false;
    }
}
