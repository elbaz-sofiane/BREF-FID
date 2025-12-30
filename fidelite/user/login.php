<?php
/**
 * Page de connexion client
 */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client - Bref Barbershop</title>
    <link rel="stylesheet" href="../../asset/style/style.css">

    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            padding-top: 100px;
        }

        .login-box {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 2rem;
            color: #000;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #000;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #000;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .checkbox-group input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-group label {
            color: #666;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .register-link a {
            color: #000;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<header>
    <nav>
        <div class="logo-container">
            <img src="../../asset/media/logo.png" alt="Logo Bref Barbershop" class="logo-img">
            <span class="logo-text">Bref Barbershop</span>
        </div>

        <button class="menu-toggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-menu">
            <li><a href="../../index.html#accueil">Accueil</a></li>
            <li><a href="../index.php">Fidélité</a></li>
        </ul>
    </nav>
</header>

<br>

<div class="login-container">
    <div class="login-box">

        <div class="login-header">
            <h1>👤 Connexion Client</h1>
            <p>Entrez votre email pour recevoir un lien de connexion</p>
        </div>

        <form>
            <div class="form-group">
                <label>Adresse email</label>
                <input type="email" placeholder="votre@email.com" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember">
                <label for="remember">Se souvenir de moi (30 jours)</label>
            </div>

            <button type="submit" class="submit-btn">
                Envoyer le lien de connexion
            </button>
        </form>

        <div class="register-link">
            Première visite ? <a href="register.php">Créer un compte</a>
        </div>

    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2024 Bref Barbershop. Tous droits réservés.</p>
        <p class="footer-credits">Sofiane - WebDesign</p>
    </div>
</footer>

<script src="../../asset/script/script.js"></script>

</body>
</html>
