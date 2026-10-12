@if($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex justify-center">
        <ul class="flex flex-wrap items-center justify-center gap-2">
            {{-- Previous Page Link --}}
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous"
                    class="inline-flex w-10 h-10 items-center justify-center border border-gray-300 bg-white text-gray-700 text-sm rounded-lg transition
                           {{ $paginator->onFirstPage() ? 'pointer-events-none opacity-40' : 'hover:border-black hover:text-black' }}">&lt;</a>
            </li>

            {{-- Pagination Elements --}}
            @foreach($elements as $element)
                @if(is_string($element))
                    <li class="inline-flex w-10 h-10 items-center justify-center border border-gray-200 bg-white text-gray-400 text-sm rounded-lg">{{ $element }}</li>
                @endif

                @if(is_array($element))
                    @foreach($element as $page => $url)
                        <li>
                            <a href="{{ $url }}"
                                class="inline-flex w-10 h-10 items-center justify-center border text-sm transition rounded-lg
                                       {{ $page === $paginator->currentPage()
                                            ? 'bg-black text-white font-bold border-black'
                                            : 'bg-white border-gray-300 text-gray-700 hover:border-black hover:text-black' }}">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next"
                    class="inline-flex w-10 h-10 items-center justify-center border border-gray-300 bg-white text-gray-700 text-sm rounded-lg transition
                           {{ $paginator->hasMorePages() ? 'hover:border-black hover:text-black' : 'pointer-events-none opacity-40' }}">&gt;</a>
            </li>
        </ul>
    </nav>
@endif