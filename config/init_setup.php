<?php
/**
 * Script d'initialisation - À exécuter UNE SEULE FOIS
 * Configure le premier commerçant avec un code sécurisé
 * 
 * INSTRUCTIONS:
 * 1. Créez d'abord la base de données avec database.sql
 * 2. Configurez config/db.php avec vos identifiants
 * 3. Exécutez ce fichier DEPUIS LE NAVIGATEUR : http://localhost/BREF-FID-V.1/config/init_setup.php
 * 4. SUPPRIMEZ ce fichier après utilisation pour des raisons de sécurité
 */

require_once __DIR__ . '/db.php';

// Sécurité : Ce script ne peut être exécuté qu'une seule fois
$lockFile = __DIR__ . '/.setup_lock';
if (file_exists($lockFile)) {
    die('⛔ Ce script a déjà été exécuté. Supprimez le fichier .setup_lock pour le réexécuter (déconseillé en production).');
}

// Vérifier si on est en POST (formulaire soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitizeInput($_POST['nom']);
    $email = sanitizeInput($_POST['email']);
    $codeAcces = sanitizeInput($_POST['code_acces']);
    
    // Validation
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    
    if (!isValidEmail($email)) {
        $errors[] = "Email invalide";
    }
    
    if (strlen($codeAcces) !== 6 || !ctype_digit($codeAcces)) {
        $errors[] = "Le code d'accès doit contenir exactement 6 chiffres";
    }
    
    if (empty($errors)) {
        try {
            global $pdo;
            
            // Vérifier si un commerçant existe déjà
            $stmt = $pso->query("SELECT COUNT(*) as count FROM commercants");
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                $errors[] = "Un commerçant existe déjà dans la base de données";
            } else {
                // Générer un salt unique
                $salt = bin2hex(random_bytes(16));
                
                // Créer le commerçant
                $stmt = $pdo->prepare("
                    INSERT INTO commercants (nom, email, code_acces, salt, actif)
                    VALUES (?, ?, ?, ?, 1)
                ");
                
                if ($stmt->execute([$nom, $email, $codeAcces, $salt])) {
                    // Créer le fichier de verrouillage
                    file_put_contents($lockFile, date('Y-m-d H:i:s'));
                    
                    $success = true;
                    $message = "✅ Configuration réussie ! Le commerçant a été créé.";
                } else {
                    $errors[] = "Erreur lors de la création du commerçant";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur de base de données: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration initiale - Bref Barbershop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            color: #000000;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        .subtitle {
            text-align: center;
            color: #666666;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .warning strong {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #000000;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input:focus {
            outline: none;
            border-color: #000000;
        }
        .help-text {
            font-size: 0.85rem;
            color: #666666;
            margin-top: 0.5rem;
        }
        button {
            width: 100%;
            padding: 1.2rem;
            background: #000000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #333333;
        }
        .error {
            background: #fee;
            border: 2px solid #fcc;
            color: #c33;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .error ul {
            margin-left: 1.5rem;
        }
        .success {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            color: #2e7d32;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
        }
        .success h2 {
            color: #2e7d32;
            margin-bottom: 1rem;
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .next-steps {
            background: #f5f5f5;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
        }
        .next-steps h3 {
            color: #000000;
            margin-bottom: 1rem;
        }
        .next-steps ol {
            margin-left: 1.5rem;
            line-height: 1.8;
        }
        .credentials {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            border: 2px solid #4caf50;
        }
        .credentials p {
            margin: 0.5rem 0;
        }
        .credentials strong {
            font-family: monospace;
            font-size: 1.2rem;
        }
        .delete-warning {
            background: #fee;
            border: 2px solid #f44336;
            color: #c33;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($success) && $success): ?>
            <div class="success">
                <div class="success-icon">🎉</div>
                <h2>Configuration terminée !</h2>
                <p><?php echo $message; ?></p>
                
                <div class="credentials">
                    <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>🔐 Code d'accès:</strong> <span style="font-family: monospace; font-size: 1.5rem;"><?php echo htmlspecialchars($codeAcces); ?></span></p>
                </div>
                
                <div class="next-steps">
                    <h3>📋 Prochaines étapes :</h3>
                    <ol>
                        <li>✅ Notez votre code d'accès dans un endroit sûr</li>
                        <li>🗑️ SUPPRIMEZ ce fichier (init_setup.php) immédiatement</li>
                        <li>📧 Configurez PHPMailer dans config/pdo.php</li>
                        <li>🚀 Accédez à l'espace commerçant</li>
                    </ol>
                </div>
                
                <div class="delete-warning">
                    ⚠️ IMPORTANT : Supprimez ce fichier (config/init_setup.php) pour des raisons de sécurité !
                </div>
                
                <div style="margin-top: 2rem; text-align: center;">
                    <a href="../fidelite/admin/security_page.php" style="display: inline-block; padding: 1rem 2rem; background: #000000; color: white; text-decoration: none; border-radius: 10px; font-weight: 600;">
                        Accéder à l'espace commerçant
                    </a>
                </div>
            </div>
        <?php else: ?>
            <h1>🔧 Configuration initiale</h1>
            <p class="subtitle">Créez le premier compte commerçant</p>
            
            <div class="warning">
                <strong>⚠️ Attention :</strong>
                Ce script ne doit être exécuté qu'une seule fois lors de l'installation initiale. 
                Après configuration, supprimez ce fichier pour des raisons de sécurité.
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <strong>❌ Erreur(s) :</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nom">Nom du barbershop / Commerçant</label>
                    <input 
                        type="text" 
                        id="nom" 
                        name="nom" 
                        required
                        value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : 'Bref Barbershop'; ?>"
                    >
                    <p class="help-text">Le nom qui apparaîtra sur le dashboard</p>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                    <p class="help-text">Utilisé pour les notifications (optionnel)</p>
                </div>
                
                <div class="form-group">
                    <label for="code_acces">Code d'accès (6 chiffres)</label>
                    <input 
                        type="text" 
                        id="code_acces" 
                        name="code_acces" 
                        required
                        maxlength="6"
                        pattern="[0-9]{6}"
                        placeholder="123456"
                        value="<?php echo isset($_POST['code_acces']) ? htmlspecialchars($_POST['code_acces']) : ''; ?>"
                    >
                    <p class="help-text">6 chiffres pour accéder à l'espace commerçant. Conservez-le précieusement !</p>
                </div>
                
                <button type="submit">Créer le compte commerçant</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>