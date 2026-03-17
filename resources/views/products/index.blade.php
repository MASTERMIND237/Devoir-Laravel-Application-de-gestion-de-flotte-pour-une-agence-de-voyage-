@extends('app.master')
@section('title', 'Liste des produits')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;">
    <h2>Liste des produits</h2>
    <div>
        @auth
            <span>Connecté en tant que <strong>{{ auth()->user()->name }}</strong></span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit">Déconnexion</button>
            </form>
        @endauth
    </div>
</div>
<ul>
    @foreach ($produits as $product)
        <li>{{ $product->nom }} - {{ $product->prix }}</li>
    @endforeach
</ul>
@endsection
