<?php
echo "Server is working!\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Laravel loaded: " . (class_exists('Illuminate\Foundation\Application') ? 'Yes' : 'No') . "\n";
?>
