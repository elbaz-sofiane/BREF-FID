<?php
session_start();

$bd = new mysqli("localhost", "root", "", "injectionD");

$email = $_SESSION['mail'];

$sql = "SELECT prenom, point FROM `user` WHERE mail='$email'"; //
$result = $bd->query($sql);  // Exécute la requête pour récupérer les informations de l'utilisateur connecté
$user = $result->fetch_assoc(); // Récupère les informations de l'utilisateur connecté
 
if (isset($_POST['add_points'])) {
    $email_client = $_POST['email'];
    $update_points = "UPDATE `user` SET point = point + 10 WHERE mail='$email_client'";

    
    if ($bd->query($update_points) === TRUE) {
        echo "<script>alert('10 points ont été ajoutés au client avec succès !');</script>";
    } else {
        echo "Erreur lors de l'ajout des points: " . $bd->error;
    }
}

if (isset($_POST['view_points'])) {
    $email_client = $_POST['email'];
    $get_points = "SELECT point FROM `user` WHERE mail='$email_client'";
    $result_points = $bd->query($get_points);
    
    if ($result_points && $result_points->num_rows > 0) {
        $client = $result_points->fetch_assoc();
        echo "<script>alert('Le client a actuellement " . $client['point'] . " points de fidélité.');</script>";
    } else {
        echo "<script>alert('Client non trouvé.');</script>";
    }
}


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
            max-width: 900px;
            margin: auto;
            background: linear-gradient(135deg, #000, #333);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
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
        input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.3s;
        }
            .submit-btn {
                background: #333;
                color: white;
                border: none;
                padding: 1rem;
                border-radius: 10px;
                font-size: 1rem;
                cursor: pointer;
                transition: background 0.3s;
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

<br> <br>

<div class="panel-container">
    <div class="welcome-banner">
        <h1>Bonjour Admin !</h1>

        <p>Voici votre tableau de bord administrateur pour ajouter des points de fidélité.</p>

        <form class="user-info" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            <input type="email" placeholder="Adresse email du client" name="email" required>
            <input type="submit" value="consulter les points" name="view_points" class="submit-btn">
            <input type="submit" value="Ajouter 10 points" name="add_points" class="submit-btn">
        </form>

        <a href="logout.php">Déconnexion</a>
        
    
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2024 Bref Barbershop. Tous droits réservés.</p>
        <p class="footer-credits">Sofiane - WebDesign</p>
    </div>
</footer>

</body>
</html>