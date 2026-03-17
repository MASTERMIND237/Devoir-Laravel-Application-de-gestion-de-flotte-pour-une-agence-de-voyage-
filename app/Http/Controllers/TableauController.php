<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableauController extends Controller
{
    //methode de base qui affiche un tableau
    public function afficheTableau()
    {
        // definir un tableau simple à afficher

        $table = [
            ['Nom' => 'Alice', 'Age' => 25],
            ['Nom' => 'Bob', 'Age' => 30],
            ['Nom' => 'Charlie', 'Age' => 35],
        ];

        //qfficher le tableau

        return view('table.index', ['table' => $table]);

    }
}
