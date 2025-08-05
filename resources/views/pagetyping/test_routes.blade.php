<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Typing Routes Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        button { padding: 10px 15px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Page Typing Routes Test</h1>
    
    <div class="test-section info">
        <h3>Route Testing</h3>
        <p>This page tests the routes used by the page typing interface.</p>
        <p><strong>File Indexing ID for testing:</strong> <input type="number" id="test-file-id" value="1" min="1"></p>
    </div>

    <div class="test-section">
        <h3>1. Test scanning.list Route</h3>
        <button onclick="testScanningList()">Test Scanning List</button>
        <div id="scanning-result"></div>
    </div>

    <div class="test-section">
        <h3>2. Test pagetyping.getPageTypings Route</h3>
        <button onclick="testPageTypings()">Test Page Typings</button>
        <div id="pagetyping-result"></div>
    </div>

    <div class="test-section">
        <h3>3. Test pagetyping.saveSingle Route</h3>
        <button onclick="testSaveSingle()">Test Save Single Page</button>
        <div id="save-result"></div>
    </div>

    <div class="test-section">
        <h3>4. Test PDF.js Library</h3>
        <button onclick="testPdfJs()">Test PDF.js</button>
        <div id="pdfjs-result"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configure PDF.js worker
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        function getFileId() {
            return document.getElementById('test-file-id').value;
        }

        function showResult(elementId, success, message, data = null) {
            const element = document.getElementById(elementId);
            element.className = success ? 'success' : 'error';
            element.innerHTML = `
                <h4>${success ? 'Success' : 'Error'}</h4>
                <p>${message}</p>
                ${data ? `<pre>${JSON.stringify(data, null, 2)}</pre>` : ''}
            `;
        }

        async function testScanningList() {
            try {
                const fileId = getFileId();
                const response = await fetch(`{{ route("scanning.list") }}?file_indexing_id=${fileId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                showResult('scanning-result', true, 'Scanning list route is working!', data);
            } catch (error) {
                showResult('scanning-result', false, `Error: ${error.message}`);
            }
        }

        async function testPageTypings() {
            try {
                const fileId = getFileId();
                const response = await fetch(`{{ route("pagetyping.list") }}?file_indexing_id=${fileId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                showResult('pagetyping-result', true, 'Page typings route is working!', data);
            } catch (error) {
                showResult('pagetyping-result', false, `Error: ${error.message}`);
            }
        }

        async function testSaveSingle() {
            try {
                const fileId = getFileId();
                const testData = {
                    file_indexing_id: fileId,
                    scanning_id: 1,
                    page_number: 1,
                    page_type: 'Test Document',
                    page_subtype: 'Test Page',
                    serial_number: 1,
                    page_code: 'TEST',
                    file_path: '/test/path.pdf'
                };

                const response = await fetch('{{ route("pagetyping.save-single") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(testData)
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`HTTP ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                const data = await response.json();
                showResult('save-result', true, 'Save single page route is working!', data);
            } catch (error) {
                showResult('save-result', false, `Error: ${error.message}`);
            }
        }

        function testPdfJs() {
            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('PDF.js library not loaded');
                }

                if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
                    throw new Error('PDF.js worker not configured');
                }

                showResult('pdfjs-result', true, 'PDF.js library is loaded and configured!', {
                    version: pdfjsLib.version,
                    workerSrc: pdfjsLib.GlobalWorkerOptions.workerSrc
                });
            } catch (error) {
                showResult('pdfjs-result', false, `Error: ${error.message}`);
            }
        }

        // Auto-run tests on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, ready for testing');
        });
    </script>
</body>
</html>