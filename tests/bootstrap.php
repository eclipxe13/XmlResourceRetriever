<?php

declare(strict_types=1);

// report all errors
error_reporting(-1);

// require composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// create php web server for local resources
call_user_func(function (): void {
    // Command that starts the built-in web server
    $command = sprintf(
        'php -S %s:%d -t %s > /dev/null 2>&1 & echo $!',
        '127.0.0.1',
        '8999',
        escapeshellarg(__DIR__ . '/public/')
    );

    // Execute the command and store the process ID
    $output = [];
    exec($command, $output);
    if (! isset($output[0])) {
        trigger_error('Unable to start server using ' . $command, E_USER_ERROR);
    }
    $pid = (int) $output[0];

    // Kill the web server when the process ends
    register_shutdown_function(function () use ($pid): void {
        exec('kill ' . $pid);
    });

    // wait until server is responding
    do {
        usleep(10000); // wait 0.01 seconds before each try
        $headers = @get_headers('http://localhost:8999/README.md') ?: [];
        $httpResponse = '';
        if (isset($headers[0]) && is_scalar($headers[0])) {
            $httpResponse = (string) $headers[0];
        }
    } while (false === strpos($httpResponse, '200 OK'));
});
