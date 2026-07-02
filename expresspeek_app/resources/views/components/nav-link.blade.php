@props(['active' => false, 'icon' => null])

@php($iconContent = trim((string) ($icon ?? '')))

<a {{ $attributes }}
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm mb-0.5 transition-all duration-200
          {{ $active
             ? 'bg-violet-50 text-violet-700 border border-violet-200 font-semibold shadow-sm shadow-violet-100/60'
             : 'text-slate-600 border border-transparent hover:bg-slate-50 hover:text-slate-900' }}">

    @if($iconContent !== '')
        <svg class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-violet-600' : 'text-slate-400' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $iconContent !!}
        </svg>
    @endif

    {{ $slot }}
</a>
