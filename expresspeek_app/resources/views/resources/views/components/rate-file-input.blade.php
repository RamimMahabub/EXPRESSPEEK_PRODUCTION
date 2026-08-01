@props([
    'id',
    'name',
    'label',
    'required' => false,
    'color' => 'emerald',
])

@php
$colorMap = [
    'yellow' => 'file:bg-yellow-600 hover:file:bg-yellow-500',
    'blue'   => 'file:bg-blue-600 hover:file:bg-blue-500',
    'orange' => 'file:bg-orange-600 hover:file:bg-orange-500',
    'amber'  => 'file:bg-amber-600 hover:file:bg-amber-500',
    'cyan'   => 'file:bg-cyan-600 hover:file:bg-cyan-500',
    'rose'   => 'file:bg-rose-600 hover:file:bg-rose-500',
    'purple' => 'file:bg-purple-600 hover:file:bg-purple-500',
    'emerald'=> 'file:bg-emerald-600 hover:file:bg-emerald-500',
];
$btnClasses = $colorMap[$color] ?? $colorMap['emerald'];
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-300">
        {{ $label }}
        @if($required)<span class="text-red-400 ml-0.5">*</span>@endif
    </label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="file"
        accept=".xlsx"
        {{ $required ? 'required' : '' }}
        class="mt-2 block w-full rounded-xl border border-gray-700 bg-gray-800 text-sm text-slate-400
               file:mr-4 file:rounded-lg file:border-0 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white
               {{ $btnClasses }} transition-colors"
    >
    @error($name)
        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
