<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getOnlineUsersCount() {
    $db = new SQLite3('users.db');

    // Crea la tabella solo se non esiste
    $db->exec('CREATE TABLE IF NOT EXISTS online_users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        last_activity INTEGER NOT NULL,
        session_id TEXT
    )');

    $username = $_SESSION['username'] ?? null;
    if ($username) {
        $session_id = session_id();
        $current_time = time();

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM online_users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row['count'] == 0) {
            $stmt = $db->prepare("INSERT INTO online_users (username, last_activity, session_id) VALUES (:username, :last_activity, :session_id)");
        } else {
            $stmt = $db->prepare("UPDATE online_users SET last_activity = :last_activity, session_id = :session_id WHERE username = :username");
        }

        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':last_activity', $current_time, SQLITE3_INTEGER);
        $stmt->bindValue(':session_id', $session_id, SQLITE3_TEXT);
        $stmt->execute();
    }

    // Elimina gli utenti inattivi da più di 5 minuti
    $timeout = time() - 300;
    $db->exec("DELETE FROM online_users WHERE last_activity < $timeout");

    // Conta gli utenti online
    $result = $db->query("SELECT COUNT(*) as count FROM online_users");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    return $row['count'];
}
?>

