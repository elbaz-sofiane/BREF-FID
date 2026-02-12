<?php
session_start();

$bd = new mysqli("localhost", "root", "", "injectionD");

$email = $_SESSION['mail'];

$sql = "SELECT prenom, point FROM `user` WHERE mail='$email'"; //
$result = $bd->query($sql);  // Exécute la requête pour récupérer les informations de l'utilisateur connecté
$user = $result->fetch_assoc(); // Récupère les informations de l'utilisateur connecté
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace - Bref Barbershop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../asset/style/style.css">

    <style>
        .panel-container {
            min-height: 100vh;
            padding: 100px 2rem 3rem;
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
        }

        .welcome-banner {
            max-width: 600px;
            min-height: 300px;
            margin: auto;
            background: linear-gradient(135deg, #000, #333);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            gap : 1rem;
        }

        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .user-info {
            margin: 1.5rem 0;
            background: #efff5a;
            color: #000;
            padding: 1rem;
            border-radius: 10px;
            font-weight: bold;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, #333, #000);
            border-radius: 20px;
            padding: 2rem;
        }
        
        .feature {
            text-align: center;
            color: white;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .feature h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .feature p {
            color: #ffffff;
            font-size: 0.95rem;
        }
        
        @media (max-width: 768px) {
            .choice-cards {
                grid-template-columns: 1fr;
            }
            
            .features {
                grid-template-columns: 1fr;
                gap: 1.5rem;
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
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-menu">
            <li><a href="../../index.html#accueil">Accueil</a></li>
            <li><a href="panel.php">Mon Espace</a></li>
            <li><a href="logout.php">Deconnexion</a></li>
        </ul>
    </nav>
</header>
<br><br>
<div class="panel-container">
    <div class="welcome-banner">
        <h1>Bonjour <?= $user['prenom'] ?></h1>

        <p>Voici votre tableau de bord personnel pour suivre vos points de fidélité.</p>

        <div class="user-info">
            Points de fidélité : <?= $user['point'] ?>
        </div>

        <p>Continuez à accumuler des points pour débloquer des récompenses exclusives !</p>

        
    </div>
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">⭐</div>
                    <h3>10 Point par visite</h3>
                    <p>Chaque passage compte</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎁</div>
                    <h3>50 Points = Produit</h3>
                    <p>Un produit offert</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">✂️</div>
                    <h3>100 Points = Coupe</h3>
                    <p>Une coupe gratuite</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔒</div>
                    <h3>100% Sécurisé</h3>
                    <p>Vos données protégées</p>
                </div>
                
            </div>
</div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Bref Barbershop. Tous droits réservés.</p>
            <a class="footer-credits" href="../../Mention-Legale.html">Mentions Legales |</a> <a class="footer-credits" href="https://github.com/elbaz-sofiane">Sofiane - WebDesign</a>
        </div>
    </footer>

</body>
</html>