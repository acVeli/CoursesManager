<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Modifier le plat') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Mettre à jour le plat et ses ingrédients.</p>
            </div>
            <div>
                <a href="{{ route('dishes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('dishes.update', $dish) }}" id="dish-form">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du plat</label>
                            <input type="text" name="name" value="{{ old('name', $dish->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (facultatif)</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $dish->description) }}</textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Ingrédients</h3>
                                <button type="button" onclick="addIngredient()" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-500">Ajouter un ingrédient</button>
                            </div>
                            <div id="ingredient-list" class="space-y-3">
                                @foreach(old('ingredients', $dish->ingredients->toArray()) as $index => $ingredient)
                                    <div class="grid gap-3 sm:grid-cols-[1fr_1fr_1fr_auto] items-end p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Nom</label>
                                            <input type="text" name="ingredients[{{ $index }}][name]" value="{{ $ingredient['name'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Quantité</label>
                                            <input type="number" step="0.01" name="ingredients[{{ $index }}][quantity]" value="{{ $ingredient['quantity'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Unité</label>
                                            <input type="text" name="ingredients[{{ $index }}][unit]" value="{{ $ingredient['unit'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                        </div>
                                        <div>
                                            <button type="button" onclick="removeIngredient(event)" class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-500">Supprimer</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('dishes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md text-xs font-semibold hover:bg-gray-500">Annuler</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-xs font-semibold hover:bg-green-500">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

   <script>
    let ingredientIndex = {{ count(old('ingredients', $dish->ingredients->toArray())) }};

    function addIngredient() {
        const list = document.getElementById('ingredient-list');
        const item = document.createElement('div');
        item.className = 'grid gap-3 sm:grid-cols-[1fr_1fr_1fr_auto] items-end p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900';
        item.innerHTML = `
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Nom</label>
                <input type="text" name="ingredients[${ingredientIndex}][name]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Quantité</label>
                <input type="number" step="0.01" name="ingredients[${ingredientIndex}][quantity]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Unité</label>
                <input type="text" name="ingredients[${ingredientIndex}][unit]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
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
        const row = button.closest('div.grid');
        if (row) {
            row.remove();
        }
    }
</script>
</x-app-layout>
