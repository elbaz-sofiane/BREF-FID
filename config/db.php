<?php
session_start();

/*
|--------------------------------------------------------------------------
| CONFIGURATION BASE DE DONNÉES (MAMP)
|--------------------------------------------------------------------------
*/
define('DB_HOST', 'localhost');
define('DB_NAME', 'fidelite_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_PORT', '8889'); // Port MAMP par défaut

/*
|--------------------------------------------------------------------------
| CONFIGURATION ENVIRONNEMENT
|--------------------------------------------------------------------------
*/
define('ENV', 'development'); // 'development' ou 'production'
define('BASE_URL', 'http://localhost:8888/BREF-FID-V.1');
define('FIDELITE_URL', BASE_URL . '/fidelite');

/*
|--------------------------------------------------------------------------
| CONFIGURATION EMAIL (PHPMailer)
|--------------------------------------------------------------------------
*/
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com'); // À configurer
define('SMTP_PASSWORD', 'votre-mot-de-passe-app'); // À configurer
define('SMTP_FROM_EMAIL', 'noreply@brefbarbershop.com');
define('SMTP_FROM_NAME', 'Bref Barbershop');

/*
|--------------------------------------------------------------------------
| CONFIGURATION SÉCURITÉ
|--------------------------------------------------------------------------
*/
define('SECRET_KEY', bin2hex(random_bytes(32)));
define('TOKEN_EXPIRY', 900); // 15 minutes pour les tokens email
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes de blocage

/*
|--------------------------------------------------------------------------
| GESTION DES ERREURS
|--------------------------------------------------------------------------
*/
if (ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Paris');

/*
|--------------------------------------------------------------------------
| CONNEXION PDO SÉCURISÉE (RGPD + anti-fuite d'infos)
|--------------------------------------------------------------------------
*/
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4;port=" . DB_PORT,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    if (ENV === 'development') {
        die("Erreur de connexion : " . $e->getMessage());
    }
    die("Erreur de connexion à la base de données.");
}

/*
|--------------------------------------------------------------------------
| FONCTION HELPER : Obtenir la connexion PDO
|--------------------------------------------------------------------------
*/
function getDB() {
    global $pdo;
    return $pdo;
}

/*
|--------------------------------------------------------------------------
| GÉNÉRATION D'UN QR CODE UNIQUE
|--------------------------------------------------------------------------
*/
function genererCodeBarre() {
    return 'BREF-' . strtoupper(bin2hex(random_bytes(6))); // Format: BREF-XXXXXXXXXXXX
}

/*
|--------------------------------------------------------------------------
| GÉNÉRATION TOKEN SÉCURISÉ
|--------------------------------------------------------------------------
*/
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/*
|--------------------------------------------------------------------------
| VALIDATION EMAIL (RGPD + sécurité)
|--------------------------------------------------------------------------
*/
function validerEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

function isValidEmail($email) {
    return validerEmail($email);
}

/*
|--------------------------------------------------------------------------
| NETTOYAGE DES INPUTS
|--------------------------------------------------------------------------
*/
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| PROTECTION CSRF
|--------------------------------------------------------------------------
*/
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateSecureToken(32);
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/*
|--------------------------------------------------------------------------
| CALCUL DU PROCHAIN OBJECTIF
|--------------------------------------------------------------------------
*/
function getProchainObjectif($points) {
    if ($points < 5) {
        return ['points_requis' => 5, 'recompense' => 'Produit gratuit'];
    } elseif ($points < 10) {
        return ['points_requis' => 10, 'recompense' => 'Coupe gratuite'];
    } else {
        return ['points_requis' => 10, 'recompense' => 'Cycle complet'];
    }
}

/*
|--------------------------------------------------------------------------
| FONCTION "SE SOUVENIR DE MOI" (VERSION RGPD-COMPLIANT)
|--------------------------------------------------------------------------
| → Cookies sécurisés : HttpOnly, SameSite, sans données sensibles
| → Token généré avec random_bytes (sécurité maximale)
|--------------------------------------------------------------------------
*/
function seSouvenirDuClient($email, $duree = 30) {
    global $pdo;

    $expiration = time() + (86400 * $duree);

    // Token cryptographique sûr
    $token = bin2hex(random_bytes(32));

    // COOKIE EMAIL (Pas de données sensibles, conforme RGPD)
    setcookie(
        'fidelite_email',
        $email,
        [
            'expires' => $expiration,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => ENV === 'production'
        ]
    );

    // COOKIE TOKEN SÉCURISÉ
    setcookie(
        'fidelite_token',
        $token,
        [
            'expires' => $expiration,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => ENV === 'production'
        ]
    );

    // Mise à jour en base de données
    $stmt = $pdo->prepare("UPDATE clients SET remember_token = ?, remember_expiry = ? WHERE email = ?");
    $stmt->execute([$token, date('Y-m-d H:i:s', $expiration), $email]);
}

/*
|--------------------------------------------------------------------------
| VÉRIFICATION DU COOKIE "REMEMBER ME"
|--------------------------------------------------------------------------
| → Vérifie token + expiration  
| → Régénère la session (evite fixation)
|--------------------------------------------------------------------------
*/
function verifierSouvenir() {
    if (!isset($_COOKIE['fidelite_email']) || !isset($_COOKIE['fidelite_token'])) {
        return null;
    }

    global $pdo;

    $email = $_COOKIE['fidelite_email'];
    $token = $_COOKIE['fidelite_token'];

    $stmt = $pdo->prepare("
        SELECT * FROM clients 
        WHERE email = ? 
        AND remember_token = ? 
        AND remember_expiry > NOW()
        AND actif = 1
    ");
    $stmt->execute([$email, $token]);
    $client = $stmt->fetch();

    if ($client) {
        session_regenerate_id(true); // Empêche les attaques
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_email'] = $email;
        $_SESSION['client_nom'] = $client['nom'];
        return $client;
    }

    // Token expiré ou invalide → suppression (RGPD : minimisation)
    oublierClient();
    return null;
}

/*
|--------------------------------------------------------------------------
| SUPPRESSION DU "REMEMBER ME"
|--------------------------------------------------------------------------
| → Supprime les cookies et efface le token en BDD
|--------------------------------------------------------------------------
*/
function oublierClient() {
    global $pdo;

    if (isset($_COOKIE['fidelite_email'])) {
        $stmt = $pdo->prepare("UPDATE clients SET remember_token = NULL, remember_expiry = NULL WHERE email = ?");
        $stmt->execute([$_COOKIE['fidelite_email']]);
    }

    // Suppression propre des cookies
    setcookie('fidelite_email', '', time() - 3600, '/');
    setcookie('fidelite_token', '', time() - 3600, '/');

    unset($_SESSION['client_id']);
    unset($_SESSION['client_email']);
    unset($_SESSION['client_nom']);
}

/*
|--------------------------------------------------------------------------
| LOGS DE SÉCURITÉ
|--------------------------------------------------------------------------
*/
function logSecurityEvent($type, $userType, $userId, $details = null) {
    try {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO logs_securite (type_log, user_type, user_id, ip_address, user_agent, details)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $type,
            $userType,
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            $details
        ]);
    } catch (PDOException $e) {
        error_log('Security logging error: ' . $e->getMessage());
    }
}
?>