<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-2">
                <a href="{{ route('dashboard') }}" class="block rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-indigo-500 transition">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Planning de repas</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Organisez votre semaine avec 7 colonnes et glissez vos plats pour midi et soir.</p>
                </a>

                <a href="{{ route('dishes.index') }}" class="block rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-indigo-500 transition">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gestion des plats</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Créez, modifiez et supprimez vos plats. Chaque plat inclut des ingrédients nom/quantité/unité.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
