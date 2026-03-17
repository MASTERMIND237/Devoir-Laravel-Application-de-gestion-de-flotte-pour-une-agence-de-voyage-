<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Details du produit</h1>
    <table>
            <tr>
                <th>Code</th>
                <td> {{ $produit->code }} </td>
            </tr>
            <tr>
                <th>Nom</th>
                <td> {{ $produit->nom }} </td>
            </tr>
            <tr>
                <th>Quantite</th>
                <td> {{ $produit->quantite }} </td>
            </tr>
            <tr>
                <th>Prix</th>
                <td> {{ $produit->prix }} Fcfa </td>
            </tr>
            <tr>
                <th>Description</th>
                <td> {{ $produit->description }} </td>
            </tr>
            <tr>
                <th>Date de creation</th>
                <td> {{ $produit->created_at }} </td>
            </tr>
            <tr>
                <th>Date de derniere modificetion</th>
                <td> {{ $produit->updated_at }} </td>
            </tr>
        </table>
        <br/>
        <a href="{{ route('produits.edit', $produit->id) }}">
            <button>Modifier</button>
        </a>
        <form action="{{ route('produits.destroy', $produit->id) }}" method="POST">
            @csrf
            @method('DELETE')
                <button type = "submit" onclick="return confirm('etes vous sur de vouloir supprimer ce produit?')">Suppri;er</button>
        </form>

</body>
</html>
