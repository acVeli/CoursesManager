<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Planning de repas') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Organisez votre semaine et générez automatiquement la liste de courses.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dishes.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-indigo-500">Gérer les plats</a>
                <a href="{{ route('dishes.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-green-500">Ajouter un plat</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                    <form id="plan-form" method="POST" action="{{ route('meal-plan.save') }}">
                        @csrf
                        <input type="hidden" name="week_start" value="{{ $weekStart }}" />
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semaine du</label>
                                <input type="date" name="week_start" value="{{ $weekStart }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-green-500">Enregistrer le planning</button>
                        </div>

                        <div class="overflow-x-auto pb-3">
                            <div class="min-w-[72rem]">
                                <div class="flex gap-2 mb-2">
                                    @foreach(['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'] as $day)
                                        <div class="flex-1 min-w-[10rem] px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-center text-xs uppercase text-gray-500 dark:text-gray-400">{{ $day }}</div>
                                    @endforeach
                                </div>

                                <div class="flex gap-2">
                                    @php
                                        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                                        $meals = ['midi' => 'Midi', 'soir' => 'Soir'];
                                    @endphp
                                    @foreach($days as $day)
                                        <div class="flex-1 min-w-[10rem] space-y-2">
                                            @foreach($meals as $mealKey => $mealLabel)
                                                @php $slotKey = "{$day}_{$mealKey}"; @endphp
                                                <div class="min-h-[7rem] rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-2 flex flex-col justify-between" data-slot="{{ $slotKey }}" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $mealLabel }}</div>
                                                    <div class="mt-2 grow" id="slot-{{ $slotKey }}">
                                                        @if(isset($entries[$slotKey]) && $entries[$slotKey]->dish)
                                                            <div class="rounded-lg bg-indigo-600 text-black p-2" data-dish-id="{{ $entries[$slotKey]->dish->id }}" draggable="true" ondragstart="drag(event)">{{ $entries[$slotKey]->dish->name }}</div>
                                                            <input type="hidden" name="slots[{{ $slotKey }}][week_start]" value="{{ $weekStart }}" />
                                                            <input type="hidden" name="slots[{{ $slotKey }}][day]" value="{{ $day }}" />
                                                            <input type="hidden" name="slots[{{ $slotKey }}][meal]" value="{{ $mealKey }}" />
                                                            <input type="hidden" name="slots[{{ $slotKey }}][dish_id]" value="{{ $entries[$slotKey]->dish->id }}" />
                                                        @else
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">Glissez un plat ici</div>
                                                            <input type="hidden" name="slots[{{ $slotKey }}][week_start]" value="{{ $weekStart }}" />
                                                            <input type="hidden" name="slots[{{ $slotKey }}][day]" value="{{ $day }}" />
                                                            <input type="hidden" name="slots[{{ $slotKey }}][meal]" value="{{ $mealKey }}" />
                                                            <input type="hidden" name="slots[{{ $slotKey }}][dish_id]" value="" />
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 space-y-4">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Plats disponibles</h3>
                        <div class="space-y-2">
                            @forelse($dishes as $dish)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-3" draggable="true" data-dish-id="{{ $dish->id }}" ondragstart="drag(event)">
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $dish->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $dish->ingredients->count() }} ingr.</div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Aucun plat défini. Ajoutez-en sur la page Plats.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Liste de courses</h3>
                        <div class="space-y-2">
                            @forelse($shopping as $item)
                                <label class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-2">
                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $item['name'] }} — Quantité : {{ $item['quantity'] }} - Volume unitaire : {{ $item['unit'] }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">Aucun ingrédient sélectionné pour cette semaine.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function allowDrop(event) {
            event.preventDefault();
        }

        function clearSlot(slot, showPlaceholder = false) {
            const current = slot.querySelector('[data-dish-id]');
            if (current) {
                current.remove();
            }

            slot.querySelector('div.text-xs')?.remove();

            const hidden = slot.querySelector('input[name$="[dish_id]"]');
            if (hidden) {
                hidden.value = '';
            }

            if (showPlaceholder && !slot.querySelector('[data-dish-id]')) {
                const placeholder = document.createElement('div');
                placeholder.className = 'slot-placeholder text-xs text-gray-500 dark:text-gray-400';
                placeholder.textContent = 'Glissez un plat ici';
                if (hidden) {
                    hidden.before(placeholder);
                } else {
                    slot.appendChild(placeholder);
                }
            }
        }

        function drag(event) {
            const target = event.target.closest('[data-dish-id]') || event.target;
            const dishId = target.dataset.dishId;
            const dishName = target.textContent.trim();
            const sourceSlot = target.closest('[data-slot]')?.dataset.slot || null;
            event.dataTransfer.setData('text/plain', JSON.stringify({ dishId, dishName, sourceSlot }));
        }

        function drop(event) {
            event.preventDefault();
            const data = JSON.parse(event.dataTransfer.getData('text/plain'));
            const slot = event.currentTarget;

            const sourceSlot = data.sourceSlot ? document.querySelector(`[data-slot="${data.sourceSlot}"]`) : null;
            if (sourceSlot && sourceSlot !== slot) {
                clearSlot(sourceSlot, true);
            }

            clearSlot(slot, false);

            const card = document.createElement('div');
            card.className = 'rounded-lg bg-indigo-600 text-black p-2';
            card.textContent = data.dishName;
            card.dataset.dishId = data.dishId;
            card.draggable = true;
            card.ondragstart = drag;
            slot.appendChild(card);

            const hidden = slot.querySelector('input[name$="[dish_id]"]');
            if (hidden) {
                hidden.value = data.dishId;
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('dragover', allowDrop);
            document.addEventListener('drop', function (event) {
                const slot = event.target.closest('[data-slot]');
                if (slot) {
                    return;
                }

                const data = event.dataTransfer?.getData('text/plain');
                if (!data) {
                    return;
                }

                const payload = JSON.parse(data);
                const sourceSlot = payload.sourceSlot ? document.querySelector(`[data-slot="${payload.sourceSlot}"]`) : null;
                if (sourceSlot) {
                    clearSlot(sourceSlot, true);
                }
            });

            const dateInput = document.querySelector('input[type="date"][name="week_start"]');
            if (!dateInput) return;

            dateInput.addEventListener('change', function () {
                const selected = this.value;
                if (!selected) return;
                const url = new URL(window.location.href);
                url.searchParams.set('week_start', selected);
                // Navigate to same path with updated query param to reload the page for the selected week
                window.location.href = url.pathname + (url.search ? ('?' + url.searchParams.toString()) : '');
            });
        });
    </script>
</x-app-layout>
