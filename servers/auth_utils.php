<?php
// Assicurati che la sessione sia avviata
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug - Stampa informazioni sulla sessione
error_log("Auth utils - Session info: " . print_r($_SESSION, true));

function isLoggedIn() {
    $loggedIn = isset($_SESSION['user_id']);
    error_log("isLoggedIn() called, result: " . ($loggedIn ? "true" : "false"));
    return $loggedIn;
}

function getCurrentUser() {
    if (isLoggedIn()) {
        $user = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username']
        ];
        error_log("getCurrentUser() called, returning: " . print_r($user, true));
        return $user;
    }
    error_log("getCurrentUser() called, user not logged in, returning null");
    return null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /servers/login.php');
        exit;
    }
}
?> 