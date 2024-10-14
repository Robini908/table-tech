<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;

class SupplierComponent extends Component
{
    public $suppliers = [];
    public $name;
    public $contact_info;
    public $address;
    public $isEditing = false;
    public $editingId = null;
    public $showForm = false; // Control the visibility of the form
    public $isCreating = false; // Control whether the form is for creating or editing
    public $messages = []; // Array to hold messages

    protected $rules = [
        'name' => 'required|string|max:255',
        'contact_info' => 'required|string|max:255',
        'address' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->suppliers = Supplier::all();
    }

    public function render()
    {
        return view('livewire.supplier-component');
    }

    public function showCreateForm()
    {
        $this->resetFields(); // Reset fields to clear any old data
        $this->showForm = true; // Show the form
        $this->isCreating = true; // Set to true to indicate we are creating a new supplier
        $this->isEditing = false; // Ensure editing is false
    }

    public function editSupplier($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            $this->name = $supplier->name;
            $this->contact_info = $supplier->contact_info;
            $this->address = $supplier->address;
            $this->isEditing = true;
            $this->editingId = $id;
            $this->showForm = true; // Show the form when editing
            $this->isCreating = false; // Set to false to indicate editing mode
        }
    }

    public function saveSupplier()
    {
        $this->validate();

        if ($this->isEditing) {
            $supplier = Supplier::find($this->editingId);
            $supplier->update([
                'name' => $this->name,
                'contact_info' => $this->contact_info,
                'address' => $this->address,
            ]);
            // Add a success message
            $this->messages[] = ['id' => uniqid(), 'text' => "{$this->name} has been updated!", 'type' => 'success'];
        } else {
            Supplier::create([
                'name' => $this->name,
                'contact_info' => $this->contact_info,
                'address' => $this->address,
            ]);
            // Add a success message
            $this->messages[] = ['id' => uniqid(), 'text' => "{$this->name} has been added!", 'type' => 'success'];
        }

        $this->resetFields();
        $this->suppliers = Supplier::all(); // Refresh the list
        $this->showForm = false; // Hide the form after saving
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            Supplier::destroy($id);
            // Add a success message
            $this->messages[] = ['id' => uniqid(), 'text' => "{$supplier->name} has been deleted!", 'type' => 'success'];
            $this->suppliers = Supplier::all(); // Refresh the list
        } else {
            // Add an error message if supplier not found
            $this->messages[] = ['id' => uniqid(), 'text' => "Supplier not found!", 'type' => 'error'];
        }
    }

    public function resetFields()
    {
        $this->name = '';
        $this->contact_info = '';
        $this->address = '';
        $this->isEditing = false;
        $this->editingId = null;
        $this->showForm = false; // Reset form visibility
        $this->isCreating = false; // Reset the creating state
    }
}
