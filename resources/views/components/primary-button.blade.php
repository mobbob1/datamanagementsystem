<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-orc-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orc-teal focus:bg-orc-teal active:bg-orc-navy focus:outline-none focus:ring-2 focus:ring-orc-teal focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
