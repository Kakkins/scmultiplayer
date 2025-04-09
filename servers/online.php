<?php
// File per gestire il conteggio degli utenti online
// Per ora restituisce un numero casuale tra 5 e 20

header('Content-Type: application/json');

// Genera un numero casuale di utenti online
$onlineCount = rand(5, 20);

// Restituisci il conteggio in formato JSON
echo json_encode(['count' => $onlineCount]);
?> 