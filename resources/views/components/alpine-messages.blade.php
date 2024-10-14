<div class="fixed top-0 inset-x-0 flex flex-col items-center z-50 space-y-4">
    <!-- Display the most recent success message -->
    <template x-if="messages.filter(m => m.type === 'success').length">
        <template x-for="message in messages.filter(m => m.type === 'success').slice(-1)" :key="message.id">
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-10"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-10"
                :class="{
                    'bg-blue-100 text-green-700': message.type === 'success',
                    'bg-red-100 text-red-700': message.type === 'error',
                    'bg-yellow-100 text-yellow-700': message.type === 'info',
                    'bg-orange-100 text-orange-700': message.type === 'warning'
                }" class="p-4 mb-4 text-sm rounded-lg shadow-lg max-w-md mx-auto mt-4">
                <p x-text="message.text" class="text-center"></p>
            </div>
        </template>
    </template>

    <!-- Display the most recent warning message if no success message is present -->
    <template x-if="!messages.filter(m => m.type === 'success').length && messages.filter(m => m.type === 'warning').length">
        <template x-for="message in messages.filter(m => m.type === 'warning').slice(-1)" :key="message.id">
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-10"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-10"
                :class="{
                    'bg-blue-100 text-blue-700': message.type === 'success',
                    'bg-red-100 text-red-700': message.type === 'error',
                    'bg-yellow-100 text-yellow-700': message.type === 'info',
                    'bg-orange-100 text-orange-700': message.type === 'warning'
                }" class="p-4 mb-4 text-sm rounded-lg shadow-lg max-w-md mx-auto mt-4">
                <p x-text="message.text" class="text-center"></p>
            </div>
        </template>
    </template>

    <!-- Display the most recent info message if no success or warning message is present -->
    <template x-if="!messages.filter(m => m.type === 'success').length && !messages.filter(m => m.type === 'warning').length && messages.filter(m => m.type === 'info').length">
        <template x-for="message in messages.filter(m => m.type === 'info').slice(-1)" :key="message.id">
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-10"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-10"
                :class="{
                    'bg-blue-100 text-blue-700': message.type === 'success',
                    'bg-red-100 text-red-700': message.type === 'error',
                    'bg-yellow-100 text-yellow-700': message.type === 'info',
                    'bg-orange-100 text-orange-700': message.type === 'warning'
                }" class="p-4 mb-4 text-sm rounded-lg shadow-lg max-w-md mx-auto mt-4">
                <p x-text="message.text" class="text-center"></p>
            </div>
        </template>
    </template>

    <!-- Display the most recent error message if no success, warning, or info message is present -->
    <template x-if="!messages.filter(m => m.type === 'success').length && !messages.filter(m => m.type === 'warning').length && !messages.filter(m => m.type === 'info').length && messages.filter(m => m.type === 'error').length">
        <template x-for="message in messages.filter(m => m.type === 'error').slice(-1)" :key="message.id">
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-10"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-10"
                :class="{
                    'bg-blue-100 text-blue-700': message.type === 'success',
                    'bg-red-100 text-red-700': message.type === 'error',
                    'bg-yellow-100 text-yellow-700': message.type === 'info',
                    'bg-orange-100 text-orange-700': message.type === 'warning'
                }" class="p-4 mb-4 text-sm rounded-lg shadow-lg max-w-md mx-auto mt-4">
                <p x-text="message.text" class="text-center"></p>
            </div>
        </template>
    </template>
</div>
