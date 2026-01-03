@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md shadow-sm']) !!}>
