<?php
/*
 * Entry point of the REST API. Every request to /backend/... ends here (see .htaccess).
 * Milestone 1: only proves that PHP runs. FlightPHP routes come in milestone 3.
 */
header('Content-Type: application/json');
echo json_encode([
    'app' => 'Sofra API',
    'message' => 'The backend is running.'
]);
