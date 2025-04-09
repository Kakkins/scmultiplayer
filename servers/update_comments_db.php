<?php
// Percorso del database
$db_path = __DIR__ . '/comments.db';

try {
    // Connessione al database
    $db = new SQLite3($db_path);
    
    // Aggiungi la colonna user_id alla tabella comments se non esiste
    $query = "ALTER TABLE comments ADD COLUMN user_id INTEGER DEFAULT NULL";
    
    $result = $db->exec($query);
    
    if ($result !== false) {
        echo "Database commenti aggiornato con successo!";
    } else {
        // Se la colonna esiste già, SQLite restituirà un errore
        // Verifichiamo se la colonna esiste
        $check_query = "PRAGMA table_info(comments)";
        $columns = $db->query($check_query);
        $user_id_exists = false;
        
        while ($column = $columns->fetchArray(SQLITE3_ASSOC)) {
            if ($column['name'] == 'user_id') {
                $user_id_exists = true;
                break;
            }
        }
        
        if ($user_id_exists) {
            echo "La colonna user_id esiste già nel database commenti.";
        } else {
            echo "Errore durante l'aggiornamento del database commenti.";
        }
    }
    
    // Chiudi la connessione
    $db->close();
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
}
?> 