<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;


    protected $fillable = [
        'init_quantity',
        'sold_quantity',
        'in_stock',
        'priority',
    ];




}
