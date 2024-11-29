<div class="container mt-4">
    @if($isCreating || $isEditing)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ $isEditing ? 'Edit Category' : 'Add Category' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveCategory">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" id="name" wire:model="name" class="form-control">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div>
            <!-- Button Section (Consistent Position) -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Category List</h5>
                <button wire:click="toggleCreate" class="btn btn-success">Add Category</button>
            </div>

            <!-- Conditional Rendering -->
            @if($categories->isEmpty())
                <!-- Message When No Categories -->
                <div class="alert alert-warning text-center" role="alert">
                    <i class="fas fa-exclamation-circle fa-2x text-warning mb-2"></i>
                    <h5 class="mt-2">No categories found!</h5>
                    <p>Click the "Add Category" button to create your first category.</p>
                </div>
            @else
                <!-- Table When Categories Exist -->
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <button wire:click="toggleEdit({{ $category->id }})" class="btn btn-sm btn-primary">Edit</button>
                                    <button wire:click="deleteCategory({{ $category->id }})" class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
</div>
