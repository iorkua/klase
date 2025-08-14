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
}