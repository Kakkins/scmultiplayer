<?php
// Percorso del database
$db_path = __DIR__ . '/users.db';

try {
    // Connessione al database
    $db = new SQLite3($db_path);
    
    // Crea la tabella degli utenti
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $result = $db->exec($query);
    
    if ($result !== false) {
        echo "Database utenti creato con successo!";
    } else {
        echo "Errore durante la creazione del database utenti.";
    }
    
    // Chiudi la connessione
    $db->close();
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
}
?> 