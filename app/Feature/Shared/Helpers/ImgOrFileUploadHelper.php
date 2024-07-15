<?php

namespace App\Feature\Shared\Helpers;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
//use Imagick;

class ImgOrFileUploadHelper
{
    /**
     * Save the image or file to the specified storage directory.
     *
     * @param string $storageDir The directory where the file is stored.
     * @param string $fileName The original file name.
     * @param string $newFileName The new file name with extension.
     * @return string The path of the saved file.
     * @throws \Exception
     */
    public static function saveImgOrFile(string $storageDir, string $fileName, string $newFileName): string
    {
        Log::debug('ImgOrFileUploadHelper->saveImgOrFile');

        // Determine the file extension
        $extension = strtolower(pathinfo($newFileName, PATHINFO_EXTENSION));

        // Create the full path for the original file
        $filePath = storage_path('app/' . $storageDir . '/' . $fileName);

        // Handle non-optimizable file types separately
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
            case 'webp':
            case 'bmp':
                return self::optimizeAndConvertImage($storageDir, $fileName, $newFileName);
            case 'heic':
                //return self::convertHeicToJpeg($storageDir, $fileName, $newFileName);
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
            case 'ppt':
            case 'pptx':
            case 'txt':
            case 'rtf':
                // No optimization, just save as is
                $newFilePath = storage_path('app/' . $storageDir . '/' . $newFileName);
                if (rename($filePath, $newFilePath)) {
                    return Storage::url($storageDir . '/' . $newFileName);
                } else {
                    throw new \Exception('Failed to move file: ' . $filePath);
                }
            default:
                // we can have below line uncommented and other 4 lines after that can be commented out
                // throw new \Exception('Unsupported file type: ' . $extension);
                // No optimization, just save as is
                $newFilePath = storage_path('app/' . $storageDir . '/' . $newFileName);
                Storage::move($filePath, $newFilePath);
                return Storage::url($storageDir . '/' . $newFileName);
        }

    }

    /**
     * Optimize and convert the image to the most suitable format.
     *
     * @param string $storageDir The directory where the file is stored.
     * @param string $fileName The original file name.
     * @param string $newFileName The new file name with extension.
     * @return string The path of the optimized and converted image.
     * @throws \Exception
     */
    protected static function optimizeAndConvertImage(string $storageDir, string $fileName, string $newFileName): string
    {
        Log::debug('ImgOrFileUploadHelper->optimizeAndConvertImage');

        $newExtension = "jpeg";

        // Override the new file extension to 'jpeg'
        $newFileName = pathinfo($newFileName, PATHINFO_FILENAME) . '.' . $newExtension;

        // Create the full path for the original file
        $filePath = storage_path('app/' . $storageDir . '/' . $fileName);

        // Create the OptimizerChain instance
        $optimizerChain = OptimizerChainFactory::create();

        // Optimize the image
        $optimizerChain->optimize($filePath);

        // Load the optimized image using Intervention Image
        $image = Image::make($filePath);

        // Generate the new file path
        $newFilePath = storage_path('app/' . $storageDir . '/' . $newFileName);

        // Save the optimized image with the new file name and extension
        $image->encode($newExtension, 85)->save($newFilePath);

        // Delete the original uploaded file
        $relativePath = str_replace(storage_path('app/'), '', $filePath);
        if (Storage::exists($relativePath)) {
            Storage::delete($relativePath);
        }

        return Storage::url($storageDir . '/' . $newFileName);
    }

    // Commenting out below method as it needs dependency software
    // to be installed in the Operating System and PHP extension.

    // /**
    //  * Convert HEIC image to JPEG using Imagick.
    //  * For Imagick to work, you need to have the ImageMagick software
    //  * and the Imagick PHP extension installed on your operating system
    //  *
    //  * @param string $storageDir The directory where the file is stored.
    //  * @param string $fileName The original file name.
    //  * @param string $newFileName The new file name with extension.
    //  * @return string The path of the converted image.
    //  * @throws \Exception
    //  */
    // protected static function convertHeicToJpeg(string $storageDir, string $fileName, string $newFileName): string
    // {
    //     Log::debug('ImgOrFileUploadHelper->convertHeicToJpeg');

    //     // Create the full path for the original file
    //     $filePath = storage_path('app/' . $storageDir . '/' . $fileName);

    //     // Load the HEIC image using Imagick
    //     $imagick = new Imagick($filePath);

    //     // Set the output format to JPEG
    //     $imagick->setImageFormat('jpeg');

    //     // Generate the new file path
    //     $newFilePath = storage_path('app/' . $storageDir . '/' . $newFileName);

    //     // Save the converted image as JPEG
    //     $imagick->writeImage($newFilePath);

    //     // Clear the Imagick object
    //     $imagick->clear();

    //     // Delete the original uploaded file
    //     $relativePath = str_replace(storage_path('app/'), '', $filePath);
    //     if (Storage::exists($relativePath)) {
    //         Storage::delete($relativePath);
    //     }

    //     return Storage::url($storageDir . '/' . $newFileName);
    // }
}
