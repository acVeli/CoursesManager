<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Nouveau plat') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Ajoutez un plat avec ses ingrédients.</p>
            </div>
            <div>
                <a href="{{ route('dishes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-500">Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                <form method="POST" action="{{ route('dishes.store') }}" id="dish-form">
                    @csrf

                    <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50 dark:bg-gray-900 p-6 shadow-sm">
                        <div class="mb-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Étape 1</p>
                            <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">Nom du plat</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Commencez par nommer votre plat, puis ajoutez les ingrédients.</p>
                        </div>

                        @if(auth()->user()->has_baby)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plat bébé</label>
                                <input type="checkbox" name="baby" value="1" {{ old('baby') ? 'checked' : '' }} class="mt-2 mb-2 block rounded-xl border border-gray-300 bg-white dark:bg-gray-800 text-gray-900
                                dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du plat</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Ex. Poulet au curry" required />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50 dark:bg-gray-900 p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Étape 2</p>
                                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">Ingrédients</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ajoutez un ingrédient par ligne. Vous pourrez enregistrer tous les éléments en une seule fois.</p>
                            </div>

                            <button type="button" onclick="addIngredient()" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-black shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">+ Ajouter un ingrédient</button>
                        </div>

                        @php
                            $oldIngredients = old('ingredients', []);
                        @endphp

                        <div id="ingredient-list" class="space-y-4">
                            @if(count($oldIngredients) > 0)
                                @foreach($oldIngredients as $index => $ingredient)
                                    <div class="ingredient-row grid gap-3 sm:grid-cols-[1.4fr_1fr_1fr_auto] items-end p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Nom</label>
                                            <input type="text" name="ingredients[{{ $index }}][name]" value="{{ old('ingredients.'.$index.'.name') }}" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                                            @error('ingredients.'.$index.'.name')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Quantité</label>
                                            <input type="number" step="0.01" name="ingredients[{{ $index }}][quantity]" value="{{ old('ingredients.'.$index.'.quantity') }}" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                                            @error('ingredients.'.$index.'.quantity')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Unité</label>
                                            <input type="text" name="ingredients[{{ $index }}][unit]" value="{{ old('ingredients.'.$index.'.unit') }}" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                            @error('ingredients.'.$index.'.unit')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <button type="button" onclick="removeIngredient(event)" class="inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-red-500">Supprimer</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="ingredient-row grid gap-3 sm:grid-cols-[1.4fr_1fr_1fr_auto] items-end p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Nom</label>
                                        <input type="text" name="ingredients[0][name]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Quantité</label>
                                        <input type="number" step="0.01" name="ingredients[0][quantity]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Unité</label>
                                        <input type="text" name="ingredients[0][unit]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                    </div>
                                    <div class="flex items-center pt-6">
                                        <button type="button" onclick="removeIngredient(event)" class="inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-red-500">Supprimer</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-green-600 px-6 py-3 text-sm font-semibold text-black shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Enregistrer le plat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let ingredientIndex = {{ max(count(old('ingredients', [])), 1) }};

        function addIngredient() {
            const list = document.getElementById('ingredient-list');
            const item = document.createElement('div');
            item.className = 'ingredient-row grid gap-3 sm:grid-cols-[1.4fr_1fr_1fr_auto] items-end p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm';
            item.innerHTML = `
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Nom</label>
                    <input type="text" name="ingredients[${ingredientIndex}][name]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Quantité</label>
                    <input type="number" step="0.01" name="ingredients[${ingredientIndex}][quantity]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Unité</label>
                    <input type="text" name="ingredients[${ingredientIndex}][unit]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>
                <div>
                    <button type="button" onclick="removeIngredient(event)" class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-500">Supprimer</button>
                </div>
            `;
            list.appendChild(item);
            ingredientIndex++;
        }

        function removeIngredient(event) {
            const button = event.currentTarget;
            const row = button.closest('.ingredient-row');
            if (row) {
                row.remove();
            }
        }
    </script>
</x-app-layout>
