<section class="mt-10 space-y-6 border border-red-500 p-3 rounded-md">
    <div class="relative mb-5 text-red-600">
        <flux:heading>{{ __('Delete account') }}</flux:heading>
        <flux:subheading>{{ __('Delete your account and all of your organisations and data') }}.</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <x-button negative x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Delete account') }}
        </x-button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure you want to delete your account?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Once your account is deleted, all of you organisations and their resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </flux:subheading>
            </div>

            <x-password wire:model="password" :label="__('Password')" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <x-button secondary>{{ __('Cancel') }}</x-button>
                </flux:modal.close>

                <x-button negative type="submit" spinner="deleteUser">{{ __('Delete account') }}</x-button>
            </div>
        </form>
    </flux:modal>
</section>
