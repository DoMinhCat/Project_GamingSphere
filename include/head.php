<?php
$this_page = basename($_SERVER['PHP_SELF']);

error_reporting(E_ALL);
ini_set('display_errors', 0);  // Suppress errors from showing in the browser
ini_set('log_errors', 1);      // Log errors
ini_set('error_log', '/var/log/apache2/php_errors.log');  // Custom log location

// Custom error handler for non-fatal errors
function custom_error_handler($errno, $errstr, $errfile, $errline)
{
    // Log error details
    error_log("Error [$errno] $errstr in $errfile on line $errline");

    // Set HTTP status code to 500 and allow Apache to serve the custom page
    http_response_code(500);  // Ensures a 500 error is sent to the browser
    // Do not force a redirect in PHP, let Apache handle this.
    exit();
}
set_error_handler("custom_error_handler");

// Shutdown function to handle fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error) {
        // Log the fatal error details
        error_log("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}");

        // Set HTTP status code to 500 and allow Apache to serve the custom page
        http_response_code(500);  // Ensures a 500 error is sent to the browser
        // Let Apache handle the custom error page
        exit();
    }
});
?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/include/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/include/night_mode.js"></script>
    <?php if (!empty($_SESSION['user_email'])): ?>
        <script src="/status_user/status_online.js"></script>
    <?php endif; ?>
    <title><?= $title ?></title>
</head>