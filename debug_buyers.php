<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test both queries
    echo "=== Original query (with where in join) ===\n";
    $buyers1 = DB::connection('sqlsrv')
        ->table('buyer_list as bl')
        ->leftJoin('st_unit_measurements as sum', function($join) {
            $join->on('bl.unit_no', '=', 'sum.unit_no')
                 ->where('sum.application_id', '=', 1054);
        })
        ->where('bl.application_id', 1054)
        ->select('bl.id', 'bl.buyer_title', 'bl.buyer_name', 'bl.unit_no', 'bl.unit_measurement_id', 'sum.measurement')
        ->get();

    echo 'Buyers count: ' . $buyers1->count() . PHP_EOL;

    echo "\n=== Fixed query (with on in join) ===\n";
    $buyers2 = DB::connection('sqlsrv')
        ->table('buyer_list as bl')
        ->leftJoin('st_unit_measurements as sum', function($join) {
            $join->on('bl.unit_no', '=', 'sum.unit_no')
                 ->on('bl.application_id', '=', 'sum.application_id');
        })
        ->where('bl.application_id', 1054)
        ->select('bl.id', 'bl.buyer_title', 'bl.buyer_name', 'bl.unit_no', 'bl.unit_measurement_id', 'sum.measurement')
        ->get();

    echo 'Buyers count: ' . $buyers2->count() . PHP_EOL;

    // Check st_unit_measurements table structure
    echo "\n=== st_unit_measurements table for app 1054 ===\n";
    $measurements = DB::connection('sqlsrv')->table('st_unit_measurements')->where('application_id', 1054)->get();
    echo 'Measurements count: ' . $measurements->count() . PHP_EOL;
    if ($measurements->count() > 0) {
        foreach($measurements as $m) {
            echo 'Unit: ' . $m->unit_no . ', Measurement: ' . $m->measurement . PHP_EOL;
        }
    }

} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
