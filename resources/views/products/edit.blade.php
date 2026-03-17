<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>;odifier des produits</title>
</head>
<body>
    <h1>modifier le Produits</h1>

    @if (session('sucess'))
        <p>
            {{ session('success') }}
        </p>

    @endif
    <form action="{{ route('produits.update', $produit->id) }}" method="POST">
        @csrf
        @method('PUT')
        <p>
            <label>Code du produit</label><br/>
            <input type="text" name="code" value="{{ old('code', $produit->code) }}">
            @error('code')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>Nom du produit</label><br/>
            <input type="text" name="nom" value="{{ old('nom', $produit->nom) }}">
            @error('nom')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>quantite du produit</label><br/>
            <input type="text" name="quantite" value="{{ old('quantite', $produit->quantite) }}">
            @error('quantite')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>prix du produit</label><br/>
            <input type="text" name="prix" value="{{ old('prix', $produit->prix) }}">
            @error('prix')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <label>Description du produit</label><br/>
            <textarea name="description" value="{{ old('description', $produit->description) }}"></textarea>
            @error('description')
                <br/><span> {{ $message }} </span>
            @enderror
        </p>
        <p>
            <button type = "submit">Mettre a jour</button>
            <a href="{{ route('produits.index') }}">
                <button type = "button">annuler</button>
            </a>
        </p>
    </form>
</body>
</html>

