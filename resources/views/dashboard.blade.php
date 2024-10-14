<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ open: false, selectedComponent: 'supplier', dropdownAlignRight: false }" @resize.window="dropdownAlignRight = window.innerWidth < 640 ? false : true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Dropdown for selecting component -->
                <div class="mb-4">
                    <div class="relative inline-block text-left">
                        <button @click="open = !open" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                                aria-expanded="true" aria-haspopup="true">
                            More Actions
                            <i class="fas fa-chevron-down ml-2"></i> <!-- FontAwesome icon -->
                        </button>
                        
                        <!-- Dropdown content -->
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                             :class="dropdownAlignRight ? 'right-0' : 'left-0'">
                            <div class="py-1">
                                <a href="#" @click.prevent="selectedComponent = 'supplier'; open = false"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <i class="fas fa-truck mr-2"></i> <!-- FontAwesome icon for supplier -->
                                    Supplier
                                </a>
                                <a href="#" @click.prevent="selectedComponent = 'stock'; open = false"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <i class="fas fa-boxes mr-2"></i> <!-- FontAwesome icon for stock -->
                                    Stock
                                </a>
                                <a href="#" @click.prevent="selectedComponent = 'sales'; open = false"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <i class="fas fa-cash-register mr-2"></i> <!-- FontAwesome icon for sales -->
                                    Sales
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conditional rendering based on selected component -->
                <div x-show="selectedComponent === 'supplier'" x-transition>
                    <livewire:supplier-component />
                </div>

                <div x-show="selectedComponent === 'stock'" x-transition>
                    <livewire:stock-component />
                </div>

                <div x-show="selectedComponent === 'sales'" x-transition>
                    <livewire:sales-component />
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
