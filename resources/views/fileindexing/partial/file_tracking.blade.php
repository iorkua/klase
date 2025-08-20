<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kano Land State Registry - File Tracking Sheet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 0.5in;
            }
            body {
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="bg-white font-sans text-xs">
    <div class="max-w-full mx-auto bg-white border border-black">
        <!-- Added two logos side by side at the top of header -->
        <div class="p-2 border-b border-black">
            <!-- Two logos side by side -->
            <div class="flex justify-center items-center gap-8 mb-3">
                <img src="/placeholder.svg?height=60&width=60" alt="Kano State Logo" class="w-15 h-15">
                <img src="/placeholder.svg?height=60&width=60" alt="Land Registry Logo" class="w-15 h-15">
            </div>
            
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h1 class="text-sm font-bold">KANO LAND STATE REGISTRY</h1>
                    <h2 class="text-xs">FILE TRACKING SHEET</h2>
                </div>
                <div class="text-right text-xs">
                    <p class="font-bold">Tracking ID: TRK-2023-001</p>
                    <p>Generated: 6/19/2023, 1:36:11 PM</p>
                </div>
            </div>
        </div>

        <div class="p-3">
            <!-- Added file icon and restructured file details section -->
            <div class="grid grid-cols-12 gap-4 mb-4">
                <!-- File Icon and Details -->
                <div class="col-span-8">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-10 bg-gray-200 border border-gray-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold mb-1">File Details</h3>
                            <p class="text-xs font-semibold mb-2">Certificate of Occupancy-Alhaji Ibrahim Dantata</p>
                            
                            <!-- Added status buttons -->
                            <div class="flex gap-2 mb-3">
                                <span class="bg-blue-600 text-white px-2 py-1 text-xs rounded">Status: In Process</span>
                                <span class="bg-gray-500 text-white px-2 py-1 text-xs rounded">Priority: Normal</span>
                            </div>

                            <h4 class="text-xs font-bold mb-1">File Information</h4>
                            <div class="grid grid-cols-2 gap-x-8 text-xs">
                                <div>
                                    <p><span class="font-semibold">MLSF Number:</span></p>
                                    <p><span class="font-semibold">KANGIS Number:</span></p>
                                    <p><span class="font-semibold">New KANGIS:</span></p>
                                    <p><span class="font-semibold">Date Received:</span></p>
                                    <p><span class="font-semibold">Due Date:</span></p>
                                </div>
                                <div>
                                    <p>RES-2015-4859</p>
                                    <p>KNGP 00338</p>
                                    <p>KNO001</p>
                                    <p>2023-06-15</p>
                                    <p>2023-06-30</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Updated QR Code section to match screenshot -->
                <div class="col-span-4">
                    <h3 class="text-xs font-bold mb-1">QR Code</h3>
                    <div class="border border-gray-400 p-2 text-center">
                        <img src="/placeholder.svg?height=80&width=80" alt="QR Code" class="w-20 h-20 mx-auto mb-2 border">
                        <p class="text-xs">Contains file details</p>
                        <p class="text-xs font-semibold">MLSF:RES-2015-4859</p>
                        <p class="text-xs">📱 RFID:00253478</p>
                    </div>
                </div>
            </div>

            <!-- Updated Current Location section -->
            <div class="mb-4">
                <h3 class="text-xs font-bold mb-1">Current Location</h3>
                <div class="grid grid-cols-4 gap-4 text-xs">
                    <div>
                        <p class="font-semibold">Customer Care Unit</p>
                        <p>Last updated:2023-06-16</p>
                    </div>
                    <div>
                        <p class="font-semibold">Aisha Mohammed</p>
                        <p>Current handler</p>
                    </div>
                    <div>
                        <p class="font-semibold">2023-06-15 11:45 AM</p>
                        <p>Last RFID scan</p>
                    </div>
                    <div></div>
                </div>
            </div>

            <!-- Movement History -->
            <div class="mb-4">
                <h3 class="text-xs font-bold mb-2">Movement History</h3>
                <table class="w-full border-collapse border border-black text-xs">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black p-1 text-left font-bold">Date & Time</th>
                            <th class="border border-black p-1 text-left font-bold">Location</th>
                            <th class="border border-black p-1 text-left font-bold">Handler</th>
                            <th class="border border-black p-1 text-left font-bold">Action</th>
                            <th class="border border-black p-1 text-left font-bold">Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black p-1">2023-06-15 09:30 AM</td>
                            <td class="border border-black p-1">Reception</td>
                            <td class="border border-black p-1">Fatima Usman</td>
                            <td class="border border-black p-1">File received and registered</td>
                            <td class="border border-black p-1">Manual</td>
                        </tr>
                        <tr>
                            <td class="border border-black p-1">2023-06-15 11:45 AM</td>
                            <td class="border border-black p-1">Customer Care Unit</td>
                            <td class="border border-black p-1">Aisha Mohammed</td>
                            <td class="border border-black p-1">File assigned for processing</td>
                            <td class="border border-black p-1">RFID Scan</td>
                        </tr>
                        <tr>
                            <td class="border border-black p-1">2023-06-16 02:15 PM</td>
                            <td class="border border-black p-1">Legal Department</td>
                            <td class="border border-black p-1">Musa Abdullahi</td>
                            <td class="border border-black p-1">Legal review initiated</td>
                            <td class="border border-black p-1">RFID Scan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Updated signature section layout -->
            <div class="grid grid-cols-2 gap-8 mb-4">
                <div>
                    <h3 class="text-xs font-bold mb-2">Signature</h3>
                    <div class="h-16 border-b border-black mb-1"></div>
                    <p class="text-xs">Authorized Signature</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold mb-2">Notes</h3>
                    <div class="h-16 mb-1"></div>
                    <p class="text-xs text-center">Documents under review</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div>
                    <div class="border-b border-black mb-1"></div>
                    <p class="text-xs">Date:</p>
                </div>
                <div></div>
            </div>
        </div>

        <!-- Updated footer to match screenshot -->
        <div class="border-t border-black p-2 text-xs">
            <div class="flex justify-between">
                <div>
                    <p class="font-bold">KANO STATE LAND REGISTRY</p>
                    <p>File Tracking System</p>
                </div>
                <div class="text-right">
                    <p>This tracking sheet should accompany the file at all times.</p>
                    <p>For inquiries, contact File Management Office at ext.2145.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
