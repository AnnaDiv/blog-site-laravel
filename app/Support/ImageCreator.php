<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageCreator
{
    protected $maxDim = 1500;

    public function imageValidation(UploadedFile $image): ?string
    {
        $allowed = ['jpg','jpeg','png','gif','bmp'];
        $ext = strtolower($image->getClientOriginalExtension());
        
        return in_array($ext, $allowed) ? $ext : null;
    }

    public function imageFilename(UploadedFile $image, int $user_id): string
    {
        $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safe = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        return hash('sha256', $user_id.'_'.$safe.'_'.time().'_'.random_bytes(8)).'.jpg';
    }

    public function imageCleanResize(UploadedFile $image, string $extension)
    {
        $createFrom = [
            'jpg' => 'imagecreatefromjpeg',
            'jpeg' => 'imagecreatefromjpeg',
            'png' => 'imagecreatefrompng',
            'gif' => 'imagecreatefromgif',
            'bmp' => 'imagecreatefrombmp',
        ];

        if (!function_exists($createFrom[$extension])) return false;

        $original = @$createFrom[$extension]($image->getPathname());
        if (!$original) return false;

        // EXIF rotation
        if (in_array($extension, ['jpg','jpeg']) && function_exists('exif_read_data')) {
            $exif = @exif_read_data($image->getPathname());
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3: $original = imagerotate($original, 180, 0); break;
                    case 6: $original = imagerotate($original, -90, 0); break;
                    case 8: $original = imagerotate($original, 90, 0); break;
                }
            }
        }

        $origWidth = imagesx($original);
        $origHeight = imagesy($original);

        if ($origWidth <= $this->maxDim && $origHeight <= $this->maxDim) {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        } else {
            $scale = $this->maxDim / max($origWidth, $origHeight);
            $newWidth = (int)($origWidth * $scale);
            $newHeight = (int)($origHeight * $scale);
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($extension,['png','gif'])) {
            imagecolortransparent($resized, imagecolorallocatealpha($resized,0,0,0,127));
            imagealphablending($resized,false);
            imagesavealpha($resized,true);
        }

        imagecopyresampled($resized, $original, 0,0,0,0, $newWidth,$newHeight, $origWidth,$origHeight);

        return [$resized, $original];
    }

    public function viewImage(UploadedFile $image, int $user_id)
    {
        $extension = $this->imageValidation($image);
        if (!$extension) return false;
        
        $filename = $this->imageFilename($image, $user_id);
        $images = $this->imageCleanResize($image, $extension);
        if (!$images) return false;

        return [
            'new_image' => $images[0],
            'old_image' => $images[1],
            'filename' => $filename,
        ];
    }

    public function createImage(UploadedFile $image, string $user_id): array|false
    {
        $user_id = (int) $user_id;
        $extension = $this->imageValidation($image);
        
        if ($extension === false) return false;

        $fileName = $this->imageFilename($image, $user_id);

        $relativeUserPath = "user/{$user_id}/";

        if (!Storage::disk('public')->exists($relativeUserPath)) {
            Storage::disk('public')->makeDirectory($relativeUserPath);
        }

        $absoluteUserPath = Storage::disk('public')->makeDirectory($relativeUserPath);
        $destImage = $absoluteUserPath . $fileName;
        $imageFolder = $relativeUserPath . $fileName;

        $images = $this->imageCleanResize($image, $extension);
        if ($images === false) {
            return false;
        }

        return [
            'new_image' => $images[0],
            'destination' => $destImage,
            'old_image' => $images[1],
            'image_folder' => $imageFolder,
        ];
    }

    public function createImageProf(UploadedFile $image, string $user_id): array|false
    {
        $user_id = (int) $user_id;
        $extension = $this->imageValidation($image);
        if ($extension === false) return false;

        $fileName = $this->imageFilename($image, $user_id);

        $relativeUserPath = "user/{$user_id}/profile/";

        if (!Storage::disk('public')->exists($relativeUserPath)) {
            Storage::disk('public')->makeDirectory($relativeUserPath);
        }

        $absoluteUserPath = Storage::disk('public')->makeDirectory($relativeUserPath);
        $destImage = $absoluteUserPath . $fileName;
        $imageFolder = $relativeUserPath . $fileName;

        $images = $this->imageCleanResize($image, $extension);

        return [
            'new_image' => $images[0],
            'destination' => $destImage,
            'old_image' => $images[1],
            'image_folder' => $imageFolder,
        ];
    }

}