<?php
// Percorso del database
$db_path = __DIR__ . '/users.db';

// Crea una nuova connessione al database
$db = new SQLite3($db_path);

// Crea la tabella users se non esiste
$db->exec('
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
');

echo "Database inizializzato con successo!";
$db->close();
?> 