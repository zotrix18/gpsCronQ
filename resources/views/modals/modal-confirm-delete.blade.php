<div x-show="del" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
        <div x-cloak @click="del = false" x-show="del" 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-40" aria-hidden="true">
        </div>

        <div x-cloak x-show="del" 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
            <div class="flex items-center justify-between space-x-4">
                <p>Eliminar</p>            

                <button @click="del = false" class="text-gray-600 focus:outline-none hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>

            <hr class = "mt-2">

            <h1 class="text-xl font-medium text-gray-800 mt-6">¿Estás seguro de realizar esta acción?</h1>

            <hr class = "mt-6">

            <form method="POST" action = "{{route($route.'.destroy',[$id])}}" class="mt-5">
                @csrf
                @method('DELETE')
                <div class="flex justify-end mt-6">
                    <button type="submit" class="ml-2 px-3 py-2 text-sm tracking-wide text-white capitalize transition-colors duration-200 transform bg-green-600 rounded-md dark:bg-green-500 dark:hover:bg-green-600 dark:focus:bg-green-600 hover:bg-green-600 focus:outline-none focus:bg-green-500 focus:ring focus:ring-green-600 focus:ring-opacity-50">
                        Estoy Seguro
                    </button>
                </div>  
            </form>
        </div>
    </div>    
</div>