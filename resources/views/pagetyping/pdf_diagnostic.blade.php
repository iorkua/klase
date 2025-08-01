<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        button { padding: 10px 15px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; max-height: 300px; }
        .url-test { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 3px; }
        .hex-dump { font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>PDF Diagnostic Tool</h1>
    
    <div class="test-section info">
        <h3>PDF File Analysis</h3>
        <p>This tool performs comprehensive PDF file analysis to identify loading issues.</p>
        <p><strong>Test PDF URL:</strong> <input type="text" id="pdf-url" value="/storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf" style="width: 500px;"></p>
        <button onclick="runAllTests()">Run All Tests</button>
    </div>

    <div class="test-section">
        <h3>1. HTTP Response Analysis</h3>
        <button onclick="testHttpResponse()">Test HTTP Response</button>
        <div id="http-response-result"></div>
    </div>

    <div class="test-section">
        <h3>2. File Content Analysis</h3>
        <button onclick="testFileContent()">Analyze File Content</button>
        <div id="file-content-result"></div>
    </div>

    <div class="test-section">
        <h3>3. PDF Header Validation</h3>
        <button onclick="testPdfHeader()">Check PDF Header</button>
        <div id="pdf-header-result"></div>
    </div>

    <div class="test-section">
        <h3>4. CORS and Security Headers</h3>
        <button onclick="testCorsHeaders()">Test CORS Headers</button>
        <div id="cors-headers-result"></div>
    </div>

    <div class="test-section">
        <h3>5. PDF.js Detailed Loading</h3>
        <button onclick="testPdfJsDetailed()">Test PDF.js Loading</button>
        <div id="pdfjs-detailed-result"></div>
    </div>

    <div class="test-section">
        <h3>6. Alternative PDF Loading Methods</h3>
        <button onclick="testAlternativeMethods()">Test Alternative Methods</button>
        <div id="alternative-methods-result"></div>
    </div>

    <div class="test-section">
        <h3>7. Server-Side File Check</h3>
        <button onclick="testServerSideFile()">Check Server-Side File</button>
        <div id="server-side-result"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configure PDF.js worker
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        function showResult(elementId, type, title, message, data = null) {
            const element = document.getElementById(elementId);
            element.className = type;
            element.innerHTML = `
                <h4>${getIcon(type)} ${title}</h4>
                <p>${message}</p>
                ${data ? `<pre>${typeof data === 'string' ? data : JSON.stringify(data, null, 2)}</pre>` : ''}
            `;
        }

        function getIcon(type) {
            switch(type) {
                case 'success': return '✅';
                case 'error': return '❌';
                case 'warning': return '⚠️';
                case 'info': return 'ℹ️';
                default: return '📋';
            }
        }

        async function runAllTests() {
            console.log('Running all PDF diagnostic tests...');
            await testHttpResponse();
            await testFileContent();
            await testPdfHeader();
            await testCorsHeaders();
            await testPdfJsDetailed();
            await testAlternativeMethods();
            await testServerSideFile();
        }

        async function testHttpResponse() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const response = await fetch(pdfUrl);
                
                const headers = {};
                for (let [key, value] of response.headers.entries()) {
                    headers[key] = value;
                }
                
                const contentLength = response.headers.get('content-length');
                const contentType = response.headers.get('content-type');
                
                if (response.ok) {
                    showResult('http-response-result', 'success', 'HTTP Response OK', 
                        `Status: ${response.status}, Content-Length: ${contentLength} bytes, Content-Type: ${contentType}`, 
                        { status: response.status, headers: headers });
                } else {
                    showResult('http-response-result', 'error', 'HTTP Response Error', 
                        `Status: ${response.status} ${response.statusText}`, headers);
                }
            } catch (error) {
                showResult('http-response-result', 'error', 'HTTP Request Failed', error.message);
            }
        }

        async function testFileContent() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const response = await fetch(pdfUrl);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const arrayBuffer = await response.arrayBuffer();
                const uint8Array = new Uint8Array(arrayBuffer);
                
                // Get first 100 bytes for analysis
                const firstBytes = Array.from(uint8Array.slice(0, 100))
                    .map(b => b.toString(16).padStart(2, '0'))
                    .join(' ');
                
                // Convert first 50 bytes to ASCII for readability
                const asciiHeader = Array.from(uint8Array.slice(0, 50))
                    .map(b => b >= 32 && b <= 126 ? String.fromCharCode(b) : '.')
                    .join('');
                
                const fileInfo = {
                    totalSize: arrayBuffer.byteLength,
                    firstBytesHex: firstBytes,
                    asciiHeader: asciiHeader,
                    isPdfHeader: asciiHeader.startsWith('%PDF'),
                    lastBytes: Array.from(uint8Array.slice(-20))
                        .map(b => b.toString(16).padStart(2, '0'))
                        .join(' ')
                };
                
                if (arrayBuffer.byteLength === 0) {
                    showResult('file-content-result', 'error', 'File is Empty', 
                        'The downloaded file has zero bytes', fileInfo);
                } else if (!asciiHeader.startsWith('%PDF')) {
                    showResult('file-content-result', 'error', 'Invalid PDF Header', 
                        'File does not start with PDF header (%PDF)', fileInfo);
                } else {
                    showResult('file-content-result', 'success', 'File Content Valid', 
                        `File size: ${arrayBuffer.byteLength} bytes, Valid PDF header detected`, fileInfo);
                }
                
            } catch (error) {
                showResult('file-content-result', 'error', 'File Content Analysis Failed', error.message);
            }
        }

        async function testPdfHeader() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const response = await fetch(pdfUrl, {
                    headers: { 'Range': 'bytes=0-1023' }
                });
                
                const arrayBuffer = await response.arrayBuffer();
                const uint8Array = new Uint8Array(arrayBuffer);
                const header = new TextDecoder().decode(uint8Array);
                
                const pdfVersionMatch = header.match(/%PDF-(\d\.\d)/);
                const hasXref = header.includes('xref');
                const hasTrailer = header.includes('trailer');
                
                const headerInfo = {
                    rawHeader: header.substring(0, 100),
                    pdfVersion: pdfVersionMatch ? pdfVersionMatch[1] : 'Not found',
                    hasXref: hasXref,
                    hasTrailer: hasTrailer,
                    headerLength: arrayBuffer.byteLength
                };
                
                if (pdfVersionMatch) {
                    showResult('pdf-header-result', 'success', 'Valid PDF Header', 
                        `PDF Version: ${pdfVersionMatch[1]}`, headerInfo);
                } else {
                    showResult('pdf-header-result', 'error', 'Invalid PDF Header', 
                        'No valid PDF version found in header', headerInfo);
                }
                
            } catch (error) {
                showResult('pdf-header-result', 'error', 'PDF Header Check Failed', error.message);
            }
        }

        async function testCorsHeaders() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const response = await fetch(pdfUrl, { method: 'HEAD' });
                
                const corsHeaders = {
                    'access-control-allow-origin': response.headers.get('access-control-allow-origin'),
                    'access-control-allow-methods': response.headers.get('access-control-allow-methods'),
                    'access-control-allow-headers': response.headers.get('access-control-allow-headers'),
                    'content-security-policy': response.headers.get('content-security-policy'),
                    'x-frame-options': response.headers.get('x-frame-options'),
                    'referrer-policy': response.headers.get('referrer-policy')
                };
                
                const hasCorsissues = Object.values(corsHeaders).some(value => 
                    value && (value.includes('deny') || value.includes('sameorigin')));
                
                if (hasCorsissues) {
                    showResult('cors-headers-result', 'warning', 'Potential CORS Issues', 
                        'Some security headers might block PDF loading', corsHeaders);
                } else {
                    showResult('cors-headers-result', 'success', 'CORS Headers OK', 
                        'No obvious CORS blocking headers detected', corsHeaders);
                }
                
            } catch (error) {
                showResult('cors-headers-result', 'error', 'CORS Headers Check Failed', error.message);
            }
        }

        async function testPdfJsDetailed() {
            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('PDF.js library not loaded');
                }

                const pdfUrl = document.getElementById('pdf-url').value;
                
                // Test with different configurations
                const configs = [
                    {
                        name: 'Standard Config',
                        config: { url: pdfUrl }
                    },
                    {
                        name: 'No Stream Config',
                        config: { 
                            url: pdfUrl,
                            disableStream: true,
                            disableRange: true
                        }
                    },
                    {
                        name: 'Verbose Config',
                        config: { 
                            url: pdfUrl,
                            verbosity: pdfjsLib.VerbosityLevel.INFOS
                        }
                    }
                ];
                
                let results = [];
                
                for (let testConfig of configs) {
                    try {
                        console.log(`Testing PDF.js with ${testConfig.name}...`);
                        const loadingTask = pdfjsLib.getDocument(testConfig.config);
                        const pdf = await loadingTask.promise;
                        
                        results.push({
                            config: testConfig.name,
                            success: true,
                            numPages: pdf.numPages,
                            fingerprint: pdf.fingerprint
                        });
                        
                        // If one succeeds, we can stop
                        break;
                        
                    } catch (configError) {
                        results.push({
                            config: testConfig.name,
                            success: false,
                            error: configError.message
                        });
                    }
                }
                
                const successfulConfig = results.find(r => r.success);
                
                if (successfulConfig) {
                    showResult('pdfjs-detailed-result', 'success', 'PDF.js Loading Successful', 
                        `Successfully loaded with ${successfulConfig.config}`, results);
                } else {
                    showResult('pdfjs-detailed-result', 'error', 'PDF.js Loading Failed', 
                        'All PDF.js configurations failed', results);
                }
                
            } catch (error) {
                showResult('pdfjs-detailed-result', 'error', 'PDF.js Test Failed', error.message);
            }
        }

        async function testAlternativeMethods() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const results = [];
                
                // Test 1: Fetch as blob
                try {
                    const response = await fetch(pdfUrl);
                    const blob = await response.blob();
                    results.push({
                        method: 'Fetch as Blob',
                        success: true,
                        size: blob.size,
                        type: blob.type
                    });
                } catch (error) {
                    results.push({
                        method: 'Fetch as Blob',
                        success: false,
                        error: error.message
                    });
                }
                
                // Test 2: XMLHttpRequest
                try {
                    await new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open('GET', pdfUrl, true);
                        xhr.responseType = 'arraybuffer';
                        xhr.onload = () => {
                            if (xhr.status === 200) {
                                results.push({
                                    method: 'XMLHttpRequest',
                                    success: true,
                                    size: xhr.response.byteLength,
                                    status: xhr.status
                                });
                                resolve();
                            } else {
                                results.push({
                                    method: 'XMLHttpRequest',
                                    success: false,
                                    error: `HTTP ${xhr.status}`
                                });
                                reject();
                            }
                        };
                        xhr.onerror = () => {
                            results.push({
                                method: 'XMLHttpRequest',
                                success: false,
                                error: 'Network error'
                            });
                            reject();
                        };
                        xhr.send();
                    });
                } catch (error) {
                    // Error already handled in xhr.onerror
                }
                
                const successCount = results.filter(r => r.success).length;
                
                if (successCount > 0) {
                    showResult('alternative-methods-result', 'success', 'Alternative Methods Work', 
                        `${successCount}/${results.length} methods successful`, results);
                } else {
                    showResult('alternative-methods-result', 'error', 'All Alternative Methods Failed', 
                        'No method could load the file', results);
                }
                
            } catch (error) {
                showResult('alternative-methods-result', 'error', 'Alternative Methods Test Failed', error.message);
            }
        }

        async function testServerSideFile() {
            try {
                // This would need a server-side endpoint to check the actual file
                const response = await fetch('/pagetyping/check-pdf-file', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        file_path: 'scanned_documents/5/1754031317_0_688c64d592ba4.pdf'
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    showResult('server-side-result', data.success ? 'success' : 'error', 
                        'Server-Side File Check', data.message, data.details);
                } else {
                    showResult('server-side-result', 'warning', 'Server-Side Check Unavailable', 
                        'Server-side file check endpoint not available');
                }
                
            } catch (error) {
                showResult('server-side-result', 'warning', 'Server-Side Check Failed', 
                    'Could not perform server-side file check: ' + error.message);
            }
        }

        // Auto-run basic tests on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('PDF diagnostic tool loaded');
        });
    </script>
</body>
</html>