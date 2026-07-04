<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            <!-- Profile photo (optional) -->
            <x-avatar-upload :label="__('Profile photo')" name="photo" />

            <!-- Name -->
            <x-input
                name="name"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            >
                <x-slot:label><x-required-label>{{ __('Name') }}</x-required-label></x-slot:label>
            </x-input>

            <!-- Email Address -->
            <x-input
                name="email"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            >
                <x-slot:label><x-required-label>{{ __('Email address') }}</x-required-label></x-slot:label>
            </x-input>

            <!-- Phone -->
            <x-phone-field
                :label="__('Phone')"
                required
                name="phone"
                :value="old('phone')"
                code-name="phone_country_code"
                :selected-code="old('phone_country_code', '254')"
            />

            <!-- Tax PIN (optional) -->
            <x-input
                name="tax_pin"
                :label="__('Tax PIN')"
                :value="old('tax_pin')"
                type="text"
                autocomplete="off"
                :placeholder="__('Optional — used for billing')"
            />

            <!-- Password -->
            <x-password
                name="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            >
                <x-slot:label><x-required-label>{{ __('Password') }}</x-required-label></x-slot:label>
            </x-password>

            <!-- Confirm Password -->
            <x-password
                name="password_confirmation"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            >
                <x-slot:label><x-required-label>{{ __('Confirm password') }}</x-required-label></x-slot:label>
            </x-password>

            <div class="flex items-center justify-end">
                <x-submit-button class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </x-submit-button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
