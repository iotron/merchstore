<x-filament::page>
    <div>
        <h1 class="text-xl">Types
            of products
        </h1>
        <dl class="mt-2 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="px-4 py-5 shadow-md rounded-lg overflow-hidden sm:p-6 bg-white dark:bg-slate-800">
                <h2 class="text-2xl font-medium underline underline-offset-8 decoration-green-500">Simple</h2>
                <p class="mt-4 text-slate-500 dark:text-slate-400 font-medium">Simple product is
                    unique and
                    has no variants.</p>
                <p class="mt-4 text-slate-900 dark:text-slate-50 text-sm italic">Example - Original Artworks.</p>
            </div>

            <div class="px-4 py-5 shadow-md rounded-lg overflow-hidden sm:p-6 bg-white dark:bg-slate-800">
                <h2 class="text-2xl font-medium underline underline-offset-8 decoration-green-500">Configurable
                </h2>
                <p class="mt-4 text-slate-500 dark:text-slate-400 font-medium">Configurable product has variants.
                    eg, same
                    product in multiple
                    colors, sizes.</p>
                <p class="mt-4 text-slate-900 dark:text-slate-50 text-sm italic">Example - T-Shirts, Posters.</p>
            </div>

            <div class="px-4 py-5 shadow-md rounded-lg overflow-hidden sm:p-6 bg-white dark:bg-slate-800">
                <h2 class="text-2xl font-medium underline underline-offset-8 decoration-green-500">Bundle</h2>
                <p class="mt-4 text-slate-500 dark:text-slate-400 font-medium">Bundle products consist of other
                    products.
                </p>
                <p class="mt-4 text-slate-900 dark:text-slate-50 text-sm italic">Example - Merch Combo Packs.</p>
            </div>
        </dl>
    </div>

    {{ $this->form }}

</x-filament::page>
