{{-- @props(['id', 'maxWidth'])

@php
    $id = $id ?? md5($attributes->wire('model'));

    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth ?? '2xl'];
@endphp

<div x-data="{ show: @entangle($attributes->wire('model')) }"
     x-on:close.stop="show = false"
     x-on:keydown.escape.window="show = false"
     x-show="show"
     id="{{ $id }}"
     class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
    
    <!-- Overlay -->
    <div x-show="show" 
         class="fixed inset-0 transform transition-all"
         x-on:click="show = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <!-- Modal Content -->
    <div x-show="show"
         class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        {{ $slot }}
    </div>
</div> --}}
<div x-data="{ isOpen: @entangle($attributes->wire('model')) }" 
    x-show="isOpen" 
    x-transition 
    @keydown.escape.window="isOpen = false" 
    style="display: none;">
   <div class="fixed inset-0 bg-black opacity-50"></div>
   <div class="fixed inset-0 flex items-center justify-center z-50">
       <div class="bg-white p-6 rounded shadow-lg max-w-lg w-full">
           <div class="modal-header">
               <h5 class="text-xl font-semibold">{{ $title }}</h5>
           </div>
           <div class="modal-body mt-4">
               {{ $content }}
           </div>
           <div class="modal-footer mt-4 flex justify-end">
               {{ $footer }}
           </div>
       </div>
   </div>
</div>
