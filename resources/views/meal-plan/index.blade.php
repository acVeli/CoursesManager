<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Planning de repas') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Organisez votre semaine et générez automatiquement la liste de courses.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('dishes.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-indigo-500">Gérer les plats</a>
                <a href="{{ route('dishes.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-green-500">Ajouter un plat</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                    <form id="plan-form" method="POST" action="{{ route('meal-plan.save') }}">
                        @csrf
                        <input type="hidden" name="week_start" value="{{ $weekStart }}" />
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between mb-4">
                            <div class="w-full sm:w-auto">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semaine du</label>
                                <input type="date" name="week_start" value="{{ $weekStart }}" class="mt-1 block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-green-500" id="save-plan">Enregistrer le planning</button>
                        </div>

                        @php
                            $dayLabels = ['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 'thursday' => 'Jeudi', 'friday' => 'Vendredi', 'saturday' => 'Samedi', 'sunday' => 'Dimanche'];
                            $days = array_keys($dayLabels);
                            $meals = ['midi' => 'Midi', 'soir' => 'Soir'];
                        @endphp

                        <div class="lg:overflow-x-auto lg:pb-3">
                            <div class="space-y-3 lg:space-y-0 lg:min-w-[72rem]">
                                <div class="hidden lg:flex gap-2 mb-2">
                                    @foreach($dayLabels as $dayLabel)
                                        <div class="flex-1 min-w-[10rem] px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-center text-xs uppercase text-gray-500 dark:text-gray-400">{{ $dayLabel }}</div>
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:flex lg:gap-2">
                                    @foreach($days as $day)
                                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2 lg:rounded-none lg:border-0 lg:p-0 lg:flex-1 lg:min-w-[10rem]">
                                            <div class="lg:hidden px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-center text-xs uppercase text-gray-500 dark:text-gray-400">{{ $dayLabels[$day] }}</div>
                                            @foreach($meals as $mealKey => $mealLabel)
                                                @php $slotKey = "{$day}_{$mealKey}"; @endphp
                                                <div class="min-h-[7rem] rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-2 flex flex-col justify-between" data-slot="{{ $slotKey }}" data-baby="false" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $mealLabel }}</div>
                                                    <div class="mt-2 grow relative" id="slot-{{ $slotKey }}">
                                                        @if(isset($entries[$slotKey]) && $entries[$slotKey]->dish)
                                                            <div class="rounded-lg bg-indigo-600 text-black p-2" data-dish-id="{{ $entries[$slotKey]->dish->id }}" draggable="true" ondragstart="drag(event)">{{ $entries[$slotKey]->dish->name }}</div>
                                                        @else
                                                            <div class="slot-search">
                                                                <input type="text" class="slot-search-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Rechercher un plat…" autocomplete="off" />
                                                                <ul class="slot-search-results hidden fixed z-50 max-h-36 overflow-auto rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg text-xs"></ul>
                                                            </div>
                                                        @endif
                                                        <input type="hidden" name="slots[{{ $slotKey }}][week_start]" value="{{ $weekStart }}" />
                                                        <input type="hidden" name="slots[{{ $slotKey }}][day]" value="{{ $day }}" />
                                                        <input type="hidden" name="slots[{{ $slotKey }}][meal]" value="{{ $mealKey }}" />
                                                        <input type="hidden" name="slots[{{ $slotKey }}][dish_id]" value="{{ isset($entries[$slotKey]) && $entries[$slotKey]->dish ? $entries[$slotKey]->dish->id : '' }}" />
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($hasBaby)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between gap-3 mb-1">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Planning bébé</h3>
                                    @if($babyFeedingGuide)
                                        <button
                                            type="button"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'baby-feeding-guide')"
                                            class="inline-flex items-center px-3 py-1.5 bg-pink-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-pink-500"
                                        >
                                            Recommandations
                                        </button>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Midi et goûter</p>

                                <div class="lg:overflow-x-auto lg:pb-3">
                                    <div class="space-y-3 lg:space-y-0 lg:min-w-[72rem]">
                                        <div class="hidden lg:flex gap-2 mb-2">
                                            @foreach($dayLabels as $dayLabel)
                                                <div class="flex-1 min-w-[10rem] px-2 py-1 bg-pink-100 dark:bg-pink-900/30 rounded-md text-center text-xs uppercase text-pink-600 dark:text-pink-300">{{ $dayLabel }}</div>
                                            @endforeach
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:flex lg:gap-2">
                                            @php
                                                $babyMeals = ['midi' => 'Midi', 'gouter' => 'Goûter'];
                                            @endphp
                                            @foreach($days as $day)
                                                <div class="rounded-xl border border-pink-200 dark:border-pink-800 p-3 space-y-2 lg:rounded-none lg:border-0 lg:p-0 lg:flex-1 lg:min-w-[10rem]">
                                                    <div class="lg:hidden px-2 py-1 bg-pink-100 dark:bg-pink-900/30 rounded-md text-center text-xs uppercase text-pink-600 dark:text-pink-300">{{ $dayLabels[$day] }}</div>
                                                    @foreach($babyMeals as $mealKey => $mealLabel)
                                                        @php
                                                            $slotKey = "{$day}_{$mealKey}";
                                                            $entry = $entries[$slotKey] ?? null;
                                                        @endphp
                                                        <div class="min-h-[4.5rem] rounded-xl border border-dashed border-pink-300 dark:border-pink-700 bg-pink-50 dark:bg-gray-900 p-2 flex flex-col justify-between"
                                                             data-slot="baby-{{ $slotKey }}"
                                                             data-baby="true"
                                                             ondrop="drop(event)"
                                                             ondragover="allowDrop(event)">
                                                            <div class="text-xs font-semibold text-pink-700 dark:text-pink-300">{{ $mealLabel }}</div>
                                                            <div class="mt-1 grow relative" id="slot-baby-{{ $slotKey }}">
                                                                @if($entry && $entry->babyDish)
                                                                    <div class="rounded-lg bg-pink-500 text-black p-2 text-sm"
                                                                         data-dish-id="{{ $entry->babyDish->id }}"
                                                                         draggable="true"
                                                                         ondragstart="drag(event)">
                                                                        {{ $entry->babyDish->name }}
                                                                    </div>
                                                                @else
                                                                    <div class="slot-search">
                                                                        <input type="text" class="slot-search-input w-full rounded-md border-pink-300 dark:border-pink-700 dark:bg-gray-800 dark:text-gray-100 text-xs shadow-sm focus:border-pink-500 focus:ring-pink-500" placeholder="Rechercher un plat bébé…" autocomplete="off" />
                                                                        <ul class="slot-search-results hidden fixed z-50 max-h-36 overflow-auto rounded-md border border-pink-200 dark:border-pink-700 bg-white dark:bg-gray-800 shadow-lg text-xs"></ul>
                                                                    </div>
                                                                @endif
                                                                @if($mealKey === 'gouter')
                                                                    <input type="hidden" name="slots[{{ $slotKey }}][week_start]" value="{{ $weekStart }}" />
                                                                    <input type="hidden" name="slots[{{ $slotKey }}][day]" value="{{ $day }}" />
                                                                    <input type="hidden" name="slots[{{ $slotKey }}][meal]" value="{{ $mealKey }}" />
                                                                @endif
                                                                <input type="hidden" name="slots[{{ $slotKey }}][baby_dish_id]" value="{{ $entry && $entry->babyDish ? $entry->babyDish->id : '' }}" />
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 space-y-4">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Plats disponibles</h3>
                        <div class="space-y-2">
                            @forelse($dishes as $dish)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-3" draggable="true" data-dish-id="{{ $dish->id }}" ondragstart="drag(event)">
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100 min-w-0 break-words">{{ $dish->name }}</div>
                                    <div class="text-xs text-gray-500 shrink-0">{{ $dish->ingredients->count() }} ingr.</div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Aucun plat défini. Ajoutez-en sur la page Plats.</div>
                            @endforelse
                        </div>
                    </div>

                    @if($hasBaby)
                        <div class="rounded-lg border border-pink-200 dark:border-pink-800 p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Plats bébé disponibles</h3>
                            <div class="space-y-2">
                                @forelse($babyDishes as $dish)
                                    <div class="flex items-center justify-between gap-3 rounded-lg border border-pink-200 dark:border-pink-800 bg-pink-50 dark:bg-gray-900 p-3" draggable="true" data-dish-id="{{ $dish->id }}" ondragstart="drag(event)">
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100 min-w-0 break-words">{{ $dish->name }}</div>
                                        <div class="text-xs text-gray-500 shrink-0">{{ $dish->ingredients->count() }} ingr.</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">Aucun plat bébé. Cochez « Plat bébé » lors de la création d'un plat.</div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Liste de courses</h3>
                        <div class="space-y-2">
                            @forelse($shopping as $item)
                                <label class="flex items-start gap-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-2">
                                    <input type="checkbox" class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 border-gray-300 rounded" />
                                    <span class="text-sm text-gray-700 dark:text-gray-200 break-words">{{ $item['name'] }} — Quantité : {{ $item['quantity'] }} - Volume unitaire : {{ $item['unit'] }}</span>
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
        const planDishes = @json($dishes->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values());
        const planBabyDishes = @json($babyDishes->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values());

        function allowDrop(event) {
            event.preventDefault();
        }

        function isBabySlot(slot) {
            return slot.dataset.baby === 'true';
        }

        function slotContent(slot) {
            return isBabySlot(slot)
                ? slot.querySelector('[id^="slot-baby-"]')
                : slot.querySelector('[id^="slot-"]:not([id^="slot-baby-"])');
        }

        function dishFieldName(slot) {
            return isBabySlot(slot) ? 'baby_dish_id' : 'dish_id';
        }

        function dishesForSlot(slot) {
            return isBabySlot(slot) ? planBabyDishes : planDishes;
        }

        function searchPlaceholder(slot) {
            return isBabySlot(slot) ? 'Rechercher un plat bébé…' : 'Rechercher un plat…';
        }

        function searchInputClass(slot) {
            return isBabySlot(slot)
                ? 'slot-search-input w-full rounded-md border-pink-300 dark:border-pink-700 dark:bg-gray-800 dark:text-gray-100 text-xs shadow-sm focus:border-pink-500 focus:ring-pink-500'
                : 'slot-search-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
        }

        function searchResultsClass(slot) {
            return isBabySlot(slot)
                ? 'slot-search-results hidden fixed z-50 max-h-36 overflow-auto rounded-md border border-pink-200 dark:border-pink-700 bg-white dark:bg-gray-800 shadow-lg text-xs'
                : 'slot-search-results hidden fixed z-50 max-h-36 overflow-auto rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg text-xs';
        }

        let activeSlotSearch = null;

        function positionSearchResults(input, results) {
            const rect = input.getBoundingClientRect();
            const maxHeight = 144;
            const spaceBelow = window.innerHeight - rect.bottom - 8;
            const spaceAbove = rect.top - 8;
            const openUp = spaceBelow < Math.min(maxHeight, 120) && spaceAbove > spaceBelow;

            results.style.left = `${rect.left}px`;
            results.style.width = `${Math.max(rect.width, 140)}px`;
            results.style.right = 'auto';

            if (openUp) {
                results.style.top = 'auto';
                results.style.bottom = `${window.innerHeight - rect.top + 4}px`;
                results.style.maxHeight = `${Math.min(maxHeight, spaceAbove)}px`;
            } else {
                results.style.bottom = 'auto';
                results.style.top = `${rect.bottom + 4}px`;
                results.style.maxHeight = `${Math.min(maxHeight, Math.max(spaceBelow, 80))}px`;
            }
        }

        function syncActiveSlotSearchPosition() {
            if (!activeSlotSearch) {
                return;
            }

            const { input, results } = activeSlotSearch;
            if (!input.isConnected || results.classList.contains('hidden')) {
                activeSlotSearch = null;
                return;
            }

            positionSearchResults(input, results);
        }

        function cardClass(slot) {
            return isBabySlot(slot)
                ? 'rounded-lg bg-pink-500 text-black p-2 text-sm'
                : 'rounded-lg bg-indigo-600 text-black p-2';
        }

        function slotHasDish(slot) {
            const content = slotContent(slot);
            return Boolean(content?.querySelector('[data-dish-id]'));
        }

        function createSlotSearch(slot) {
            const wrap = document.createElement('div');
            wrap.className = 'slot-search';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = searchInputClass(slot);
            input.placeholder = searchPlaceholder(slot);
            input.autocomplete = 'off';

            const results = document.createElement('ul');
            results.className = searchResultsClass(slot);

            wrap.appendChild(input);
            wrap.appendChild(results);
            return wrap;
        }

        function hideSearchResults(results) {
            if (!results) return;
            if (activeSlotSearch?.results === results) {
                activeSlotSearch = null;
            }
            results.classList.add('hidden');
            results.innerHTML = '';
            results.style.left = '';
            results.style.top = '';
            results.style.bottom = '';
            results.style.width = '';
            results.style.maxHeight = '';
            if (results.dataset.portal === 'true' && results.parentElement === document.body) {
                const hostId = results.dataset.hostId;
                const host = hostId ? document.getElementById(hostId) : null;
                if (host) {
                    host.appendChild(results);
                }
                delete results.dataset.portal;
                delete results.dataset.hostId;
            }
        }

        function renderSearchResults(slot, input, results, query) {
            const dishes = dishesForSlot(slot);
            const q = query.trim().toLowerCase();
            const matches = q
                ? dishes.filter((d) => d.name.toLowerCase().includes(q))
                : dishes.slice(0, 8);

            const search = input.closest('.slot-search');
            if (search && !search.id) {
                search.id = `slot-search-${Math.random().toString(36).slice(2, 9)}`;
            }

            if (results.parentElement !== document.body) {
                results.dataset.portal = 'true';
                results.dataset.hostId = search.id;
                document.body.appendChild(results);
            }

            results.innerHTML = '';

            if (!matches.length) {
                const empty = document.createElement('li');
                empty.className = 'px-2 py-1.5 text-gray-500 dark:text-gray-400';
                empty.textContent = 'Aucun plat trouvé';
                results.appendChild(empty);
            } else {
                matches.forEach((dish) => {
                    const li = document.createElement('li');
                    li.className = 'px-2 py-1.5 cursor-pointer text-gray-800 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700';
                    li.textContent = dish.name;
                    li.addEventListener('mousedown', (event) => {
                        event.preventDefault();
                        assignDishToSlot(slot, dish.id, dish.name);
                        notifyModification();
                    });
                    results.appendChild(li);
                });
            }

            activeSlotSearch = { input, results };
            positionSearchResults(input, results);
            results.classList.remove('hidden');
        }

        function bindSlotSearch(slot) {
            const content = slotContent(slot);
            const search = content?.querySelector('.slot-search');
            if (!search || search.dataset.bound === 'true') {
                return;
            }

            const input = search.querySelector('.slot-search-input');
            const results = search.querySelector('.slot-search-results');
            if (!input || !results) {
                return;
            }

            search.dataset.bound = 'true';

            const show = () => renderSearchResults(slot, input, results, input.value);

            input.addEventListener('focus', show);
            input.addEventListener('input', show);

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    hideSearchResults(results);
                    input.blur();
                }
            });

            input.addEventListener('blur', () => {
                setTimeout(() => hideSearchResults(results), 150);
            });
        }

        function clearSlot(slot, showSearch = false) {
            const content = slotContent(slot);
            if (!content) {
                return;
            }

            content.querySelector('[data-dish-id]')?.remove();

            const search = content.querySelector('.slot-search');
            if (search) {
                const portalResults = search.id
                    ? document.querySelector(`.slot-search-results[data-host-id="${search.id}"]`)
                    : null;
                if (portalResults) {
                    if (activeSlotSearch?.results === portalResults) {
                        activeSlotSearch = null;
                    }
                    portalResults.remove();
                }
                search.remove();
            }

            content.querySelector('.slot-placeholder')?.remove();

            const field = dishFieldName(slot);
            const hidden = content.querySelector(`input[name$="[${field}]"]`);
            if (hidden) {
                hidden.value = '';
            }

            if (showSearch && !content.querySelector('[data-dish-id]')) {
                const nextSearch = createSlotSearch(slot);
                if (hidden) {
                    hidden.before(nextSearch);
                } else {
                    content.appendChild(nextSearch);
                }
                bindSlotSearch(slot);
            }
        }

        function assignDishToSlot(slot, dishId, dishName) {
            clearSlot(slot, false);

            const content = slotContent(slot);
            const card = document.createElement('div');
            card.className = cardClass(slot);
            card.textContent = dishName;
            card.dataset.dishId = String(dishId);
            card.draggable = true;
            card.ondragstart = drag;

            const field = dishFieldName(slot);
            const hidden = content.querySelector(`input[name$="[${field}]"]`);
            if (hidden) {
                hidden.before(card);
                hidden.value = dishId;
            } else {
                content.appendChild(card);
            }
        }

        function notifyModification() {
            document.getElementById('save-plan').textContent = 'Enregistrer le planning*';
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

            // Un seul plat par slot : refuse le drop si déjà occupé (sauf déplacement depuis ce même slot)
            const sourceSlotEl = data.sourceSlot ? document.querySelector(`[data-slot="${data.sourceSlot}"]`) : null;
            if (slotHasDish(slot) && sourceSlotEl !== slot) {
                return;
            }

            if (sourceSlotEl && sourceSlotEl !== slot) {
                clearSlot(sourceSlotEl, true);
            }

            assignDishToSlot(slot, data.dishId, data.dishName);
            notifyModification();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-slot]').forEach(bindSlotSearch);

            // Capture aussi le scroll des conteneurs overflow-x-auto
            window.addEventListener('scroll', syncActiveSlotSearchPosition, true);
            window.addEventListener('resize', syncActiveSlotSearchPosition);

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
                    notifyModification();
                }
            });

            const dateInput = document.querySelector('input[type="date"][name="week_start"]');
            if (!dateInput) return;

            dateInput.addEventListener('change', function () {
                const selected = this.value;
                if (!selected) return;
                const url = new URL(window.location.href);
                url.searchParams.set('week_start', selected);
                window.location.href = url.pathname + (url.search ? ('?' + url.searchParams.toString()) : '');
            });
        });
    </script>

    @if($hasBaby && $babyFeedingGuide)
        <x-modal name="baby-feeding-guide" maxWidth="xl">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recommandations alimentaires</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Âge du bébé : <span class="font-medium text-gray-900 dark:text-gray-100">{{ $babyFeedingGuide['age_label'] }}</span>
                            — {{ $babyFeedingGuide['stage_title'] }}
                        </p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close-modal', 'baby-feeding-guide')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">Fermer</button>
                </div>

                <div class="mt-5 space-y-4">
                    @if(!empty($babyFeedingGuide['portions']))
                        <div class="rounded-lg border border-pink-200 dark:border-pink-800 bg-pink-50 dark:bg-pink-950/30 p-4">
                            <h3 class="text-sm font-semibold text-pink-800 dark:text-pink-200">Quantités à viser</h3>
                            <p class="mt-1 text-xs text-pink-700/80 dark:text-pink-300/80">Repères pour guider les portions ; adaptez à l’appétit de bébé.</p>
                            <dl class="mt-3 space-y-3">
                                @foreach($babyFeedingGuide['portions'] as $portion)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $portion['label'] }}</dt>
                                        <dd class="mt-0.5 text-sm text-gray-700 dark:text-gray-200">{{ $portion['amount'] }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif

                    @if($babyFeedingGuide['protein'])
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Protéines (viande, poisson, œuf)</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $babyFeedingGuide['protein'] }}</p>
                        </div>
                    @endif

                    @if($babyFeedingGuide['dairy'])
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Lait et produits laitiers</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $babyFeedingGuide['dairy'] }}</p>
                        </div>
                    @endif

                    @if($babyFeedingGuide['textures'])
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Textures</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $babyFeedingGuide['textures'] }}</p>
                        </div>
                    @endif

                    @if(!empty($babyFeedingGuide['tips']))
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Comment procéder</h3>
                            <ul class="mt-2 list-disc list-inside space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                @foreach($babyFeedingGuide['tips'] as $tip)
                                    <li>{{ $tip }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-700">
                        Indications générales jusqu’aux 2 ans du bébé. Adaptez toujours selon l’avis de votre pédiatre.
                    </p>
                </div>
            </div>
        </x-modal>
    @endif
</x-app-layout>
