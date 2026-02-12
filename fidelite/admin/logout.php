<!-- page de logout client -->
<?php
session_start();

if (isset($_COOKIE['remember_mail'])) { //si ya un cookie 
    setcookie('remember_mail', '', time() - 3600, '/'); //supprimer en mettant une date d'expiration passer
}

session_destroy(); // Détruit la session pour déconnecter l'utilisateur
header("Location: ../index.php"); // Redirige vers la page d'accueil après la déconnexion
exit;
?>