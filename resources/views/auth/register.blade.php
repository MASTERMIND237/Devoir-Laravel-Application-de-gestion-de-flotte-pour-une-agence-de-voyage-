<h2>Inscription</h2>
<form method="POST" action="{{ route('register.submit') }}">
    @csrf
    <label>Nom</label><br>
    <input type="text" name="name" required><br><br>
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>
    <label>Mot de passe</label><br>
    <input type="password" name="password" required><br><br>
    <label>Confirmation du mot de passe</label><br>
    <input type="password" name="password_confirmation"
required><br><br>
    <button type="submit">S’inscrire</button>
</form>
