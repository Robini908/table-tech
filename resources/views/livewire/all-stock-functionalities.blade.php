<div>
    <!-- Tab Navigation -->
    <div class="flex space-x-6 border-b-2 border-indigo-200 pb-2">
        <button 
            wire:click="setActiveTab(1)"
            class="{{ $activeTab === 1 ? 'text-indigo-600 border-b-4 border-indigo-600 transform scale-105' : 'text-gray-500 hover:text-indigo-600 transition-all ease-in-out duration-300' }} py-2 px-6 text-sm font-semibold transition-all ease-in-out duration-300 hover:text-indigo-700 focus:outline-none">
            Check Stock
        </button>
        <button 
            wire:click="setActiveTab(2)"
            class="{{ $activeTab === 2 ? 'text-indigo-600 border-b-4 border-indigo-600 transform scale-105' : 'text-gray-500 hover:text-indigo-600 transition-all ease-in-out duration-300' }} py-2 px-6 text-sm font-semibold transition-all ease-in-out duration-300 hover:text-indigo-700 focus:outline-none">
            Resallable Stock
        </button>
        <button 
            wire:click="setActiveTab(3)"
            class="{{ $activeTab === 3 ? 'text-indigo-600 border-b-4 border-indigo-600 transform scale-105' : 'text-gray-500 hover:text-indigo-600 transition-all ease-in-out duration-300' }} py-2 px-6 text-sm font-semibold transition-all ease-in-out duration-300 hover:text-indigo-700 focus:outline-none">
            Track Excess Demand
        </button>
    </div>

    <!-- Tab Content -->
    <div class="mt-6 space-y-6">
        @if($activeTab === 1)
            <div>
                @livewire('stock-manager')
            </div>
        @elseif($activeTab === 2)
            <div>
                @livewire('resallable-items-stock')
            </div>
        @elseif($activeTab === 3)
            <div>
                @livewire('track-excess-demand')
            </div>
        @endif
    </div>
</div>
