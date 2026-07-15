<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Plats') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Créez, modifiez et supprimez vos plats.</p>
            </div>
            <div>
                <a href="{{ route('dishes.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-indigo-500">Nouveau plat</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 space-y-4">
                @forelse($dishes as $dish)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $dish->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dish->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $dish->ingredients->count() }} ingrédient(s)</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('dishes.edit', $dish) }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 text-black rounded-md text-xs font-semibold hover:bg-yellow-400">Modifier</a>
                                <form method="POST" action="{{ route('dishes.destroy', $dish) }}" onsubmit="return confirm('Supprimer ce plat ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 text-black rounded-md text-xs font-semibold hover:bg-red-500">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4 text-sm text-gray-500">
                        Aucun plat enregistré. Commencez par en créer un.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
