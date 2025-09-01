<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test the fixed query
    $buyers = DB::connection('sqlsrv')
        ->table('buyer_list as bl')
        ->leftJoin('st_unit_measurements as sum', function($join) {
            $join->on('bl.unit_no', '=', 'sum.unit_no')
                 ->on('bl.application_id', '=', 'sum.application_id');
        })
        ->where('bl.application_id', 1054)
        ->select('bl.id', 'bl.buyer_title', 'bl.buyer_name', 'bl.unit_no', 'bl.unit_measurement_id', 'sum.measurement')
        ->get();

    echo 'Buyers count with fixed query: ' . $buyers->count() . PHP_EOL;

    if ($buyers->count() > 0) {
        foreach($buyers as $buyer) {
            echo 'ID: ' . $buyer->id . ', Name: ' . ($buyer->buyer_title ?? '') . ' ' . ($buyer->buyer_name ?? '') . ', Unit: ' . ($buyer->unit_no ?? '') . ', Measurement: ' . ($buyer->measurement ?? 'N/A') . PHP_EOL;
        }
    }

} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
