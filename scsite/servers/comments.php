<?php
require_once 'auth_utils.php';

// Verifica se l'utente è autenticato per i commenti
$isLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();

// Debug - Stampa informazioni sulla sessione
error_log("Session info: " . print_r($_SESSION, true));
error_log("Is logged in: " . ($isLoggedIn ? "true" : "false"));
error_log("Current user: " . print_r($currentUser, true));

// Gestione del form di commento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $server_id = $_POST['server_id'] ?? '';
    $comment = $_POST['comment'] ?? '';
    
    if (!empty($server_id) && !empty($comment)) {
        // Verifica che l'utente sia autenticato
        if (!$isLoggedIn) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'You need to login to comment']);
            exit;
        }
        
        $db = new SQLite3(__DIR__ . '/comments.db');
        
        // Verifica se la tabella comments esiste
        $tableExists = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='comments'");
        
        if (!$tableExists) {
            // Crea la tabella comments se non esiste
            $db->exec('
                CREATE TABLE comments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    server_id TEXT NOT NULL,
                    user_id INTEGER,
                    username TEXT NOT NULL,
                    content TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ');
        } else {
            // Verifica se la colonna 'content' esiste
            $columns = $db->query("PRAGMA table_info(comments)");
            $commentColumnExists = false;
            while ($column = $columns->fetchArray(SQLITE3_ASSOC)) {
                if ($column['name'] === 'content') {
                    $commentColumnExists = true;
                    break;
                }
            }
            
            // Se la colonna 'content' non esiste, aggiungila
            if (!$commentColumnExists) {
                $db->exec('ALTER TABLE comments ADD COLUMN content TEXT NOT NULL DEFAULT ""');
                error_log("Added 'content' column to comments table");
            }
        }
        
        // Inserisci il commento
        $stmt = $db->prepare('INSERT INTO comments (server_id, user_id, username, content) VALUES (:server_id, :user_id, :username, :content)');
        if ($stmt) {
            $stmt->bindValue(':server_id', $server_id, SQLITE3_TEXT);
            $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
            $stmt->bindValue(':username', $currentUser['username'], SQLITE3_TEXT);
            $stmt->bindValue(':content', $comment, SQLITE3_TEXT);
            
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Error saving comment']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        
        $db->close();
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Missing fields']);
        exit;
    }
}

// Recupera i commenti
$server_id = $_GET['server_id'] ?? '';
if (!empty($server_id)) {
    $db = new SQLite3(__DIR__ . '/comments.db');
    
    // Verifica se la tabella comments esiste
    $tableExists = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='comments'");
    
    if (!$tableExists) {
        // Crea la tabella comments se non esiste
        $db->exec('
            CREATE TABLE comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                server_id TEXT NOT NULL,
                user_id INTEGER,
                username TEXT NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
    } else {
        // Verifica se la colonna 'content' esiste
        $columns = $db->query("PRAGMA table_info(comments)");
        $commentColumnExists = false;
        while ($column = $columns->fetchArray(SQLITE3_ASSOC)) {
            if ($column['name'] === 'content') {
                $commentColumnExists = true;
                break;
            }
        }
        
        // Se la colonna 'content' non esiste, aggiungila
        if (!$commentColumnExists) {
            $db->exec('ALTER TABLE comments ADD COLUMN content TEXT NOT NULL DEFAULT ""');
            error_log("Added 'content' column to comments table");
        }
    }
    
    $stmt = $db->prepare('SELECT * FROM comments WHERE server_id = :server_id ORDER BY created_at DESC');
    if ($stmt) {
        $stmt->bindValue(':server_id', $server_id, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        $comments = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            // Assicurati che il campo 'content' sia presente nel risultato
            if (!isset($row['content']) && isset($row['comment'])) {
                $row['content'] = $row['comment'];
            }
            $comments[] = $row;
        }
    } else {
        $comments = [];
    }
    
    $db->close();
    
    // Restituisci i commenti in formato JSON
    header('Content-Type: application/json');
    echo json_encode([
        'comments' => $comments,
        'isLoggedIn' => $isLoggedIn
    ]);
}
?> 