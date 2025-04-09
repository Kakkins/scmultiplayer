<?php
header('Content-Type: application/json');

// Percorso del database
$db_path = __DIR__ . '/comments.db';

try {
    // Connessione al database
    $db = new SQLite3($db_path);
    
    // Elimina tutti i commenti
    $query = "DELETE FROM comments";
    $result = $db->exec($query);
    
    if ($result !== false) {
        echo json_encode(['success' => true, 'message' => 'Tutti i commenti sono stati eliminati']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Errore durante l\'eliminazione dei commenti']);
    }
    
    // Chiudi la connessione
    $db->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 