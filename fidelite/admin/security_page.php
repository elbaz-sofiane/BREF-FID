<?php
/**
 * Page de connexion commerçant avec code d'accès
 */

session_start();
require_once __DIR__ . '/../../config/db.php';

// Si déjà connecté
if (isset($_SESSION['commercant_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Erreur de sécurité.';
    } else {
        $codeAcces = sanitizeInput($_POST['code_acces']);
        
        if (empty($codeAcces)) {
            $error = 'Code d\'accès requis.';
        } else {
            global $pdo;
            
            // Récupérer le commerçant
            $stmt = $pdo->prepare("
                SELECT id, nom, email, code_acces, salt, tentatives_connexion, 
                       derniere_tentative, bloque_jusqu_a 
                FROM commercants 
                WHERE code_acces = ? AND actif = 1
            ");
            $stmt->execute([$codeAcces]);
            $commercant = $stmt->fetch();
            
            if (!$commercant) {
                $error = 'Code d\'accès invalide.';
                logSecurityEvent('connexion_echouee', 'commercant', null, 'Code invalide: ' . $codeAcces);
                sleep(2); // Anti brute-force
            } else {
                // Vérifier si le compte est bloqué
                if ($commercant['bloque_jusqu_a'] && strtotime($commercant['bloque_jusqu_a']) > time()) {
                    $minutesRestantes = ceil((strtotime($commercant['bloque_jusqu_a']) - time()) / 60);
                    $error = "Trop de tentatives. Compte bloqué pour {$minutesRestantes} minutes.";
                    logSecurityEvent('connexion_echouee', 'commercant', $commercant['id'], 'Compte bloqué');
                } else {
                    // Vérifier le code (hashé avec salt)
                    $codeHash = hash('sha256', $codeAcces . $commercant['salt']);
                    $codeStored = hash('sha256', $commercant['code_acces'] . $commercant['salt']);
                    
                    if (hash_equals($codeStored, $codeHash)) {
                        // Connexion réussie
                        $_SESSION['commercant_id'] = $commercant['id'];
                        $_SESSION['commercant_nom'] = $commercant['nom'];
                        $_SESSION['session_id'] = generateSecureToken(64);
                        
                        // Enregistrer la session
                        $stmt = $pdo->prepare("
                            INSERT INTO sessions (session_id, user_type, user_id, ip_address, user_agent)
                            VALUES (?, 'commercant', ?, ?, ?)
                        ");
                        $stmt->execute([
                            $_SESSION['session_id'],
                            $commercant['id'],
                            $_SERVER['REMOTE_ADDR'],
                            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                        ]);
                        
                        // Réinitialiser les tentatives
                        $stmt = $pdo->prepare("
                            UPDATE commercants 
                            SET tentatives_connexion = 0, 
                                bloque_jusqu_a = NULL,
                                derniere_connexion = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$commercant['id']]);
                        
                        logSecurityEvent('connexion_reussie', 'commercant', $commercant['id'], 'Connexion réussie');
                        
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        // Code incorrect - incrémenter les tentatives
                        $tentatives = $commercant['tentatives_connexion'] + 1;
                        
                        if ($tentatives >= MAX_LOGIN_ATTEMPTS) {
                            // Bloquer le compte
                            $bloqueJusqua = date('Y-m-d H:i:s', time() + LOCKOUT_TIME);
                            $stmt = $pdo->prepare("
                                UPDATE commercants 
                                SET tentatives_connexion = ?, 
                                    bloque_jusqu_a = ?,
                                    derniere_tentative = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([$tentatives, $bloqueJusqua, $commercant['id']]);
                            
                            $error = 'Trop de tentatives. Compte bloqué pour 15 minutes.';
                            logSecurityEvent('connexion_echouee', 'commercant', $commercant['id'], 'Compte bloqué après ' . MAX_LOGIN_ATTEMPTS . ' tentatives');
                        } else {
                            $stmt = $pdo->prepare("
                                UPDATE commercants 
                                SET tentatives_connexion = ?,
                                    derniere_tentative = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([$tentatives, $commercant['id']]);
                            
                            $tentativesRestantes = MAX_LOGIN_ATTEMPTS - $tentatives;
                            $error = "Code incorrect. {$tentativesRestantes} tentative(s) restante(s).";
                            logSecurityEvent('connexion_echouee', 'commercant', $commercant['id'], 'Tentative ' . $tentatives);
                        }
                        
                        sleep(2); // Anti brute-force
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Commerçant - Bref Barbershop</title>
    <link rel="stylesheet" href="../../asset/style/style.css">
    <style>
        .security-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .security-container::before {
            content: '🔒';
            position: absolute;
            font-size: 30rem;
            opacity: 0.03;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
        
        .security-box {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 2;
        }
        
        .security-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .security-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .security-header h1 {
            font-size: 2rem;
            color: #000000;
            margin-bottom: 0.5rem;
        }
        
        .security-header p {
            color: #666666;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #000000;
            font-weight: 600;
        }
        
        .code-input {
            width: 100%;
            padding: 1.5rem;
            border: 3px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1.8rem;
            text-align: center;
            font-weight: bold;
            letter-spacing: 0.5rem;
            font-family: monospace;
            transition: all 0.3s ease;
        }
        
        .code-input:focus {
            outline: none;
            border-color: #000000;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: #000000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #333333;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 2px solid #fcc;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #666666;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: #000000;
        }
        
        .security-warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
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
<br><br><br>
    <div class="security-container">
        <div class="security-box">
            <div class="security-header">
                <div class="security-icon">🔐</div>
                <h1>Espace Commerçant</h1>
                <p>Accès sécurisé</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="code_acces">Code d'accès</label>
                    <input 
                        type="password" 
                        id="code_acces" 
                        name="code_acces" 
                        class="code-input"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autocomplete="off"
                        inputmode="numeric"
                        placeholder="••••••"
                    >
                </div>

                <button type="submit" class="submit-btn">🔓 Accéder</button>
            </form>

            <div class="security-warning">
                🛡️ Cette zone est protégée. Les tentatives d'accès sont enregistrées.
            </div>

            <div class="back-link">
                <a href="../index.php">← Retour</a>
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
        // Auto-formatage du code (numérisation uniquement)
        document.getElementById('code_acces').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>
</html>