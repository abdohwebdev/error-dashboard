<?php
// Simple PHP error log parser

$logFile = __DIR__ . '/error.log';

$errors = [];

if (file_exists($logFile)) {
    $lines = file($logFile);
    foreach ($lines as $line) {
        // Match: [date] error_type: message in file:line
        if (preg_match('/\[(.*?)\]\s+PHP\s+(Fatal error|Warning|Notice|Deprecated):\s+(.*?)\s+in\s+([^\s]+)\s*(?:on line|:)\s*(\d+)/', $line, $matches)) {
            $errors[] = [
            'timestamp' => $matches[1],
            'type' => $matches[2],
            'message' => $matches[3],
            'file' => $matches[4],
            'line' => $matches[5],
            ];
        }
    }
}

// Output parsed errors as JSON
header('Content-Type: application/json');
echo json_encode($errors, JSON_PRETTY_PRINT);
