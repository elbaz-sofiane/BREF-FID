<?php
/**
 * Envoi de l'email de bienvenue avec QR code
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-7.0.1/src/Exception.php';
require __DIR__ . '/PHPMailer-7.0.1/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-7.0.1/src/SMTP.php';

function sendWelcomeEmail($email, $nom, $qrCode) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Destinataires
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $nom);
        
        // Générer l'URL du QR code (utiliser une API gratuite)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrCode);
        
        $mail->isHTML(true);
        $mail->Subject = 'Bienvenue au programme de fidélité Bref Barbershop !';
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #000000 0%, #333333 100%); color: white; padding: 40px 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .qr-section { background: white; padding: 30px; text-align: center; border-radius: 10px; margin: 20px 0; border: 3px solid #000000; }
                .qr-code { max-width: 300px; margin: 20px auto; }
                .info-box { background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 20px 0; border-radius: 5px; }
                .rewards { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
                .reward-card { background: white; padding: 20px; text-align: center; border-radius: 10px; border: 2px solid #e0e0e0; }
                .reward-icon { font-size: 3rem; margin-bottom: 10px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Bienvenue ' . htmlspecialchars($nom) . ' !</h1>
                    <p>Vous faites maintenant partie de notre programme de fidélité</p>
                </div>
                <div class="content">
                    <p>Nous sommes ravis de vous compter parmi nos clients fidèles !</p>
                    
                    <div class="qr-section">
                        <h2 style="margin-top: 0;">📱 Votre QR Code Personnel</h2>
                        <img src="' . $qrCodeUrl . '" alt="QR Code" class="qr-code" />
                        <p style="font-family: monospace; font-size: 1.2rem; font-weight: bold; color: #000000;">
                            ' . $qrCode . '
                        </p>
                        <p style="color: #666; font-size: 0.9rem;">
                            Présentez ce code à chaque visite pour gagner des points
                        </p>
                    </div>
                    
                    <div class="info-box">
                        <strong>💡 Comment ça marche ?</strong><br>
                        1️⃣ Présentez votre QR code à chaque visite<br>
                        2️⃣ Gagnez 1 point par scan<br>
                        3️⃣ Profitez de récompenses exclusives !
                    </div>
                    
                    <h3 style="text-align: center; color: #000000;">🎁 Vos Récompenses</h3>
                    <div class="rewards">
                        <div class="reward-card">
                            <div class="reward-icon">🎁</div>
                            <strong>5 Points</strong>
                            <p style="color: #666; font-size: 0.9rem; margin: 10px 0 0 0;">
                                Un produit gratuit
                            </p>
                        </div>
                        <div class="reward-card">
                            <div class="reward-icon">✂️</div>
                            <strong>10 Points</strong>
                            <p style="color: #666; font-size: 0.9rem; margin: 10px 0 0 0;">
                                Une coupe gratuite
                            </p>
                        </div>
                    </div>
                    
                    <p style="text-align: center; margin-top: 30px;">
                        <a href="' . FIDELITE_URL . '/user/panel.php" style="display: inline-block; padding: 15px 40px; background: #000000; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                            Accéder à mon espace
                        </a>
                    </p>
                </div>
                <div class="footer">
                    <p>© 2024 Bref Barbershop - Programme de fidélité</p>
                    <p>📍 Adresse du salon | 📞 Téléphone | 🌐 Site web</p>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->AltBody = "Bienvenue au programme de fidélité Bref Barbershop !\n\n"
                       . "Votre code QR : $qrCode\n\n"
                       . "Présentez ce code à chaque visite pour gagner des points.\n\n"
                       . "Récompenses :\n"
                       . "- 5 points = 1 produit gratuit\n"
                       . "- 10 points = 1 coupe gratuite\n\n"
                       . "Accédez à votre espace : " . FIDELITE_URL . "/user/panel.php";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email bienvenue: {$mail->ErrorInfo}");
        return false;
    }
}