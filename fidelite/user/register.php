<?php
/**
 * Page d'inscription client
 */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Bref Barbershop</title>
    <link rel="stylesheet" href="../../asset/style/style.css">

    <style>
        .register-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            padding-top: 100px;
        }

        .register-box {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            font-size: 2rem;
            color: #000;
            margin-bottom: 0.5rem;
        }

        .register-header p {
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .login-link a {
            color: #000;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
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

<div class="register-container">
    <div class="register-box">

        <div class="register-header">
            <h1>✨ Créer un compte</h1>
            <p>Rejoignez notre programme de fidélité</p>
        </div>

        <form>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" placeholder="exemple@email.com" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" placeholder="Votre prénom" required>
                </div>

                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" placeholder="Votre nom" required>
                </div>
            </div>

            <div class="form-group">
                <label>Téléphone (optionnel)</label>
                <input type="tel" placeholder="06 12 34 56 78">
            </div>

            <button type="submit" class="submit-btn">Créer mon compte</button>
        </form>

        <div class="login-link">
            Déjà inscrit ? <a href="login.php">Se connecter</a>
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
