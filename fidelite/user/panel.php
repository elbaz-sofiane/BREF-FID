<?php
/**
 * Panel client - Affichage QR code et points
 */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - Bref Barbershop</title>
    <link rel="stylesheet" href="../../asset/style/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        .panel-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            padding: 100px 2rem 3rem;
        }
        
        .panel-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #000000;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666666;
            font-size: 1rem;
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #000000;
            text-align: left;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .card h2::after {
            display: none;
        }
        
        .qr-container {
            text-align: center;
        }
        
        #qrcode {
            display: inline-block;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .qr-code-text {
            font-family: monospace;
            font-size: 1.2rem;
            color: #000000;
            font-weight: bold;
            margin-top: 1rem;
        }
        
        .qr-instructions {
            color: #666666;
            margin-top: 1rem;
            line-height: 1.6;
        }
        
        .rewards-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .reward-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 10px;
        }
        
        .reward-item.available {
            background: #e8f5e9;
            border: 2px solid #4caf50;
        }
        
        .reward-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .reward-icon {
            font-size: 2rem;
        }
        
        .reward-details h3 {
            font-size: 1.1rem;
            color: #000000;
            margin-bottom: 0.25rem;
        }
        
        .reward-details p {
            color: #666666;
            font-size: 0.9rem;
        }
        
        .reward-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .reward-badge.available {
            background: #4caf50;
            color: white;
        }
        
        .reward-badge.locked {
            background: #e0e0e0;
            color: #999999;
        }
        
        .history-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 10px;
            border-left: 4px solid #000000;
        }
        
        .history-info {
            flex: 1;
        }
        
        .history-type {
            font-weight: 600;
            color: #000000;
            margin-bottom: 0.25rem;
        }
        
        .history-date {
            font-size: 0.85rem;
            color: #666666;
        }
        
        .history-points {
            font-size: 1.2rem;
            font-weight: bold;
            color: #4caf50;
        }
        
        .logout-btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: #000000;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #333333;
        }
        
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #666666;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        @media (max-width: 968px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
                <li><a href="panel.php">Mon Espace</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
<br><br><br>

    <div class="panel-container">
        <div class="panel-content">
            <?php if ($welcome): ?>
            <div class="welcome-banner">
                <h1>🎉 Bienvenue <?php echo htmlspecialchars($client['prenom']); ?> !</h1>
                <p>Votre compte a été créé avec succès. Présentez votre QR code à chaque visite pour gagner des points.</p>
            </div>
            <?php endif; ?>

            <div class="actions-bar">
                <h1 style="margin: 0; color: #000000;">Mon Espace Fidélité</h1>
                <a href="logout.php" class="logout-btn">Déconnexion</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-value"><?php echo $client['points']; ?></div>
                    <div class="stat-label">Points</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎁</div>
                    <div class="stat-value"><?php echo $produitsDisponibles; ?></div>
                    <div class="stat-label">Produits disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✂️</div>
                    <div class="stat-value"><?php echo $coupesDisponibles; ?></div>
                    <div class="stat-label">Coupes disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-value"><?php echo count($historique); ?></div>
                    <div class="stat-label">Visites</div>
                </div>
            </div>

            <div class="main-grid">
                <div class="card">
                    <h2>🎯 Mon QR Code</h2>
                    <div class="qr-container">
                        <div id="qrcode"></div>
                        <div class="qr-code-text"><?php echo htmlspecialchars($client['qr_code']); ?></div>
                        <p class="qr-instructions">
                            Présentez ce QR code au commerçant à chaque visite pour gagner 1 point.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <h2>🎁 Mes Récompenses</h2>
                    <div class="rewards-list">
                        <div class="reward-item <?php echo $produitsDisponibles > 0 ? 'available' : ''; ?>">
                            <div class="reward-info">
                                <div class="reward-icon">🎁</div>
                                <div class="reward-details">
                                    <h3>Produit gratuit</h3>
                                    <p>5 points nécessaires</p>
                                </div>
                            </div>
                            <div class="reward-badge <?php echo $produitsDisponibles > 0 ? 'available' : 'locked'; ?>">
                                <?php echo $produitsDisponibles > 0 ? $produitsDisponibles . ' disponible(s)' : 'Bientôt'; ?>
                            </div>
                        </div>

                        <div class="reward-item <?php echo $coupesDisponibles > 0 ? 'available' : ''; ?>">
                            <div class="reward-info">
                                <div class="reward-icon">✂️</div>
                                <div class="reward-details">
                                    <h3>Coupe gratuite</h3>
                                    <p>10 points nécessaires</p>
                                </div>
                            </div>
                            <div class="reward-badge <?php echo $coupesDisponibles > 0 ? 'available' : 'locked'; ?>">
                                <?php echo $coupesDisponibles > 0 ? $coupesDisponibles . ' disponible(s)' : 'Bientôt'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>📜 Historique</h2>
                <?php if (count($historique) > 0): ?>
                    <div class="history-list">
                        <?php foreach ($historique as $item): ?>
                            <div class="history-item">
                                <div class="history-info">
                                    <div class="history-type">
                                        <?php
                                        $typeLabels = [
                                            'scan' => '✅ Scan effectué',
                                            'produit_gratuit' => '🎁 Produit récupéré',
                                            'coupe_gratuite' => '✂️ Coupe récupérée'
                                        ];
                                        echo $typeLabels[$item['type_action']] ?? $item['type_action'];
                                        ?>
                                    </div>
                                    <div class="history-date">
                                        <?php echo date('d/m/Y à H:i', strtotime($item['date_action'])); ?>
                                    </div>
                                </div>
                                <div class="history-points">
                                    <?php echo $item['points_ajoutes'] > 0 ? '+' : ''; ?><?php echo $item['points_ajoutes']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    
                <?php endif; ?>
                <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>Aucune visite pour le moment.<br>Présentez votre QR code lors de votre prochaine visite !</p>
                    </div>
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
    <script>
        // Générer le QR code
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $client['qr_code']; ?>",
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>