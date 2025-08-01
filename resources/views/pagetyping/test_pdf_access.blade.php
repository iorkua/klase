<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Access Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        button { padding: 10px 15px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .url-test { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>PDF Access and Loading Test</h1>
    
    <div class="test-section info">
        <h3>PDF File Testing</h3>
        <p>This page tests PDF file accessibility and PDF.js loading capabilities.</p>
        <p><strong>Test PDF URL:</strong> <input type="text" id="pdf-url" value="/storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf" style="width: 500px;"></p>
    </div>

    <div class="test-section">
        <h3>1. Test Direct PDF Access</h3>
        <button onclick="testDirectAccess()">Test Direct Access</button>
        <div id="direct-access-result"></div>
    </div>

    <div class="test-section">
        <h3>2. Test PDF File Size</h3>
        <button onclick="testFileSize()">Check File Size</button>
        <div id="file-size-result"></div>
    </div>

    <div class="test-section">
        <h3>3. Test PDF.js Loading</h3>
        <button onclick="testPdfJsLoading()">Test PDF.js</button>
        <div id="pdfjs-loading-result"></div>
    </div>

    <div class="test-section">
        <h3>4. Test PDF Content</h3>
        <button onclick="testPdfContent()">Test PDF Content</button>
        <div id="pdf-content-result"></div>
    </div>

    <div class="test-section">
        <h3>5. Direct PDF Link</h3>
        <div class="url-test">
            <a href="/storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf" target="_blank">
                Open PDF in New Tab
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configure PDF.js worker
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        function showResult(elementId, success, message, data = null) {
            const element = document.getElementById(elementId);
            element.className = success ? 'success' : 'error';
            element.innerHTML = `
                <h4>${success ? '✅ Success' : '❌ Error'}</h4>
                <p>${message}</p>
                ${data ? `<pre>${JSON.stringify(data, null, 2)}</pre>` : ''}
            `;
        }

        async function testDirectAccess() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const response = await fetch(pdfUrl, { method: 'HEAD' });
                
                if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    const contentLength = response.headers.get('content-length');
                    
                    showResult('direct-access-result', true, 'PDF file is accessible', {
                        status: response.status,
                        contentType: contentType,
                        contentLength: contentLength + ' bytes',
                        url: pdfUrl
                    });
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            } catch (error) {
                showResult('direct-access-result', false, `Error accessing PDF: ${error.message}`);
            }
        }

        async function testFileSize() {
            try {
                const pdfUrl = document.getElementById('pdf-url').value;
                const response = await fetch(pdfUrl, { method: 'HEAD' });
                
                if (response.ok) {
                    const contentLength = response.headers.get('content-length');
                    const sizeInBytes = parseInt(contentLength);
                    const sizeInKB = (sizeInBytes / 1024).toFixed(2);
                    const sizeInMB = (sizeInBytes / (1024 * 1024)).toFixed(2);
                    
                    if (sizeInBytes === 0) {
                        showResult('file-size-result', false, 'PDF file is empty (0 bytes)');
                    } else {
                        showResult('file-size-result', true, 'PDF file has valid size', {
                            bytes: sizeInBytes,
                            kilobytes: sizeInKB + ' KB',
                            megabytes: sizeInMB + ' MB'
                        });
                    }
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            } catch (error) {
                showResult('file-size-result', false, `Error checking file size: ${error.message}`);
            }
        }

        async function testPdfJsLoading() {
            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('PDF.js library not loaded');
                }

                const pdfUrl = document.getElementById('pdf-url').value;
                
                showResult('pdfjs-loading-result', true, 'PDF.js library is available', {
                    version: pdfjsLib.version,
                    workerSrc: pdfjsLib.GlobalWorkerOptions.workerSrc,
                    testUrl: pdfUrl
                });
            } catch (error) {
                showResult('pdfjs-loading-result', false, `PDF.js error: ${error.message}`);
            }
        }

        async function testPdfContent() {
            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('PDF.js library not loaded');
                }

                const pdfUrl = document.getElementById('pdf-url').value;
                
                // Show loading
                document.getElementById('pdf-content-result').innerHTML = '<p>Loading PDF...</p>';
                
                const loadingTask = pdfjsLib.getDocument({
                    url: pdfUrl,
                    verbosity: pdfjsLib.VerbosityLevel.WARNINGS
                });
                
                const pdf = await loadingTask.promise;
                
                // Get first page to test content
                const page = await pdf.getPage(1);
                const viewport = page.getViewport({ scale: 1.0 });
                
                showResult('pdf-content-result', true, 'PDF content loaded successfully', {
                    numPages: pdf.numPages,
                    firstPageSize: {
                        width: viewport.width,
                        height: viewport.height
                    },
                    fingerprint: pdf.fingerprint
                });
                
            } catch (error) {
                console.error('PDF content test error:', error);
                showResult('pdf-content-result', false, `PDF content error: ${error.message}`);
            }
        }

        // Auto-run basic tests on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('PDF test page loaded');
            setTimeout(() => {
                testDirectAccess();
                testFileSize();
                testPdfJsLoading();
            }, 1000);
        });
    </script>
</body>
</html>