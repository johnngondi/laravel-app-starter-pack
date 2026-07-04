<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <x-input
                name="email"
                value="{{ request('email') }}"
                type="email"
                required
                autocomplete="email"
            >
                <x-slot:label><x-required-label>{{ __('Email') }}</x-required-label></x-slot:label>
            </x-input>

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
                <x-submit-button class="w-full" data-test="reset-password-button">
                    {{ __('Reset password') }}
                </x-submit-button>
            </div>
        </form>
    </div>
</x-layouts::auth>
