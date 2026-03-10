<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * FileSecurity handles secure storage and retrieval of user uploads
 */
class FileSecurity extends Component
{
    /**
     * Securely saves an uploaded file with a hashed name outside the web root
     * @param UploadedFile $file
     * @param string $subDir
     * @return string|bool The hashed filename relative to storage root
     */
    public static function secureSave(UploadedFile $file, $subDir = 'photos')
    {
        $storagePath = Yii::getAlias('@common/storage/' . $subDir);

        if (!is_dir($storagePath)) {
            FileHelper::createDirectory($storagePath, 0775, true);
        }

        // Generate a random hash for the filename
        $extension = $file->extension;
        $hashName = bin2hex(random_bytes(16)) . '.' . $extension;
        $fullPath = $storagePath . '/' . $hashName;

        if ($file->saveAs($fullPath)) {
            return $subDir . '/' . $hashName;
        }

        return false;
    }

    /**
     * Verifies if a file's actual MIME type matches allowed types
     * @param string $filePath
     * @param array $allowedMimes
     * @return bool
     */
    public static function validateMimeType($filePath, $allowedMimes = ['image/jpeg', 'image/png'])
    {
        $mime = FileHelper::getMimeType($filePath);
        return in_array($mime, $allowedMimes);
    }
}
