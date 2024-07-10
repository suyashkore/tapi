<?php

namespace App\Feature\Shared\Helpers;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Optimize and convert the image to the most suitable format.
     *
     * @param string $storageDir The directory where the file is stored.
     * @param string $fileName The original file name.
     * @param string $newFileName The new file name with extension.
     * @return string The path of the optimized and converted image.
     * @throws \Exception
     */
    public static function optimizeAndConvertImage(string $storageDir, string $fileName, string $newFileName): string
    {
        Log::debug('ImageHelper->optimizeAndConvertImage');

        // Determine the new file extension
        $newExtension = pathinfo($newFileName, PATHINFO_EXTENSION) ?: 'jpeg';

        // Create the full path for the original file
        $filePath = storage_path('app/' . $storageDir . '/' . $fileName);

        // If the file is a PDF, just change the filename and save
        if ($newExtension === 'pdf') {
            $newFilePath = storage_path('app/' . $storageDir . '/' . $newFileName);
            Storage::move($filePath, $newFilePath);
            return Storage::url($storageDir . '/' . $newFileName);
        }

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
}
