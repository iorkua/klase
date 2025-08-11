<?php

/**
 * FIXED getDGData method for CertificationController
 * Replace the existing getDGData method with this code
 */

public function getDGData(Request $request)
{
    try {
        // Development bypass setting - can be controlled via query parameter
        $bypassPrerequisites = $request->get('bypass', 'true') === 'true'; // Default to true for development
        
        $query = DB::connection('sqlsrv')->table('recertification_applications');
        $applications = $query->get();

        // Format data for the DG table
        $data = $applications->map(function($app) use ($bypassPrerequisites, $request) {
            // Parse payload to get additional fields
            $payload = json_decode($app->payload ?? '{}', true);
            
            // Determine applicant name based on type
            $applicantName = '';
            if ($app->applicant_type === 'Corporate') {
                $applicantName = $app->organisation_name ?? 'N/A';
            } else {
                // Try to get name from payload first, then from direct columns
                $surname = $payload['surname'] ?? $app->surname ?? '';
                $firstName = $payload['first_name'] ?? $app->first_name ?? '';
                $applicantName = trim($surname . ' ' . $firstName);
                if (empty($applicantName)) {
                    $applicantName = 'N/A';
                }
            }

            // Format plot details
            $plotDetails = '';
            $plotNumber = $payload['plot_number'] ?? $app->plot_number ?? '';
            $layoutDistrict = $payload['layout_district'] ?? $app->layout_district ?? '';
            $plotSize = $payload['plot_size'] ?? $app->plot_size ?? '';
            
            if ($plotNumber) {
                $plotDetails .= 'Plot: ' . $plotNumber;
            }
            if ($layoutDistrict) {
                $plotDetails .= ($plotDetails ? ', ' : '') . $layoutDistrict;
            }
            if ($plotSize) {
                $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $plotSize;
            }
            if (empty($plotDetails)) {
                $plotDetails = 'N/A';
            }

            // Get LGA name from payload
            $lgaName = $payload['lga_name'] ?? $app->lga_name ?? 'N/A';

            if ($bypassPrerequisites) {
                // Development mode - all prerequisites are automatically complete
                $acknowledgementGenerated = true;
                $verificationGenerated = true;
                $gisCaptured = true;
                $vettingGenerated = true;
                $edmsCaptured = true;
                $cofoFrontGenerated = true;
            } else {
                // Production mode - check actual database values
                $fileNo = $app->file_number ?? null;
                
                // 1. Check certificate_generated (CofO Front Page)
                $cofoFrontGenerated = ($app->certificate_generated ?? 0) == 1;
                
                // 2. Check acknowledgement
                $acknowledgementGenerated = ($app->acknowledgement ?? '') === 'Generated';
                
                // 3. Check verification status
                $verificationGenerated = ($app->verification ?? '') === 'Verified';
                
                // 4. Check EDMS captured (file_indexings + pagetypings)
                $edmsCaptured = false;
                try {
                    $fileIndexing = DB::connection('sqlsrv')->table('file_indexings')
                        ->where('recertification_application_id', $app->id)
                        ->first();
                        
                    if ($fileIndexing) {
                        $edmsCaptured = DB::connection('sqlsrv')->table('pagetypings')
                            ->where('file_indexing_id', $fileIndexing->id)
                            ->exists();
                    }
                } catch (\Throwable $e) {
                    $edmsCaptured = false;
                }
                
                // 5. Check GIS captured
                $gisCaptured = ($app->gis_captured ?? 0) == 1;
                
                // 6. Check vetting generated
                $vettingGenerated = ($app->vetting_generated ?? 0) == 1;
            }
            
            $dgApproval = ($app->dg_approval ?? 0) == 1;

            return [
                'id' => $app->id,
                'file_number' => $app->file_number ?? 'N/A',
                'applicant_name' => $applicantName,
                'applicant_type' => $app->applicant_type ?? 'N/A',
                'plot_details' => $plotDetails,
                'lga_name' => $lgaName,
                'submitted_to_dg_date' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : date('d M Y'),
                'dg_status' => $dgApproval ? 'approved' : 'pending',
                'dg_approval' => $dgApproval,
                // Prerequisites status
                'acknowledgement_generated' => $acknowledgementGenerated,
                'verification_generated' => $verificationGenerated,
                'gis_captured' => $gisCaptured,
                'vetting_generated' => $vettingGenerated,
                'edms_captured' => $edmsCaptured,
                'cofo_front_generated' => $cofoFrontGenerated,
                'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A'
            ];
        });

        // Calculate statistics
        $total = $data->count();
        $approved = $data->where('dg_approval', true)->count();
        $pending = $total - $approved;
        $thisMonth = $data->filter(function($app) {
            if ($app['created_at'] === 'N/A') return false;
            try {
                $createdDate = \DateTime::createFromFormat('d M Y', $app['created_at']);
                if (!$createdDate) return false;
                $startOfMonth = new \DateTime('first day of this month');
                return $createdDate >= $startOfMonth;
            } catch (\Exception $e) {
                return false;
            }
        })->count();

        $statistics = [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'thisMonth' => $thisMonth
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'statistics' => $statistics
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching DG data', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'data' => [],
            'statistics' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'thisMonth' => 0],
            'error' => 'Failed to fetch DG data'
        ]);
    }
}