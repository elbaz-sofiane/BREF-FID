<?php
/**
 * Scanner QR Code pour commerçant
 */

session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['commercant_id'])) {
    header('Location: security_page.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR - Bref Barbershop</title>
    <link rel="stylesheet" href="../../asset/style/style.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        .scanner-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            padding: 100px 2rem 3rem;
        }
        
        .scanner-content {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .scanner-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .scanner-header h1 {
            font-size: 2.5rem;
            color: #000000;
            margin-bottom: 0.5rem;
        }
        
        .scanner-header p {
            color: #666666;
            font-size: 1.1rem;
        }
        
        .scanner-box {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        #reader {
            border-radius: 15px;
            overflow: hidden;
            border: 3px solid #e0e0e0;
        }
        
        .manual-input-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .manual-input-section h3 {
            font-size: 1.3rem;
            color: #000000;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .input-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .input-group input {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            text-transform: uppercase;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #000000;
        }
        
        .input-group button {
            padding: 1rem 2rem;
            background: #000000;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .input-group button:hover {
            background: #333333;
        }
        
        .result-box {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: none;
        }
        
        .result-box.success {
            display: block;
            border: 3px solid #4caf50;
        }
        
        .result-box.error {
            display: block;
            border: 3px solid #f44336;
        }
        
        .result-content {
            text-align: center;
        }
        
        .result-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        
        .result-title {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #000000;
        }
        
        .client-info {
            background: #f5f5f5;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }
        
        .client-info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .client-info-row:last-child {
            border-bottom: none;
        }
        
        .client-info-label {
            font-weight: 600;
            color: #666666;
        }
        
        .client-info-value {
            color: #000000;
            font-weight: 600;
        }
        
        .points-display {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 1.5rem 0;
        }
        
        .points-item {
            text-align: center;
        }
        
        .points-value {
            font-size: 3rem;
            font-weight: bold;
            color: #4caf50;
        }
        
        .points-label {
            color: #666666;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #000000;
            color: white;
        }
        
        .btn-primary:hover {
            background: #333333;
        }
        
        .btn-success {
            background: #4caf50;
            color: white;
        }
        
        .btn-success:hover {
            background: #45a049;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #000000;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        
        .reward-available {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            text-align: center;
            font-weight: 600;
            color: #856404;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #000000;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .points-display {
                flex-direction: column;
                gap: 1rem;
            }
            
            .action-buttons {
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

    <div class="scanner-container">
        <div class="scanner-content">
            <div class="scanner-header">
                <h1>📷 Scanner QR Code</h1>
                <p>Pointez la caméra vers le QR code du client</p>
            </div>

            <div class="scanner-box">
                <div id="reader"></div>
            </div>

            <div class="manual-input-section">
                <h3>⌨️ Saisie manuelle</h3>
                <div class="input-group">
                    <input 
                        type="text" 
                        id="manual-code" 
                        placeholder="BREF-XXXXXXXXXXXX"
                        maxlength="17"
                    >
                    <button onclick="processManualCode()">Valider</button>
                </div>
            </div>

            <div id="result-box" class="result-box">
                <div class="result-content" id="result-content">
                    <!-- Résultat dynamique -->
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
        let html5QrCode;
        let lastScannedCode = '';
        let scanTimeout;

        // Initialiser le scanner
        function initScanner() {
            html5QrCode = new Html5Qrcode("reader");
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[cameras.length - 1].id; // Caméra arrière
                    
                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        (decodedText) => {
                            if (decodedText !== lastScannedCode) {
                                lastScannedCode = decodedText;
                                processQRCode(decodedText);
                                
                                // Éviter les scans multiples
                                clearTimeout(scanTimeout);
                                scanTimeout = setTimeout(() => {
                                    lastScannedCode = '';
                                }, 3000);
                            }
                        },
                        (errorMessage) => {
                            // Erreurs de scan (ignorées)
                        }
                    ).catch(err => {
                        console.error('Erreur caméra:', err);
                        showError('Impossible d\'accéder à la caméra. Utilisez la saisie manuelle.');
                    });
                } else {
                    showError('Aucune caméra détectée. Utilisez la saisie manuelle.');
                }
            }).catch(err => {
                console.error('Erreur:', err);
                showError('Erreur d\'accès à la caméra.');
            });
        }

        // Traiter le QR code
        function processQRCode(qrCode) {
            showLoading();
            
            fetch('process_scan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ qr_code: qrCode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data);
                } else {
                    showError(data.message || 'Erreur lors du traitement.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showError('Erreur de connexion au serveur.');
            });
        }

        // Saisie manuelle
        function processManualCode() {
            const code = document.getElementById('manual-code').value.trim().toUpperCase();
            if (code) {
                processQRCode(code);
                document.getElementById('manual-code').value = '';
            }
        }

        // Afficher le chargement
        function showLoading() {
            const resultBox = document.getElementById('result-box');
            const resultContent = document.getElementById('result-content');
            
            resultBox.className = 'result-box';
            resultBox.style.display = 'block';
            resultContent.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p style="margin-top: 1rem; color: #666;">Traitement en cours...</p>
                </div>
            `;
        }

        // Afficher le succès
        function showSuccess(data) {
            const resultBox = document.getElementById('result-box');
            const resultContent = document.getElementById('result-content');
            
            resultBox.className = 'result-box success';
            
            let rewardHtml = '';
            if (data.produits_disponibles > 0 || data.coupes_disponibles > 0) {
                rewardHtml = `
                    <div class="reward-available">
                        🎉 Récompenses disponibles !
                        ${data.produits_disponibles > 0 ? ` ${data.produits_disponibles} produit(s)` : ''}
                        ${data.coupes_disponibles > 0 ? ` ${data.coupes_disponibles} coupe(s)` : ''}
                    </div>
                `;
            }
            
            resultContent.innerHTML = `
                <div class="result-icon">✅</div>
                <div class="result-title">Point ajouté !</div>
                
                <div class="client-info">
                    <div class="client-info-row">
                        <span class="client-info-label">Client</span>
                        <span class="client-info-value">${data.client_nom}</span>
                    </div>
                    <div class="client-info-row">
                        <span class="client-info-label">Email</span>
                        <span class="client-info-value">${data.client_email}</span>
                    </div>
                </div>
                
                <div class="points-display">
                    <div class="points-item">
                        <div class="points-value">${data.points_total}</div>
                        <div class="points-label">Points totaux</div>
                    </div>
                </div>
                
                ${rewardHtml}
                
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="resetScanner()">Scanner un autre code</button>
                    <a href="dashboard.php" class="btn btn-secondary">Retour au dashboard</a>
                </div>
            `;
        }

        // Afficher une erreur
        function showError(message) {
            const resultBox = document.getElementById('result-box');
            const resultContent = document.getElementById('result-content');
            
            resultBox.className = 'result-box error';
            
            resultContent.innerHTML = `
                <div class="result-icon">❌</div>
                <div class="result-title">Erreur</div>
                <p style="color: #666; margin: 1rem 0;">${message}</p>
                
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="resetScanner()">Réessayer</button>
                </div>
            `;
        }

        // Réinitialiser le scanner
        function resetScanner() {
            document.getElementById('result-box').style.display = 'none';
            lastScannedCode = '';
        }

        // Auto-formatage de la saisie manuelle
        document.getElementById('manual-code').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });

        // Démarrer le scanner au chargement
        window.addEventListener('load', () => {
            initScanner();
        });

        // Nettoyer le scanner à la fermeture
        window.addEventListener('beforeunload', () => {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop();
            }
        });
    </script>
</body>
</html>