<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException; // Import for query exceptions
use Exception; // Import for general exceptions

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
        'batch_number' => 'nullable|string|max:100', // Keeping this rule for validation
        'image' => 'nullable|image|max:1024',
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

    public function showCreateForm()
    {
        $this->resetFields();
        $this->generateBatchNumber();
        $this->showForm = true;
        $this->isCreating = true;
        $this->isEditing = false;
    }

    public function editStock($id)
    {
        $stock = Stock::find($id);
        if ($stock) {
            $this->name = $stock->name;
            $this->quantity = $stock->quantity;
            $this->unit = $stock->unit;
            $this->cost_price = $stock->cost_price;
            $this->selling_price = $stock->selling_price;
            $this->expiry_date = $stock->expiry_date;
            $this->supplier_id = $stock->supplier_id;
            $this->batch_number = $stock->batch_number; // Load the batch number
            $this->image = null;

            $this->isEditing = true;
            $this->editingId = $id;
            $this->showForm = true;
            $this->isCreating = false;
        }
    }

    public function saveStock()
    {
        $this->validate();

        try {
            if ($this->isEditing) {
                $stock = Stock::find($this->editingId);
                // Update image only if a new one has been uploaded
                if ($this->image) {
                    // Delete old image if it exists
                    if ($stock->image) {
                        Storage::delete($stock->image);
                    }
                    $path = $this->image->store('images/stocks');
                } else {
                    $path = $stock->image; // Keep old image if no new upload
                }
                $stock->update([
                    'name' => $this->name,
                    'quantity' => $this->quantity,
                    'unit' => $this->unit,
                    'cost_price' => $this->cost_price,
                    'selling_price' => $this->selling_price,
                    'expiry_date' => $this->expiry_date,
                    'supplier_id' => $this->supplier_id,
                    // 'batch_number' is not updated during editing
                    'image' => $path,
                ]);

                $this->messages[] = ['id' => uniqid(), 'text' => "{$this->name} has been updated successfully!", 'type' => 'success'];
            } else {
                $path = $this->image->store('images/stocks');

                Stock::create([
                    'name' => $this->name,
                    'quantity' => $this->quantity,
                    'unit' => $this->unit,
                    'cost_price' => $this->cost_price,
                    'selling_price' => $this->selling_price,
                    'expiry_date' => $this->expiry_date,
                    'supplier_id' => $this->supplier_id,
                    'batch_number' => $this->batch_number,
                    'image' => $path,
                ]);

                $this->messages[] = ['id' => uniqid(), 'text' => "{$this->name} has been added successfully!", 'type' => 'success'];
            }

            $this->resetFields();
            $this->stocks = Stock::with('supplier')->get();
            $this->showForm = false;

        } catch (QueryException $e) {
            // Handle database query exceptions
            $this->messages[] = ['id' => uniqid(), 'text' => 'Database error: ' . $e->getMessage(), 'type' => 'error'];
        } catch (Exception $e) {
            // Handle other exceptions
            $this->messages[] = ['id' => uniqid(), 'text' => 'An error occurred: ' . $e->getMessage(), 'type' => 'error'];
        }
    }

    public function deleteStock($id)
    {
        try {
            $stock = Stock::find($id);
            if ($stock) {
                // Delete image file if it exists
                if ($stock->image) {
                    Storage::delete($stock->image);
                }
                $stock->delete();
                $this->stocks = Stock::with('supplier')->get();
                $this->messages[] = ['id' => uniqid(), 'text' => "{$stock->name} has been deleted successfully!", 'type' => 'success'];
            }
        } catch (QueryException $e) {
            // Handle database query exceptions
            $this->messages[] = ['id' => uniqid(), 'text' => 'Database error: ' . $e->getMessage(), 'type' => 'error'];
        } catch (Exception $e) {
            // Handle other exceptions
            $this->messages[] = ['id' => uniqid(), 'text' => 'An error occurred: ' . $e->getMessage(), 'type' => 'error'];
        }
    }

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
        $this->isEditing = false;
        $this->editingId = null;
        $this->showForm = false;
        $this->isCreating = false;
    }
}
