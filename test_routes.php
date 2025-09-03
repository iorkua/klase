<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test if the route exists
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    
    echo "Testing pagetyping.api.stats route...\n";
    
    $found = false;
    foreach ($routes as $route) {
        if ($route->getName() === 'pagetyping.api.stats') {
            echo "✓ Found route: " . $route->getName() . "\n";
            echo "  URI: " . $route->uri() . "\n";
            echo "  Methods: " . implode(', ', $route->methods()) . "\n";
            echo "  Controller: " . $route->getActionName() . "\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "✗ Route 'pagetyping.api.stats' not found\n";
        echo "Available pagetyping routes:\n";
        foreach ($routes as $route) {
            if (strpos($route->getName() ?? '', 'pagetyping') !== false) {
                echo "  - " . $route->getName() . " (" . $route->uri() . ")\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
