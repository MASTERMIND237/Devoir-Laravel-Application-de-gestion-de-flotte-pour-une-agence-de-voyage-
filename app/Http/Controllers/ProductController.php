<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // gestion de l'utilisateur qui effectue 1 ou plusieurs commandes relation hasMany
        /*$user = User::find(1); // recherhe d'un utilisateur dont l'id est 1
        //$user->commande;  //on accede qux commandes de l'utilisateur.
        foreach($user->commande as $c)
            {
                echo($c->nom);
            }
        //gestion du produit qui est contenu dqns une seule commande relation hasOne
        $produit = Product::find(1);
            echo($produit->commandeProduit->nom);

        // un produit appartient a une categorie

        $categories = Product::with('categorie')->get();
        foreach ($categories->$categorie as $categories) {
            echo($categorie->nom);
            foreach($cattegorie->$product as $product){
                echo($product->nom);
            }
        }*/

        // la relation belongsToMany — charger les produits avec leur categorie
        $produits = Product::with('categorie')->get();

        return view('products.index', [
            'produits' => $produits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():View
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->all());
        return redirect()->route('produits.index')
            ->withSuccess('Nouveau produit ajoute avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $produit): View
    {
        return view('products.show',[
            'produit'=>$produit
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $produit): View
    {
        return view('products.edit',[
            'produit'=>$produit
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $produit): RedirectResponse
    {
        $produit->update($request->all());

        return redirect()->back()
                ->withSuccess('Produit mis a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $produit): RedirectResponse
    {
        $produit->delete();

        return redirect()->route('produits.index')
                ->withSuccess('Produit supprime aevc succes. ');
    }
}
