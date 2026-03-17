<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gestion des produits')</title>
</head>
<body>
@include('app.header')
<main>
    @yield('content')
</main>
@include('app.bottom')
</body>
</html>
