<?php
/**
 * Envoi de l'email avec le token de connexion
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-7.0.1/src/Exception.php';
require __DIR__ . '/PHPMailer-7.0.1/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-7.0.1/src/SMTP.php';

function sendLoginToken($email, $token, $rememberMe = false) {
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
        $mail->addAddress($email);
        
        // Contenu
        $loginUrl = FIDELITE_URL . '/user/login.php?token=' . urlencode($token) . '&remember=' . ($rememberMe ? '1' : '0');
        
        $mail->isHTML(true);
        $mail->Subject = 'Connexion à votre espace Bref Barbershop';
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #000000; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 15px 40px; background: #000000; color: white !important; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
                .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 Connexion à votre espace</h1>
                </div>
                <div class="content">
                    <p>Bonjour,</p>
                    
                    <p>Vous avez demandé à vous connecter à votre espace fidélité Bref Barbershop.</p>
                    
                    <p style="text-align: center;">
                        <a href="' . $loginUrl . '" class="button">Se connecter</a>
                    </p>
                    
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <p style="word-break: break-all; background: white; padding: 10px; border-radius: 5px; font-size: 12px;">
                        ' . $loginUrl . '
                    </p>
                    
                    <div class="warning">
                        ⏱️ <strong>Ce lien expire dans 15 minutes</strong> pour votre sécurité.
                    </div>
                    
                    <p>Si vous n\'avez pas demandé cette connexion, ignorez simplement cet email.</p>
                </div>
                <div class="footer">
                    <p>© 2024 Bref Barbershop - Programme de fidélité</p>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->AltBody = "Connexion à votre espace Bref Barbershop\n\n"
                       . "Cliquez sur ce lien pour vous connecter :\n"
                       . $loginUrl . "\n\n"
                       . "Ce lien expire dans 15 minutes.\n\n"
                       . "Si vous n'avez pas demandé cette connexion, ignorez cet email.";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email login: {$mail->ErrorInfo}");
        return false;
    }
}