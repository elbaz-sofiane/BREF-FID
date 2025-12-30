<?php
/**
 * Dashboard commerçant
 */

session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['commercant_id'])) {
    header('Location: security_page.php');
    exit;
}

global $pdo;
// Statistiques générales
$stmt = $pdo->query("SELECT COUNT(*) as total FROM clients WHERE actif = 1");
$totalClients = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(points) as total FROM clients WHERE actif = 1");
$totalPoints = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM historique_points WHERE DATE(date_action) = CURDATE()");
$scansAujourdhui = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM historique_points WHERE YEARWEEK(date_action) = YEARWEEK(NOW())");
$scansSemaine = $stmt->fetch()['total'];

// Dernières activités
$stmt = $pdo->prepare("
    SELECT hp.*, c.nom, c.prenom, c.email 
    FROM historique_points hp
    JOIN clients c ON c.id = hp.client_id
    WHERE hp.commercant_id = ?
    ORDER BY hp.date_action DESC
    LIMIT 15
");
$stmt->execute([$_SESSION['commercant_id']]);
$dernieresActivites = $stmt->fetchAll();

// Top clients
$stmt = $pdo->query("
    SELECT nom, prenom, email, points 
    FROM clients 
    WHERE actif = 1 
    ORDER BY points DESC 
    LIMIT 10
");
$topClients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bref Barbershop</title>
    <link rel="stylesheet" href="../../asset/style/style.css">
    <style>
        .dashboard-container {
            min-height: 100vh;
            background: #f5f5f5;
            padding: 100px 2rem 3rem;
        }
        
        .dashboard-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .dashboard-header h1 {
            font-size: 2rem;
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-primary {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: white;
            color: #000000;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #000000;
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .stat-icon {
            font-size: 2.5rem;
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
            grid-template-columns: 2fr 1fr;
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
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
            text-align: left;
        }
        
        .card h2::after {
            display: none;
        }
        
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 10px;
            border-left: 3px solid #4caf50;
        }
        
        .activity-info {
            flex: 1;
        }
        
        .activity-client {
            font-weight: 600;
            color: #000000;
            margin-bottom: 0.25rem;
        }
        
        .activity-date {
            font-size: 0.85rem;
            color: #666666;
        }
        
        .activity-type {
            padding: 0.25rem 0.75rem;
            background: #4caf50;
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .activity-type.produit {
            background: #ff9800;
        }
        
        .activity-type.coupe {
            background: #2196f3;
        }
        
        .client-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .client-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 10px;
        }
        
        .client-info h3 {
            font-size: 1rem;
            color: #000000;
            margin-bottom: 0.25rem;
        }
        
        .client-info p {
            font-size: 0.85rem;
            color: #666666;
        }
        
        .client-points {
            font-size: 1.5rem;
            font-weight: bold;
            color: #000000;
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
        
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-header {
                text-align: center;
            }
            
            .header-actions {
                width: 100%;
                flex-direction: column;
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="scanner.php">Scanner QR</a></li>
                <li><a href="page_help.php">Aide</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
<br><br><br>
    <div class="dashboard-container">
        <div class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1>👋 Bonjour, <?php echo htmlspecialchars($_SESSION['commercant_nom']); ?></h1>
                    <p>Tableau de bord du programme de fidélité</p>
                </div>
                <div class="header-actions">
                    <a href="scanner.php" class="btn-primary">📷 Scanner QR Code</a>
                    <a href="logout.php" class="btn-secondary">Déconnexion</a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">👥</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($totalClients); ?></div>
                    <div class="stat-label">Clients inscrits</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">⭐</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($totalPoints); ?></div>
                    <div class="stat-label">Points distribués</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">📅</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($scansAujourdhui); ?></div>
                    <div class="stat-label">Scans aujourd'hui</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">📊</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($scansSemaine); ?></div>
                    <div class="stat-label">Scans cette semaine</div>
                </div>
            </div>

            <div class="main-grid">
                <div class="card">
                    <h2>🔔 Activité récente</h2>
                    <?php if (count($dernieresActivites) > 0): ?>
                        <div class="activity-list">
                            <?php foreach ($dernieresActivites as $activite): ?>
                                <div class="activity-item">
                                    <div class="activity-info">
                                        <div class="activity-client">
                                            <?php echo htmlspecialchars($activite['prenom'] . ' ' . $activite['nom']); ?>
                                        </div>
                                        <div class="activity-date">
                                            <?php echo date('d/m/Y à H:i', strtotime($activite['date_action'])); ?>
                                        </div>
                                    </div>
                                    <div class="activity-type <?php echo $activite['type_action']; ?>">
                                        <?php
                                        $types = [
                                            'scan' => '+' . $activite['points_ajoutes'] . ' pt',
                                            'produit_gratuit' => 'Produit',
                                            'coupe_gratuite' => 'Coupe'
                                        ];
                                        echo $types[$activite['type_action']] ?? $activite['type_action'];
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>Aucune activité récente</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>🏆 Top Clients</h2>
                    <?php if (count($topClients) > 0): ?>
                        <div class="client-list">
                            <?php foreach ($topClients as $client): ?>
                                <div class="client-item">
                                    <div class="client-info">
                                        <h3><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h3>
                                        <p><?php echo htmlspecialchars($client['email']); ?></p>
                                    </div>
                                    <div class="client-points"><?php echo $client['points']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">👥</div>
                            <p>Aucun client inscrit</p>
                        </div>
                    <?php endif; ?>
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
</body>
</html>