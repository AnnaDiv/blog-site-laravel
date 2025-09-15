<x-layout>
<?php
$columns = 4;
$total = count($categories);
$rows = ceil($total / $columns);

// Prepare desktop (column-major) grid
$column_major = [];
for ($i = 0; $i < $rows; $i++) {
    for ($j = 0; $j < $columns; $j++) {
        $index = $i + $j * $rows;
        $column_major[$i][$j] = $categories[$index] ?? null;
    }
}
?>
<div class="top-bar">
    <div class="search-bar">
        <form method="GET" action="{{ route('categories.search') }}">
            @csrf
            <input type="text" pattern=".{3,}" name="search_q"
                   value="{{ old('search_q') }}" required pattern=".{3,}"
                   required title="3 characters minimum" placeholder="Search categories..." />
            <button type="submit" class="user-search-btn">Search Categories</button>
        </form>
    </div>
</div>

<!-- Desktop layout -->
<div class="category-grid-desktop">
    <?php foreach ($column_major as $row): ?>
        <div class="category-row">
            <?php foreach ($row as $category): ?>
                <div class="category-cell">
                    <?php if ($category): ?>
                        <a href="index.php?<?php echo http_build_query([
                            'route' => 'client',
                            'pages' => 'category',
                            'category' => $category->title,
                            'page' => 1
                        ]); ?>">
                            {{ $category->title }}
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Mobile layout -->
<div class="category-grid-mobile">
    <?php foreach ($categories as $category): ?>
        <div class="category-cell">
            <a href="index.php?<?php echo http_build_query([
                'route' => 'client',
                'pages' => 'category',
                'category' => $category->title,
                'page' => 1
            ]); ?>">
                {{ $category->title }}
            </a>
        </div>
    <?php endforeach; ?>
</div>

{{ $categories->links() }}
</x-layout>