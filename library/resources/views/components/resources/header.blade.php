@props([
    'title' => '',
    'createRoute' => '',
    'createLabel' => 'Novo',
    'exportRoute' => '',
    'createExport' => 'Exportar',
])
    <div  class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $title }}</h1>
    <div class="flex items-center gap-3">
         @if($createRoute)
            <a href="{{ $createRoute }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> {{ $createLabel }}
            </a>
        @endif

     @if($exportRoute)
        <a href="{{ $exportRoute }}" class="btn btn-outline">
            <i class="fas fa-file-excel mr-2"></i> {{ $createExport }}
        </a>
    @endif
    </div>
</div>