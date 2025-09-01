<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {

        $cart = Auth::user()//recupera o user logado
            ->cart()//chama o relacionamento (se ele tem um carrinho)
            ->with('items.book')//carrega os items do carrinho e livro associado a cada item
            ->firstOrCreate();//garante que se tiver só busca se n tiver cria um cart

        return view('cart.index', compact('cart'));
    }

    public function add(Book $book)
    {
        $cart = Auth::user()->cart()->firstOrCreate(); //cria ou relaciona o user a um carrinho

        //lista de items
        //verifica se ja existe esse livro no carrinho
        $item = $cart->items->where('book_id', $book->id)->first();
        //se ja existe apenas incrementa a quantity
        if ($item) {
            $item->quantity += 1;
            $item->save();
        } else { //se n cria um novo cardItem
            $cart->items()->create([
                'book_id' => $book->id,
                'quantity' => 1,
                'price' => $book->price ?? 0,
            ]);
        }

        return redirect()->back()->with('success', 'Livro adicionado ao carrinho!');
    }


    public function updateQuantity(Request $request, CartItem $item)
    {
        $item->update([
            'quatity' => $request->quantity,
        ]);

        return with('success', 'Quantidade atualizada.');
    }

    public function remove(CartItem $item)
    {

        $item->delete();

        return with('success', 'Item removido do carrinho');
    }
}
