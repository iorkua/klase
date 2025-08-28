<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\FileIndexing;
use App\Models\Scanning;
use App\Models\PageTyping;
use Exception;

class PageTypingService
{
    /**
     * Get file thumbnails for page typing interface
     */
    public function getFileThumbnails($fileIndexingId)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['scannings', 'pagetypings'])
                ->find($fileIndexingId);

            if (!$fileIndexing) {
                throw new Exception('File indexing not found');
            }

            $thumbnails = [];
            
            foreach ($fileIndexing->scannings as $scanning) {
                $fileExtension = strtolower(pathinfo($scanning->document_path, PATHINFO_EXTENSION));
                
                if ($fileExtension === 'pdf') {
                    // Handle PDF files - generate thumbnails for each page
                    $pdfThumbnails = $this->generatePdfThumbnails($scanning);
                    $thumbnails = array_merge($thumbnails, $pdfThumbnails);
                } else {
                    // Handle image files
                    $thumbnails[] = [
                        'scanning_id' => $scanning->id,
                        'page_number' => 1,
                        'file_path' => $scanning->document_path,
                        'thumbnail_url' => $this->generateImageThumbnail($scanning->document_path),
                        'preview_url' => Storage::url($scanning->document_path),
                        'original_filename' => $scanning->original_filename,
                        'document_type' => $scanning->document_type,
                        'is_typed' => $this->isPageTyped($fileIndexingId, $scanning->id, 1)
                    ];
                }
            }

            return $thumbnails;
        } catch (Exception $e) {
            Log::error('Error getting file thumbnails', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Generate thumbnails for PDF pages
     */
    private function generatePdfThumbnails($scanning)
    {
        $thumbnails = [];
        $pdfPath = storage_path('app/public/' . ltrim($scanning->document_path, '/'));
        
        if (!file_exists($pdfPath)) {
            Log::warning('PDF file not found', ['path' => $pdfPath]);
            return $thumbnails;
        }

        try {
            $pageCount = $this->getPdfPageCount($pdfPath);
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $thumbnails[] = [
                    'scanning_id' => $scanning->id,
                    'page_number' => $i,
                    'file_path' => $scanning->document_path . '#page=' . $i,
                    'thumbnail_url' => $this->generatePdfPageThumbnail($scanning->document_path, $i),
                    'preview_url' => Storage::url($scanning->document_path) . '#page=' . $i,
                    'original_filename' => $scanning->original_filename,
                    'document_type' => $scanning->document_type,
                    'is_typed' => $this->isPageTyped($scanning->file_indexing_id, $scanning->id, $i)
                ];
            }
        } catch (Exception $e) {
            Log::error('Error generating PDF thumbnails', [
                'scanning_id' => $scanning->id,
                'error' => $e->getMessage()
            ]);
        }

        return $thumbnails;
    }

    /**
     * Generate thumbnail for PDF page
     */
    private function generatePdfPageThumbnail($pdfPath, $pageNumber)
    {
        try {
            $thumbnailDir = 'thumbnails/pdf';
            $thumbnailName = pathinfo($pdfPath, PATHINFO_FILENAME) . '_page_' . $pageNumber . '.jpg';
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
            $fullThumbnailPath = storage_path('app/public/' . $thumbnailPath);

            // Create thumbnail directory if it doesn't exist
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir);
            }

            // Check if thumbnail already exists
            if (Storage::disk('public')->exists($thumbnailPath)) {
                return Storage::url($thumbnailPath);
            }

            // Generate thumbnail using Imagick if available
            if (extension_loaded('imagick')) {
                $fullPdfPath = storage_path('app/public/' . ltrim($pdfPath, '/'));
                
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($fullPdfPath . '[' . ($pageNumber - 1) . ']');
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality(80);
                $imagick->thumbnailImage(200, 300, true);
                
                $imagick->writeImage($fullThumbnailPath);
                $imagick->clear();
                
                return Storage::url($thumbnailPath);
            }

            // Fallback: return a placeholder or the PDF icon
            return $this->getPlaceholderThumbnail('pdf');
        } catch (Exception $e) {
            Log::error('Error generating PDF page thumbnail', [
                'pdf_path' => $pdfPath,
                'page_number' => $pageNumber,
                'error' => $e->getMessage()
            ]);
            return $this->getPlaceholderThumbnail('pdf');
        }
    }

    /**
     * Generate thumbnail for image file
     */
    private function generateImageThumbnail($imagePath)
    {
        try {
            $thumbnailDir = 'thumbnails/images';
            $thumbnailName = pathinfo($imagePath, PATHINFO_FILENAME) . '_thumb.jpg';
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;

            // Create thumbnail directory if it doesn't exist
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir);
            }

            // Check if thumbnail already exists
            if (Storage::disk('public')->exists($thumbnailPath)) {
                return Storage::url($thumbnailPath);
            }

            // Generate thumbnail
            $fullImagePath = storage_path('app/public/' . ltrim($imagePath, '/'));
            $fullThumbnailPath = storage_path('app/public/' . $thumbnailPath);

            if (extension_loaded('imagick')) {
                $imagick = new \Imagick($fullImagePath);
                $imagick->thumbnailImage(200, 300, true);
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality(80);
                $imagick->writeImage($fullThumbnailPath);
                $imagick->clear();
                
                return Storage::url($thumbnailPath);
            }

            // Fallback using GD library
            $imageInfo = getimagesize($fullImagePath);
            if ($imageInfo) {
                $this->generateThumbnailWithGD($fullImagePath, $fullThumbnailPath, $imageInfo);
                return Storage::url($thumbnailPath);
            }

            return Storage::url($imagePath); // Return original if thumbnail generation fails
        } catch (Exception $e) {
            Log::error('Error generating image thumbnail', [
                'image_path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return Storage::url($imagePath);
        }
    }

    /**
     * Generate thumbnail using GD library
     */
    private function generateThumbnailWithGD($sourcePath, $thumbnailPath, $imageInfo)
    {
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        // Calculate thumbnail dimensions
        $thumbWidth = 200;
        $thumbHeight = 300;
        
        $ratio = min($thumbWidth / $width, $thumbHeight / $height);
        $newWidth = $width * $ratio;
        $newHeight = $height * $ratio;

        // Create source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }

        // Create thumbnail
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save thumbnail
        imagejpeg($thumbnail, $thumbnailPath, 80);

        // Clean up
        imagedestroy($source);
        imagedestroy($thumbnail);
    }

    /**
     * Get placeholder thumbnail for unsupported file types
     */
    private function getPlaceholderThumbnail($type)
    {
        // Return a data URL for a simple placeholder
        switch ($type) {
            case 'pdf':
                return 'data:image/svg+xml;base64,' . base64_encode('
                    <svg xmlns="http://www.w3.org/2000/svg" width="200" height="300" viewBox="0 0 200 300">
                        <rect width="200" height="300" fill="#f3f4f6"/>
                        <text x="100" y="150" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">PDF</text>
                        <text x="100" y="170" text-anchor="middle" font-family="Arial" font-size="12" fill="#9ca3af">Page Preview</text>
                    </svg>
                ');
            default:
                return 'data:image/svg+xml;base64,' . base64_encode('
                    <svg xmlns="http://www.w3.org/2000/svg" width="200" height="300" viewBox="0 0 200 300">
                        <rect width="200" height="300" fill="#f3f4f6"/>
                        <text x="100" y="150" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">File</text>
                        <text x="100" y="170" text-anchor="middle" font-family="Arial" font-size="12" fill="#9ca3af">Preview</text>
                    </svg>
                ');
        }
    }

    /**
     * Get PDF page count
     */
    private function getPdfPageCount($pdfPath)
    {
        try {
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->readImage($pdfPath);
                $pageCount = $imagick->getNumberImages();
                $imagick->clear();
                return $pageCount;
            }

            // Fallback: parse PDF manually
            $content = file_get_contents($pdfPath);
            if ($content) {
                preg_match_all('/\/Count\s+(\d+)/', $content, $matches);
                if (!empty($matches[1])) {
                    return max($matches[1]);
                }
                
                preg_match_all('/\/Type\s*\/Page[^s]/', $content, $matches);
                if (!empty($matches[0])) {
                    return count($matches[0]);
                }
            }

            return 3; // Default assumption
        } catch (Exception $e) {
            Log::error('Error getting PDF page count', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Check if a page is already typed
     */
    private function isPageTyped($fileIndexingId, $scanningId, $pageNumber)
    {
        return PageTyping::on('sqlsrv')
            ->where('file_indexing_id', $fileIndexingId)
            ->where('scanning_id', $scanningId)
            ->where('page_no', $pageNumber)
            ->exists();
    }

    /**
     * Get files for PageType More (files with is_updated = 1)
     */
    public function getPageTypeMoreFiles($search = '', $limit = 20)
    {
        try {
            $query = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereHas('pagetypings') // Must have existing page typings
                ->where('is_updated', 1); // And be marked as updated

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('district', 'like', "%{$search}%")
                      ->orWhere('lga', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('updated_at', 'desc')
                         ->limit($limit)
                         ->get();
        } catch (Exception $e) {
            Log::error('Error getting PageType More files', [
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Create combined file view for PageType More
     */
    public function createCombinedFileView($fileIndexingId)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['scannings', 'pagetypings'])
                ->find($fileIndexingId);

            if (!$fileIndexing) {
                throw new Exception('File not found');
            }

            $combinedPath = "PAGETYPING/{$fileIndexing->file_number}/combined.pdf";
            
            // This would typically involve merging existing typed pages with new scans
            // For now, we'll return the path where the combined file should be stored
            
            return [
                'combined_path' => $combinedPath,
                'existing_pages' => $fileIndexing->pagetypings->count(),
                'new_scans' => $fileIndexing->scannings->count() - $fileIndexing->pagetypings->count(),
                'total_pages' => $fileIndexing->scannings->count()
            ];
        } catch (Exception $e) {
            Log::error('Error creating combined file view', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Store files in EDMS folder structure
     */
    public function storeInEdmsStructure($fileIndexingId, $files)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')->find($fileIndexingId);
            if (!$fileIndexing) {
                throw new Exception('File indexing not found');
            }

            $edmsPath = "PAGETYPING/{$fileIndexing->file_number}";
            
            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists($edmsPath)) {
                Storage::disk('public')->makeDirectory($edmsPath);
            }

            $storedFiles = [];
            $pageNumber = 1;

            foreach ($files as $file) {
                $filename = sprintf('page_%03d.%s', $pageNumber, $file->getClientOriginalExtension());
                $filePath = $edmsPath . '/' . $filename;
                
                // Store the file
                $path = $file->storeAs($edmsPath, $filename, 'public');
                
                $storedFiles[] = [
                    'page_number' => $pageNumber,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
                
                $pageNumber++;
            }

            return $storedFiles;
        } catch (Exception $e) {
            Log::error('Error storing files in EDMS structure', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}