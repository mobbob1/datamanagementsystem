<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Scan QR/Barcode</h2>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
        <p class="text-sm text-gray-600 mb-4">
            Use a barcode/QR scanner (acts like a keyboard) to scan into the field below, or paste/type the document number.
        </p>
        <form method="GET" action="{{ route('scan') }}" class="flex items-center gap-2">
            <x-text-input name="code" value="{{ request('code') }}" class="w-full" placeholder="Scan or type document number" autofocus />
            <x-primary-button>Find</x-primary-button>
        </form>
        <p class="text-xs text-gray-500 mt-3">Scanning will redirect to the document if found, otherwise it will perform a search.</p>
    </div>
</x-app-layout>
