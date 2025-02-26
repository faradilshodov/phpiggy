<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class ReceiptService
{
    public function __construct(private Database $db) {}

    public function validateFile(?array $file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException([
                'receipt' => ['Receipt file is required'],
            ]);
        }

        $maxFileSizeMB = 3 * 1024 * 1024; // 3 MB

        if($file['size'] > $maxFileSizeMB) {
            throw new ValidationException([
                'receipt' => ['Receipt file size exceeds the maximum limit of 3 MB'],
            ]);
        }

        $originalFileName = $file['name'];

        if(!preg_match('/^[A-za-z0-9\s._-]+$/', $originalFileName)) {
            throw new ValidationException([
                'receipt' => ['Invalid file name'],
            ]);
        }

        $clientMimeType =  $file['type'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if (!in_array($clientMimeType, $allowedMimeTypes)) {
            throw new ValidationException([
                'receipt' => ['Invalid file type. Only JPG, PNG, and PDF files are allowed'],
            ]);
        }
    }
}
