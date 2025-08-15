<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
 
class CertificationController extends Controller
{
    /**
     * Show certification management page
     */
    public function index()
    {
        return view('recertification.certification');
    }

    /**
     * Get certification data for the certification management page
     */
    public function getCertificationData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the certification table
            $data = $applications->map(function($app) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if ($app->plot_size) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $app->plot_size;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                // Check if certificate is generated
                $certificateGenerated = $app->certificate_generated ?? false;

                return [
                    'id' => $app->id,
                    'cofO_serialNo' => $app->cofo_number ?? 'N/A',
                    'NewKANGISFileno' => $app->NewKANGISFileno ?? 'N/A',
                    'kangisFileNo' => $app->kangisFileNo ?? 'N/A',
                    'mlsfNo' => $app->mlsfNo ?? 'N/A',
                    'reg_no' => $app->reg_no ?? 'N/A',
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'certificate_generated' => $certificateGenerated,
                    'certificate_generated_date' => $app->certificate_generated_date ? date('d M Y', strtotime($app->certificate_generated_date)) : null,
                    'cofo_number' => $app->cofo_number ?? 'N/A',
                    'cofo_serial_no' => $app->cofo_serial_no ?? 'N/A',
                    'serial_no' => $app->serial_no ?? 'N/A',
                    'reg_page' => $app->reg_page ?? 'N/A',
                    'reg_volume' => $app->reg_volume ?? 'N/A',
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $generated = $data->where('certificate_generated', true)->count();
            $pending = $total - $generated;
            $thisMonth = $data->where('created_at', '>=', now()->startOfMonth()->format('d M Y'))->count();

            $statistics = [
                'total' => $total,
                'generated' => $generated,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching certification data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'generated' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch certification data'
            ]);
        }
    }

    /**
     * Show Bills & Payments page
     */
    public function billsPayments()
    {
        return view('recertification.bills_payments');
    }

    /**
     * Get Bills & Payments data
     */
    public function getBillsPaymentsData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the Bills & Payments table
            $data = $applications->map(function($app) {
                // Parse payload to get payment information
                $payload = json_decode($app->payload ?? '{}', true);
                
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Get payment information from payload or direct columns
                $paymentMethod = $payload['payment_method'] ?? $app->payment_method ?? 'N/A';
                $receiptNo = $payload['receipt_no'] ?? $app->receipt_no ?? 'N/A';
                $bankName = $payload['bank_name'] ?? $app->bank_name ?? 'N/A';
                $paymentAmount = $payload['payment_amount'] ?? $app->payment_amount ?? 0;
                $paymentDate = $payload['payment_date'] ?? $app->payment_date ?? $app->created_at;

                return [
                    'id' => $app->id,
                    'applicant_name' => $applicantName,
                    'payment_method' => $paymentMethod,
                    'receipt_no' => $receiptNo,
                    'bank_name' => $bankName,
                    'payment_amount' => floatval($paymentAmount),
                    'payment_date' => $paymentDate,
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A'
                ];
            });

            // Filter out records without payment information
            $data = $data->filter(function($payment) {
                return $payment['payment_amount'] > 0 || 
                       $payment['receipt_no'] !== 'N/A' || 
                       $payment['payment_method'] !== 'N/A';
            });

            // Calculate statistics
            $total = $data->count();
            $totalAmount = $data->sum('payment_amount');
            $avgAmount = $total > 0 ? $totalAmount / $total : 0;
            
            // Count payments this month
            $thisMonth = $data->filter(function($payment) {
                if ($payment['payment_date'] === 'N/A') return false;
                try {
                    $paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s', $payment['payment_date']);
                    if (!$paymentDate) {
                        $paymentDate = \DateTime::createFromFormat('Y-m-d', $payment['payment_date']);
                    }
                    if (!$paymentDate) return false;
                    
                    $startOfMonth = new \DateTime('first day of this month');
                    return $paymentDate >= $startOfMonth;
                } catch (\Exception $e) {
                    return false;
                }
            })->count();

            $statistics = [
                'total' => $total,
                'totalAmount' => $totalAmount,
                'avgAmount' => $avgAmount,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching Bills & Payments data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'totalAmount' => 0, 'avgAmount' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch Bills & Payments data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export payments data
     */
    public function exportPayments(Request $request)
    {
        try {
            // Get the payments data
            $response = $this->getBillsPaymentsData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if (!$responseData['success']) {
                return redirect()->back()->with('error', 'Failed to export payments data');
            }
            
            $payments = $responseData['data'];
            
            // Create CSV content
            $csvContent = "SN,Applicant Name,Payment Method,Receipt No,Bank Name,Payment Amount,Payment Date\n";
            
            foreach ($payments as $index => $payment) {
                $csvContent .= sprintf(
                    "%d,%s,%s,%s,%s,%.2f,%s\n",
                    $index + 1,
                    '"' . str_replace('"', '""', $payment['applicant_name']) . '"',
                    '"' . str_replace('"', '""', $payment['payment_method']) . '"',
                    '"' . str_replace('"', '""', $payment['receipt_no']) . '"',
                    '"' . str_replace('"', '""', $payment['bank_name']) . '"',
                    $payment['payment_amount'],
                    $payment['payment_date']
                );
            }
            
            // Return CSV download
            $filename = 'recertification_payments_' . date('Y-m-d_H-i-s') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            Log::error('Error exporting payments', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to export payments data');
        }
    }

    /**
     * Show EDMS page
     */
    public function edms()
    {
        return view('recertification.edms');
    }

    /**
     * Get EDMS data for the EDMS management page
     */
    public function getEDMSData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the EDMS table
            $data = $applications->map(function($app) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if ($app->plot_size) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $app->plot_size;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                // Check EDMS status by looking for file indexing records
                $edmsStatus = 'pending';
                $documentCount = 0;
                $lastUpdated = $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A';

                try {
                    // Check if there's a file indexing record for this application
                    $fileIndexing = DB::connection('sqlsrv')->table('file_indexings')
                        ->where('recertification_application_id', $app->id)
                        ->first();

                    if ($fileIndexing) {
                        // Check for scanned documents
                        $scannings = DB::connection('sqlsrv')->table('scannings')
                            ->where('file_indexing_id', $fileIndexing->id)
                            ->get();

                        $documentCount = $scannings->count();

                        if ($documentCount > 0) {
                            // Check if page typing is complete
                            $pageTypings = DB::connection('sqlsrv')->table('pagetypings')
                                ->where('file_indexing_id', $fileIndexing->id)
                                ->get();

                            if ($pageTypings->count() > 0) {
                                $edmsStatus = 'digitized';
                            } else {
                                $edmsStatus = 'scanning';
                            }
                        } else {
                            $edmsStatus = 'indexing';
                        }

                        $lastUpdated = $fileIndexing->updated_at ? date('d M Y', strtotime($fileIndexing->updated_at)) : $lastUpdated;
                    }
                } catch (\Exception $e) {
                    Log::warning('Error checking EDMS status for application ' . $app->id, [
                        'error' => $e->getMessage()
                    ]);
                }

                return [
                    'id' => $app->id,
                    'cofO_serialNo' => $app->cofo_number ?? 'N/A',
                    'NewKANGISFileno' => $app->NewKANGISFileno ?? 'N/A',
                    'kangisFileNo' => $app->kangisFileNo ?? 'N/A',
                    'mlsfNo' => $app->mlsfNo ?? 'N/A',
                    'reg_no' => $app->reg_no ?? 'N/A',
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'cofo_number' => $app->cofo_number ?? 'N/A',
                    'edms_status' => $edmsStatus,
                    'document_count' => $documentCount,
                    'last_updated' => $lastUpdated,
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $digitized = $data->where('edms_status', 'digitized')->count();
            $pending = $data->where('edms_status', 'pending')->count();
            $thisMonth = $data->where('created_at', '>=', now()->startOfMonth()->format('d M Y'))->count();

            $statistics = [
                'total' => $total,
                'digitized' => $digitized,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching EDMS data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'digitized' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch EDMS data'
            ]);
        }
    }

    /**
     * View Confirmation of Registration of Instrument (CoR)
     */
    public function viewCoR($id)
    {
        try {
            // Get the application data
            $application = DB::connection('sqlsrv')->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return redirect()->route('recertification.certification')
                    ->with('error', 'Application not found');
            }

            // Return the CORI view with the application data
            return view('recertification.cori', compact('application'));

        } catch (\Exception $e) {
            Log::error('Error viewing CoR for application ' . $id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('recertification.certification')
                ->with('error', 'Failed to load Confirmation of Registration');
        }
    }

    /**
     * Generate Certificate of Occupancy Front Page
     */
    public function generateCofoFrontPage($id)
    {
        try {
            // Get the application data
            $application = DB::connection('sqlsrv')->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Check if certificate is already generated
            if ($application->certificate_generated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate has already been generated for this application'
                ]);
            }

            // Generate certificate with current timestamp
            $certificateGeneratedDate = now()->format('Y-m-d H:i:s.v');
            
            // Update the application record
            DB::connection('sqlsrv')->table('recertification_applications')
                ->where('id', $id)
                ->update([
                    'certificate_generated' => true,
                    'certificate_generated_date' => $certificateGeneratedDate,
                    // Set default values if not already set
                    'serial_no' => $application->serial_no ?: $application->cofo_number ?: '1',
                    'reg_page' => $application->reg_page ?: '1',
                    'reg_volume' => $application->reg_volume ?: '1',
                    'updated_at' => now()
                ]);

            Log::info('Certificate generated for recertification application', [
                'application_id' => $id,
                'certificate_generated_date' => $certificateGeneratedDate
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate of Occupancy Front Page generated successfully',
                'certificate_generated_date' => $certificateGeneratedDate
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating CofO Front Page for application ' . $id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Certificate of Occupancy Front Page: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update certificate registration details
     */
    public function updateCertificateDetails(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'serial_no' => 'required|string|max:50',
                'reg_page' => 'required|string|max:50',
                'reg_volume' => 'required|string|max:50',
                'certificate_generated_date' => 'nullable|date'
            ]);

            // Get the application data
            $application = DB::connection('sqlsrv')->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Update the certificate details
            DB::connection('sqlsrv')->table('recertification_applications')
                ->where('id', $id)
                ->update([
                    'serial_no' => $validatedData['serial_no'],
                    'reg_page' => $validatedData['reg_page'],
                    'reg_volume' => $validatedData['reg_volume'],
                    'certificate_generated_date' => $validatedData['certificate_generated_date'] ?: $application->certificate_generated_date,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate details updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating certificate details for application ' . $id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update certificate details: ' . $e->getMessage()
            ], 500);
        }
    }
}