<a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-outline">Ver</a>

@if(auth()->user()->isAdmin() && $return->status === 'pending')
    <form class="inline" action="{{ route('returns.confirmReturn', $return) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-success ml-2">Marcar como devolvido</button>
    </form>
@endif