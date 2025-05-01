@props(['package', 'delay' => 0])

<article {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:-translate-y-1']) }} data-aos="fade-up" data-aos-delay="{{ $delay }}">
    <header>
        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ $package->name }}
        </h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6 text-base sm:text-lg min-h-[4.5rem]">
            {{ $package->description ?? 'Опис відсутній' }}
        </p>
    </header>
    <ul class="space-y-3 mb-6 text-gray-700 dark:text-gray-200 text-sm sm:text-base">
        @foreach ($package->services as $service)
            <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span>{{ $service->name }}</span>
            </li>
        @endforeach
    </ul>
    <footer class="mt-auto">
        <p class="text-lg sm:text-xl font-semibold text-emerald-600 dark:text-emerald-400 mb-6">
            Від {{ number_format($package->calculatePrice(), 0, ',', ' ') }} грн
        </p>
        <a href="{{ route('packages.show', $package->id) }}" 
           class="block text-center bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200">
            Обрати пакет
        </a>
    </footer>
</article>