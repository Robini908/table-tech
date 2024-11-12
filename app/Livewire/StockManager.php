<?php

namespace App\Livewire;

use App\Models\Stock;
use Livewire\Component;
use Livewire\WithPagination;
use Exception;

class StockManager extends Component
{
    use WithPagination;

    public $name, $quantity, $unit, $cost_price, $selling_price, $expiry_date, $supplier_id, $batch_number;
    public $images = [];
    public $stockId;

    // Message storage array
    public $messages = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer',
        'unit' => 'required|string',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'expiry_date' => 'nullable|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'batch_number' => 'nullable|string|max:255',
    ];

    public function saveStock()
    {
        $this->validate(); // Trigger the validation

        try {
            $data = $this->validate();  // Perform validation

            // Handle file uploads for images
            if ($this->images) {
                $imagePaths = [];
                foreach ($this->images as $image) {
                    $imagePaths[] = $image->store('stock_images', 'public');
                }
                $data['images'] = $imagePaths;
            }

            // Save or update stock
            Stock::updateOrCreate(['id' => $this->stockId], $data);

            // Clear the input fields after successful save
            $this->resetInputFields();

            // Add success message
            $this->messages[] = [
                'id' => uniqid(),
                'text' => "Stock item saved successfully.",
                'type' => 'success'
            ];
        } catch (Exception $e) {
            // Add error message if an exception occurs
            $this->messages[] = [
                'id' => uniqid(),
                'text' => "Error saving stock: " . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    public function editStock($id)
    {
        try {
            $stock = Stock::findOrFail($id);
            $this->fill($stock->toArray());
            $this->stockId = $stock->id;
        } catch (Exception $e) {
            // Add error message if stock not found or other exception
            $this->messages[] = [
                'id' => uniqid(),
                'text' => "Stock not found: " . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    public function deleteStock($id)
    {
        try {
            // Find and delete the stock
            Stock::findOrFail($id)->delete();

            // Add success message
            $this->messages[] = [
                'id' => uniqid(),
                'text' => "Stock item deleted successfully.",
                'type' => 'success'
            ];
        } catch (Exception $e) {
            // Add error message if deletion fails
            $this->messages[] = [
                'id' => uniqid(),
                'text' => "Error deleting stock: " . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    private function resetInputFields()
    {
        $this->reset([
            'name', 'quantity', 'unit', 'cost_price', 'selling_price', 
            'expiry_date', 'supplier_id', 'batch_number', 'images', 'stockId'
        ]);
    }

    public function render()
    {
        return view('livewire.stock-manager', [
            'stocks' => Stock::paginate(10),
        ]);
    }
}
