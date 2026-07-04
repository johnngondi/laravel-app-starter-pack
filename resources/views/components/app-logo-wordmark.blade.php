{{--
    Wegon AI horizontal wordmark for the auth + onboarding pages. These pages
    follow the user's theme (@fluxAppearance), so we swap between the navy
    variant (legible on light backgrounds) and the white variant (for dark)
    using Tailwind's `dark:` toggle. Callers supply the sizing (e.g. h-12 w-auto).
--}}
<img
    src="{{ asset('WegonAILogo/wegon-ai-horizontal-light.svg') }}"
    alt="{{ config('app.name', 'Wegon AI') }}"
    {{ $attributes->merge(['class' => 'block dark:hidden']) }}
/>
<img
    src="{{ asset('WegonAILogo/wegon-ai-horizontal.svg') }}"
    alt="{{ config('app.name', 'Wegon AI') }}"
    {{ $attributes->merge(['class' => 'hidden dark:block']) }}
/>
