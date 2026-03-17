<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> qjouter produit</title>
</head>
<body>
    <h1>Creation des Produits</h1>
    <form action="{{ route('produits.store') }}" method="POST">
        @csrf
        <p>
            <label>Code du produit</label><br/>
            <input type="text" name="code" value="{{ old('code') }}">
            @error('code')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>Nom du produit</label><br/>
            <input type="text" name="nom" value="{{ old('nom') }}">
            @error('nom')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>quantite du produit</label><br/>
            <input type="text" name="quantite" value="{{ old('quantite') }}">
            @error('quantite')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>prix du produit</label><br/>
            <input type="text" name="prix" value="{{ old('prix') }}">
            @error('prix')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>Description du produit</label><br/>
            <textarea name="description" value="{{ old('description') }}"></textarea>
            @error('description')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <button type = "submit">Enregistrer</button>
            <a href="{{ route('produits.index') }}">
                <button type = "button">annuler</button>
            </a>
        </p>
    </form>
</body>
</html>
