@props(['perPage' => 15])

<div class="flex items-center gap-3">
    <label for="perPage" class="text-sm font-medium text-gray-700 dark:text-gray-300">
        Itens por página:
    </label>
    <select id="perPage" name="perPage"
        onchange="
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            window.location = url.toString();
        "
        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
    </select>
</div>
