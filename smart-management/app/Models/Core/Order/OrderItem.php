<?php

namespace App\Models\Core\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Order\Order;
use App\Models\Core\Article;
use App\Models\Core\Entity;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'article_id',
        'supplier_id',
        'quantity',
        'unit_price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

}
