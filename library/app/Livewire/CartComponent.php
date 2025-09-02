<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartComponent extends Component
{

    public $cart;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = Auth::user()
            ->cart()
            ->with('items.book.authors')
            ->first();
    }
    public function incrementQuantity($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item) {
            $item->increment('quantity');
            $this->loadCart();
        }
    }

    public function decrementQuantity($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->loadCart();
        }
    }

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item) {
            $item->delete();
            $this->loadCart();
        }
    }

    public function render()
    {
        return view('livewire.cart-component');
    }
}
