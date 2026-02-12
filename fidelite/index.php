<?php
session_start();
if (isset($_SESSION['mail'])) {
    header("Location: user/panel.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fidélité - Bref Barbershop</title>
    <link rel="stylesheet" href="../asset/style/style.css">
    <style>
        .fidelite-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .fidelite-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .fidelite-container {
            max-width: 1000px;
            width: 100%;
            position: relative;
            z-index: 2;
        }
        
        .fidelite-header {
            text-align: center;
            color: white;
            margin-bottom: 4rem;
        }
        
        .fidelite-header h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .fidelite-header p {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            color: #cccccc;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .choice-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .choice-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
        }
        
        .choice-card:hover {
            transform: translateY(-10px);
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .choice-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        
        .choice-card h2 {
            color: white;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .choice-card h2::after {
            display: none;
        }
        
        .choice-card p {
            color: #cccccc;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .choice-btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: white;
            color: #000000;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .choice-btn:hover {
            background: #f0f0f0;
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.3);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
            color: #cccccc;
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
                <img src="../asset/media/logo.png" alt="Logo Bref Barbershop" class="logo-img">
                <span class="logo-text">Bref Barbershop</span>
            </div>
            <button class="menu-toggle" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-menu">
                <li><a href="../index.html#accueil">Accueil</a></li>
            </ul>
        </nav>
    </header>

    <br><br><br>

    <div class="fidelite-hero">
        <div class="fidelite-container">
            <div class="fidelite-header">
                <h1>💎 Programme Fidélité</h1>
                <p>Gagnez des points à chaque visite et profitez de récompenses exclusives</p>
            </div>

            <div class="choice-cards">
                <div class="choice-card">
                    <div class="choice-icon">👤</div>
                    <h2>Espace Client</h2>
                    <p>Consultez vos points, votre QR code et suivez vos récompenses en temps réel</p>
                    <a href="user/login.php" class="choice-btn">Se connecter</a>
                </div>

                <div class="choice-card">
                    <div class="choice-icon">🏪</div>
                    <h2>Espace Commerçant</h2>
                    <p>Scannez les QR codes clients et gérez le programme de fidélité</p>
                    <a href="admin/login-admin.php" class="choice-btn">Accéder</a>
                </div>
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
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Bref Barbershop. Tous droits réservés.</p>
            <a class="footer-credits" href="../Mention-Legale.html">Mentions Legales |</a> <a class="footer-credits" href="https://github.com/elbaz-sofiane">Sofiane - WebDesign</a>
        </div>
    </footer>

    <script src="../asset/script/script.js"></script>
</body>
</html>