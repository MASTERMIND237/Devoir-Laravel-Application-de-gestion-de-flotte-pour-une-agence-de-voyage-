<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[
        'code',
        'nom',
        'quantite',
        'prix',
        'description'
    ];

    public function commandeProduit()
    {
        return $this->hasOne(Commande::class);
    }
    public function categorie(){
        return $this->belongsToMany(Categorie::class,'product_categories');
    }
}
