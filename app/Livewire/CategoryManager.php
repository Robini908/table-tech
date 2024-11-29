<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategoryManager extends Component
{
    public $categories;
    public $name;
    public $categoryId;
    public $isCreating = false;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function render()
    {
        $this->categories = Category::all();

        return view('livewire.category-manager');
    }

    public function toggleCreate()
    {
        $this->resetForm();
        $this->isCreating = !$this->isCreating;
        $this->isEditing = false;
    }

    public function toggleEdit($id)
    {
        $this->resetForm();
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->isEditing = true;
        $this->isCreating = false;
    }

    public function resetForm()
    {
        $this->reset(['name', 'categoryId']);
    }

    public function saveCategory()
    {
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            ['name' => $this->name]
        );

        $this->resetForm();
        $this->isCreating = false;
        $this->isEditing = false;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $this->resetForm();
    }

    public function cancel()
    {
        $this->resetForm();
        $this->isCreating = false;
        $this->isEditing = false;
    }
}
