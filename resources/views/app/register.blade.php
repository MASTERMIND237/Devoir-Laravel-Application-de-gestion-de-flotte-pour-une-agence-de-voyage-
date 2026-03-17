<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 440px;
            padding: 50px 40px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-header h1 {
            font-size: 28px;
            color: #1a202c;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .auth-header p {
            color: #718096;
            font-size: 15px;
        }

        .auth-header p a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-header p a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 14px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input.error {
            border-color: #f56565;
        }

        .error-message {
            color: #f56565;
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fed7d7;
            color: #c53030;
            border: 1px solid #fc8181;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #f56565;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #ed8936;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #48bb78;
        }

        .password-strength-text {
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .password-strength-text.show {
            display: block;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>Inscription</h1>
            <p>Déjà inscrit ? <a href="/login">Connectez-vous</a></p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/register" id="registerForm">
            @csrf
            
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Jean Dupont"
                    required
                >
                <span class="error-message" id="nameError"></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="votre@email.com"
                    required
                >
                <span class="error-message" id="emailError"></span>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Au moins 6 caractères"
                    required
                >
                <div class="password-strength" id="passwordStrength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <span class="password-strength-text" id="passwordStrengthText"></span>
                <span class="error-message" id="passwordError"></span>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Retapez votre mot de passe"
                    required
                >
                <span class="error-message" id="passwordConfirmError"></span>
            </div>

            <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>

        <div class="footer-links">
            <a href="/">Retour à l'accueil</a>
        </div>
    </div>

    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('passwordStrengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                strengthIndicator.classList.remove('show');
                strengthText.classList.remove('show');
                return;
            }

            strengthIndicator.classList.add('show');
            strengthText.classList.add('show');

            let strength = 0;
            
            // Length
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            
            // Contains number
            if (/\d/.test(password)) strength++;
            
            // Contains lowercase and uppercase
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            
            // Contains special character
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';
            
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Mot de passe faible';
                strengthText.style.color = '#f56565';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Mot de passe moyen';
                strengthText.style.color = '#ed8936';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Mot de passe fort';
                strengthText.style.color = '#48bb78';
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Reset errors
            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.remove('show');
            });
            document.querySelectorAll('input').forEach(el => {
                el.classList.remove('error');
            });

            // Name validation
            const name = document.getElementById('name');
            if (name.value.trim().length < 2) {
                document.getElementById('nameError').textContent = 'Le nom doit contenir au moins 2 caractères';
                document.getElementById('nameError').classList.add('show');
                name.classList.add('error');
                isValid = false;
            }

            // Email validation
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                document.getElementById('emailError').textContent = 'Veuillez entrer un email valide';
                document.getElementById('emailError').classList.add('show');
                email.classList.add('error');
                isValid = false;
            }

            // Password validation
            const password = document.getElementById('password');
            if (password.value.length < 6) {
                document.getElementById('passwordError').textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                document.getElementById('passwordError').classList.add('show');
                password.classList.add('error');
                isValid = false;
            }

            // Password confirmation
            const passwordConfirm = document.getElementById('password_confirmation');
            if (password.value !== passwordConfirm.value) {
                document.getElementById('passwordConfirmError').textContent = 'Les mots de passe ne correspondent pas';
                document.getElementById('passwordConfirmError').classList.add('show');
                passwordConfirm.classList.add('error');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Animation sur focus
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>