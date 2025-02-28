<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

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

        if ($file['size'] > $maxFileSizeMB) {
            throw new ValidationException([
                'receipt' => ['Receipt file size exceeds the maximum limit of 3 MB'],
            ]);
        }

        $originalFileName = $file['name'];

        if (!preg_match('/^[A-Za-z0-9\s._-]+$/', $originalFileName)) {
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

    public function upload(array $file, int $transaction)
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = bin2hex(random_bytes(16)) . '.' . $fileExtension;

        $uploadPath = Paths::STORAGE_UPLOADS . '/' . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new ValidationException([
                'receipt' => ['Failed to upload the file'],
            ]);
        }

        $this->db->query(
            "INSERT INTO receipts(
            transaction_id, original_filename, storage_filename, media_type
            )
            VALUES (:transaction_id, :original_filename, :storage_filename, :media_type)",
            [
                'transaction_id' => $transaction,
                'original_filename' => $file['name'],
                'storage_filename' => $newFileName,
                'media_type' => $file['type'],
            ]            
        );
    }
}
