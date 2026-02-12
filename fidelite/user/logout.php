<!-- page de logout client -->
<?php
session_start();
// Supprimer le cookie remember_mail
if (isset($_COOKIE['remember_mail'])) {
    setcookie('remember_mail', '', time() - 3600, '/'); // '/' pour que le cookie soit supprimé sur tout le site
}
session_destroy(); // Détruit la session pour déconnecter l'utilisateur
header("Location: ../index.php"); // Redirige vers la page d'accueil après la déconnexion
exit;
?>