@props([
    'columns' => [],      // Array de colunas ['Título' => 'book.title', 'Data' => 'return_date', ...]
    'rows' => [],         // Array de objetos/arrays com os dados
    'actions' => null     // Callback para renderizar ações
])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $header => $key)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ is_numeric($header) ? $key : $header }}
                        </th>
                    @endforeach
                    @if($actions)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rows as $row)
                    <tr>
                        @foreach($columns as $key)
                            @php
                                $value = is_numeric($key) ? data_get($row, $key) : (is_callable($key) ? $key($row) : data_get($row, $key));

                                // Formata datas automaticamente
                                if($value instanceof \Illuminate\Support\Carbon) {
                                    $value = $value->format('d/m/Y');
                                }

                                // Badge para status
                                if(data_get($row, 'status') && $key === 'status') {
                                    $statusClass = match(data_get($row, 'status')) {
                                        'approved' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'pending_returned' => 'badge-warning',
                                        'returned' => 'badge-info',
                                        'rejected' => 'badge-error',
                                        default => 'badge-secondary',
                                    };
                                    $value = '<span class="badge ' . $statusClass . '">' . ucfirst(data_get($row, 'status')) . '</span>';
                                }
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $value !!}
                            </td>
                        @endforeach

                        @if($actions)
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $actions($row) !!}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + ($actions ? 1 : 0) }}" class="px-6 py-4 text-center">
                            Nenhum registro encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
