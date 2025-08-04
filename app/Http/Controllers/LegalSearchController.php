<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LegalSearchController extends Controller
{
    public function index()
    { 
        $PageTitle = 'Legal Search - Official (for filing purpose)';
        $PageDescription = '';
        return view('legal_search.index', compact('PageTitle', 'PageDescription'));
    } 

    public function search(Request $request)
    {
        // Get all search parameters
        $fileNo = $request->input('query');
        $guarantorName = $request->input('guarantorName');
        $guaranteeName = $request->input('guaranteeName');
        $lga = $request->input('lga');
        $district = $request->input('district');
        $location = $request->input('location');
        $plotNumber = $request->input('plotNumber');
        $planNumber = $request->input('planNumber');
        $size = $request->input('size');
        $caveat = $request->input('caveat');

        // Check if at least one search parameter is provided
        $hasSearchCriteria = !empty($fileNo) || !empty($guarantorName) || !empty($guaranteeName) || 
                            !empty($lga) || !empty($district) || !empty($location) || 
                            !empty($plotNumber) || !empty($planNumber) || !empty($size) || !empty($caveat);

        if (!$hasSearchCriteria) {
            return response()->json([
                'property_records' => [],
                'registered_instruments' => [],
                'cofo' => [],
            ]);
        }

        // Search across the 3 specified tables only: property_records, registered_instruments, CofO
        
        // 1. property_records table
        $property_records = DB::connection('sqlsrv')->table('property_records')
            ->where(function($q) use ($fileNo, $guarantorName, $guaranteeName, $lga, $district, $location, $plotNumber, $planNumber, $size, $caveat) {
                // File number search
                if (!empty($fileNo)) {
                    $q->where(function($subQ) use ($fileNo) {
                        $subQ->whereRaw("UPPER(mlsFNo) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(kangisFileNo) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(NewKANGISFileno) LIKE UPPER(?)", ["%{$fileNo}%"]);
                    });
                }
                
                // Guarantor name search
                if (!empty($guarantorName)) {
                    $q->where(function($subQ) use ($guarantorName) {
                        $subQ->whereRaw("UPPER(Assignor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Grantor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Mortgagor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Lessor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Surrenderor) LIKE UPPER(?)", ["%{$guarantorName}%"]);
                    });
                }
                
                // Guarantee name search
                if (!empty($guaranteeName)) {
                    $q->where(function($subQ) use ($guaranteeName) {
                        $subQ->whereRaw("UPPER(Assignee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Grantee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Mortgagee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Lessee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Surrenderee) LIKE UPPER(?)", ["%{$guaranteeName}%"]);
                    });
                }
                
                // LGA search
                if (!empty($lga)) {
                    $q->whereRaw("UPPER(lgsaOrCity) LIKE UPPER(?)", ["%{$lga}%"]);
                }
                
                // District search
                if (!empty($district)) {
                    $q->whereRaw("UPPER(location) LIKE UPPER(?)", ["%{$district}%"]);
                }
                
                // Location search
                if (!empty($location)) {
                    $q->where(function($subQ) use ($location) {
                        $subQ->whereRaw("UPPER(location) LIKE UPPER(?)", ["%{$location}%"])
                             ->orWhereRaw("UPPER(property_description) LIKE UPPER(?)", ["%{$location}%"]);
                    });
                }
                
                // Plot number search
                if (!empty($plotNumber)) {
                    $q->whereRaw("UPPER(plot_no) LIKE UPPER(?)", ["%{$plotNumber}%"]);
                }
                
                // Size search
                if (!empty($size)) {
                    $q->whereRaw("UPPER(CAST(size AS VARCHAR)) LIKE UPPER(?)", ["%{$size}%"]);
                }
                
                // Caveat search
                if (!empty($caveat)) {
                    $q->whereRaw("UPPER(caveat) = UPPER(?)", [$caveat]);
                }
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        // 2. registered_instruments table
        $registered_instruments = DB::connection('sqlsrv')->table('registered_instruments')
            ->where(function($q) use ($fileNo, $guarantorName, $guaranteeName, $lga, $district, $location, $plotNumber, $planNumber, $size, $caveat) {
                // File number search
                if (!empty($fileNo)) {
                    $q->where(function($subQ) use ($fileNo) {
                        $subQ->whereRaw("UPPER(fileno) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(parent_fileNo) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(MLSFileNo) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(KAGISFileNO) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(NewKANGISFileNo) LIKE UPPER(?)", ["%{$fileNo}%"]);
                    });
                }
                
                // Guarantor name search
                if (!empty($guarantorName)) {
                    $q->where(function($subQ) use ($guarantorName) {
                        $subQ->whereRaw("UPPER(Grantor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(mortgagor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(assignor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(lessor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(surrenderor) LIKE UPPER(?)", ["%{$guarantorName}%"]);
                    });
                }
                
                // Guarantee name search
                if (!empty($guaranteeName)) {
                    $q->where(function($subQ) use ($guaranteeName) {
                        $subQ->whereRaw("UPPER(Grantee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(mortgagee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(assignee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(lessee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(surrenderee) LIKE UPPER(?)", ["%{$guaranteeName}%"]);
                    });
                }
                
                // LGA search
                if (!empty($lga)) {
                    $q->whereRaw("UPPER(lga) LIKE UPPER(?)", ["%{$lga}%"]);
                }
                
                // District search
                if (!empty($district)) {
                    $q->whereRaw("UPPER(district) LIKE UPPER(?)", ["%{$district}%"]);
                }
                
                // Location search
                if (!empty($location)) {
                    $q->where(function($subQ) use ($location) {
                        $subQ->whereRaw("UPPER(propertyAddress) LIKE UPPER(?)", ["%{$location}%"])
                             ->orWhereRaw("UPPER(propertyDescription) LIKE UPPER(?)", ["%{$location}%"]);
                    });
                }
                
                // Plot number search
                if (!empty($plotNumber)) {
                    $q->where(function($subQ) use ($plotNumber) {
                        $subQ->whereRaw("UPPER(plotNumber) LIKE UPPER(?)", ["%{$plotNumber}%"])
                             ->orWhereRaw("UPPER(plotNo) LIKE UPPER(?)", ["%{$plotNumber}%"]);
                    });
                }
                
                // Size search
                if (!empty($size)) {
                    $q->whereRaw("UPPER(CAST(size AS VARCHAR)) LIKE UPPER(?)", ["%{$size}%"]);
                }
            })
            ->orderBy('deeds_date', 'desc')
            ->get();

        // 3. CofO table
        $cofo = DB::connection('sqlsrv')->table('CofO')
            ->where(function($q) use ($fileNo, $guarantorName, $guaranteeName, $lga, $district, $location, $plotNumber, $planNumber, $size, $caveat) {
                // File number search
                if (!empty($fileNo)) {
                    $q->where(function($subQ) use ($fileNo) {
                        $subQ->whereRaw("UPPER(mlsFNo) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(kangisFileNo) LIKE UPPER(?)", ["%{$fileNo}%"])
                             ->orWhereRaw("UPPER(NewKANGISFileno) LIKE UPPER(?)", ["%{$fileNo}%"]);
                    });
                }
                
                // Guarantor name search
                if (!empty($guarantorName)) {
                    $q->where(function($subQ) use ($guarantorName) {
                        $subQ->whereRaw("UPPER(Assignor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Grantor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Mortgagor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Lessor) LIKE UPPER(?)", ["%{$guarantorName}%"])
                             ->orWhereRaw("UPPER(Surrenderor) LIKE UPPER(?)", ["%{$guarantorName}%"]);
                    });
                }
                
                // Guarantee name search
                if (!empty($guaranteeName)) {
                    $q->where(function($subQ) use ($guaranteeName) {
                        $subQ->whereRaw("UPPER(Assignee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Grantee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Mortgagee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Lessee) LIKE UPPER(?)", ["%{$guaranteeName}%"])
                             ->orWhereRaw("UPPER(Surrenderee) LIKE UPPER(?)", ["%{$guaranteeName}%"]);
                    });
                }
                
                // LGA search
                if (!empty($lga)) {
                    $q->whereRaw("UPPER(lgsaOrCity) LIKE UPPER(?)", ["%{$lga}%"]);
                }
                
                // District search
                if (!empty($district)) {
                    $q->whereRaw("UPPER(location) LIKE UPPER(?)", ["%{$district}%"]);
                }
                
                // Location search
                if (!empty($location)) {
                    $q->where(function($subQ) use ($location) {
                        $subQ->whereRaw("UPPER(location) LIKE UPPER(?)", ["%{$location}%"])
                             ->orWhereRaw("UPPER(property_description) LIKE UPPER(?)", ["%{$location}%"]);
                    });
                }
                
                // Plot number search
                if (!empty($plotNumber)) {
                    $q->whereRaw("UPPER(plot_no) LIKE UPPER(?)", ["%{$plotNumber}%"]);
                }
                
                // Size search
                if (!empty($size)) {
                    $q->whereRaw("UPPER(CAST(size AS VARCHAR)) LIKE UPPER(?)", ["%{$size}%"]);
                }
                
                // Caveat search
                if (!empty($caveat)) {
                    $q->whereRaw("UPPER(caveat) = UPPER(?)", [$caveat]);
                }
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json([
            'property_records' => $property_records,
            'registered_instruments' => $registered_instruments,
            'cofo' => $cofo,
        ]);
    }

    public function report()
    {
        return view('legal_search.report');
    }

    public function legal_search_report()
    {
        $PageTitle = 'Legal Search Report';
        return view('legal_search.legal_search_report');
    }
    
    /**
     * Check if the query looks like a file number pattern
     * 
     * @param string $query
     * @return bool
     */
    private function isFileNumberPattern($query)
    {
        $trimmedQuery = trim($query);
        
        // Only consider it a file number if it has more specific patterns
        $patterns = [
            '/^ST-\d+/i',        // ST- followed by numbers
            '/^RES-\d+/i',       // RES- followed by numbers  
            '/^COM-\d+/i',       // COM- followed by numbers
            '/^IND-\d+/i',       // IND- followed by numbers
            '/^MLS-\d+/i',       // MLS- followed by numbers
            '/^KAN-\d+/i',       // KAN- followed by numbers
            '/^KANGIS-\d+/i',    // KANGIS- followed by numbers
            '/^[A-Z]{2,4}-\d+/i', // Pattern like XX-123 or XXX-123
            '/^\d{4}\/\d+/i',    // Pattern like 2023/123
            '/^[A-Z]+\d{3,}/i',  // Pattern like ABC123 (at least 3 digits)
        ];
        
        // If it's just a prefix like "ST-" without numbers, treat it as file number search
        if (preg_match('/^(ST|RES|COM|IND|MLS|KAN|KANGIS)-$/i', $trimmedQuery)) {
            return true;
        }
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $trimmedQuery)) {
                return true;
            }
        }
        
        return false;
    }
}