@props([
    'columns' => [],
    'items' => [],
    'emptyMessage' => 'Nenhum registro encontrado',
    'actions' => ['show', 'edit', 'delete'],
    'routePrefix' => ''
])

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
    <table class="table table-zebra w-full">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>
                        @if($column['sortable'] ?? false)
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => $column['sortKey'] ?? $column['key'],
                                'direction' => request('sort') === ($column['sortKey'] ?? $column['key']) 
                                    ? (request('direction') === 'asc' ? 'desc' : 'asc')
                                    : 'asc'
                            ]) }}" class="flex items-center">
                                {{ $column['label'] }}
                                @if(request('sort') === ($column['sortKey'] ?? $column['key']))
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </a>
                        @else
                            {{ $column['label'] }}
                        @endif
                    </th>
                @endforeach
                
                @if(!empty($actions))
                    <th>Ações</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    @foreach($columns as $column)
                        <td>
                            @if(isset($column['format']) && is_callable($column['format']))
                                {!! $column['format']($item) !!}
                            @else
                                {{ $item->{$column['key']} }}
                            @endif
                        </td>
                    @endforeach
                    
                    @if(!empty($actions))
                        <td class="flex space-x-2">
                            @if(in_array('show', $actions))
                                <a href="{{ route("{$routePrefix}.show", $item) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                            @if(in_array('edit', $actions))
                                <a href="{{ route("{$routePrefix}.edit", $item) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if(in_array('delete', $actions))
                                <form action="{{ route("{$routePrefix}.destroy", $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Tem certeza?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + (!empty($actions) ? 1 : 0) }}" class="text-center py-4">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>