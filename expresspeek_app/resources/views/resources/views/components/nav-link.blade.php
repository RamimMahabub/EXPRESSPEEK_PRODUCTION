@props(['active' => false, 'icon' => null])

@php($iconContent = trim((string) ($icon ?? '')))

<a {{ $attributes }}
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm mb-0.5 transition-all duration-200
          {{ $active
             ? 'bg-violet-600/20 text-violet-300 border border-violet-600/30 font-medium'
             : 'text-slate-400 hover:bg-gray-800 hover:text-gray-200' }}">

    @if($iconContent !== '')
        <svg class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-violet-400' : 'text-slate-400' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $iconContent !!}
        </svg>
    @endif

    {{ $slot }}
</a>
