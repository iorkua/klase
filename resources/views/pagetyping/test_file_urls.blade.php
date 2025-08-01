<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File URL Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        button { padding: 10px 15px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .url-test { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 3px; }
        .url-link { color: #007bff; text-decoration: none; }
        .url-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>File URL Generation Test</h1>
    
    <div class="test-section info">
        <h3>Testing File URL Generation</h3>
        <p>This page tests if file URLs are being generated correctly for the page typing interface.</p>
    </div>

    <div class="test-section">
        <h3>1. Test Scanning List API</h3>
        <p><strong>File Indexing ID:</strong> <input type="number" id="test-file-id" value="5" min="1"></p>
        <button onclick="testScanningUrls()">Test File URLs</button>
        <div id="url-test-result"></div>
    </div>

    <div class="test-section">
        <h3>2. Manual URL Tests</h3>
        <div class="url-test">
            <strong>Expected URL Format:</strong><br>
            <code>http://klas.com.ng/storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf</code>
        </div>
        <div class="url-test">
            <strong>Test Direct Access:</strong><br>
            <a href="/storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf" target="_blank" class="url-link">
                /storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf
            </a>
        </div>
        <div class="url-test">
            <strong>Test Asset Helper:</strong><br>
            <a href="{{ asset('storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf') }}" target="_blank" class="url-link">
                {{ asset('storage/scanned_documents/5/1754031317_0_688c64d592ba4.pdf') }}
            </a>
        </div>
    </div>

    <script>
        async function testScanningUrls() {
            const fileId = document.getElementById('test-file-id').value;
            const resultDiv = document.getElementById('url-test-result');
            
            try {
                resultDiv.innerHTML = '<p>Testing...</p>';
                
                const response = await fetch(`{{ route("scanning.list") }}?file_indexing_id=${fileId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.scanned_files && data.scanned_files.length > 0) {
                    let html = '<div class="success"><h4>✅ Success - File URLs Generated</h4>';
                    
                    data.scanned_files.forEach((file, index) => {
                        const isCorrect = file.file_url && !file.file_url.includes('//storage/app/public/') && file.file_url.includes('/storage/');
                        const statusIcon = isCorrect ? '✅' : '���';
                        const statusClass = isCorrect ? 'success' : 'error';
                        
                        html += `
                            <div class="url-test ${statusClass}">
                                <strong>${statusIcon} File ${index + 1}: ${file.filename}</strong><br>
                                <strong>Generated URL:</strong> <a href="${file.file_url}" target="_blank" class="url-link">${file.file_url}</a><br>
                                <strong>Status:</strong> ${isCorrect ? 'Correct format' : 'Incorrect format'}
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    resultDiv.innerHTML = html;
                } else {
                    resultDiv.innerHTML = '<div class="error"><h4>❌ No Files Found</h4><p>No scanned files found for this file indexing ID.</p></div>';
                }
                
            } catch (error) {
                resultDiv.innerHTML = `<div class="error"><h4>❌ Error</h4><p>${error.message}</p></div>`;
            }
        }

        // Auto-test on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('File URL test page loaded');
        });
    </script>
</body>
</html>