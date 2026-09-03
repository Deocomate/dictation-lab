@props(['class' => ''])

<div {{ $attributes->merge([
    'class' => 'w-full max-w-full bg-white border border-border-light rounded-xl p-5 sm:p-6 shadow-card '.$class,
]) }}>
    {{ $slot }}
</div>
