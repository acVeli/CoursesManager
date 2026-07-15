<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Baby planning -->
        <div class="mt-4" x-data="{ hasBaby: {{ old('has_baby') == '1' ? 'true' : 'false' }} }">
            <x-input-label :value="__('Avez-vous un bébé ?')" />
            <div class="mt-2 flex gap-6">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="radio" name="has_baby" value="1" @change="hasBaby = true" @checked(old('has_baby') == '1') class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    Oui
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="radio" name="has_baby" value="0" @change="hasBaby = false" @checked(old('has_baby', '0') == '0') required class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    Non
                </label>
            </div>
            <x-input-error :messages="$errors->get('has_baby')" class="mt-2" />

            <div class="mt-4" x-show="hasBaby" x-cloak style="display: none;">
                <x-input-label for="baby_birth_date" :value="__('Date de naissance du bébé')" />
                <x-text-input id="baby_birth_date" class="block mt-1 w-full" type="date" name="baby_birth_date" :value="old('baby_birth_date')" max="{{ now()->toDateString() }}" x-bind:required="hasBaby" />
                <x-input-error :messages="$errors->get('baby_birth_date')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
