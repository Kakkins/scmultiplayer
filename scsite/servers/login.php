<?php
// Assicurati che la sessione sia avviata
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug - Stampa informazioni sulla sessione
error_log("Login - Session info before login: " . print_r($_SESSION, true));

// Se l'utente è già loggato, reindirizza alla home
if (isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

// Gestione del form di login
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validazione
    if (empty($username) || empty($password)) {
        $error = 'Tutti i campi sono obbligatori';
    } else {
        // Connessione al database
        $db_path = __DIR__ . '/users.db';
        $db = new SQLite3($db_path);
        
        // Verifica le credenziali
        $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if ($user = $result->fetchArray(SQLITE3_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                // Login riuscito
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Debug - Stampa informazioni sulla sessione dopo il login
                error_log("Login successful - Session info after login: " . print_r($_SESSION, true));
                
                // Reindirizza alla pagina precedente o alla home
                $redirect = $_SESSION['redirect_after_login'] ?? '/index.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Password non corretta';
            }
        } else {
            $error = 'Username non trovato';
        }
        
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Survivalcraft</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-font: 'Alegreya Sans SC', sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--main-font) !important;
        }
        
        body, h1, h2, h3, h4, h5, h6, p, span, a, button, input, textarea, select, option, label, div {
            font-family: var(--main-font) !important;
        }

        body {
            background-color: #1a1a1a;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4CAF50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #888;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #1a1a1a;
            color: white;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            background-color: #f44336;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #4CAF50;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <a href="/index.html" class="back-link">← Torna alla home</a>
    
    <div class="container">
        <h1>Login</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Accedi</button>
        </form>
        
        <div class="register-link">
            Non hai un account? <a href="register.php">Registrati</a>
        </div>
    </div>
</body>
</html> 