<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;
    //relation inverse de commande et produit
    public function ProduitCommande()
    {
        return $this->belongsTo(Product::class);
    }
    //relation inverse de user et commande
    public function Usercommande()
    {
        return $this->belongsTo(User::class);
    }
}
