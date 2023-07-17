<?php

namespace App\Service;

use Intervention\Image\ImageManager;

class DefaultImageGenerator
{
    private $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function generateImage(string $firstName, string $lastName,string $id, int $width = 200, int $height = 200): string
    {
        $image = imagecreatetruecolor($width, $height);

        // Set the background color
        $backgroundColor = imagecolorallocate($image, 237, 28, 36);
        imagefill($image, 0, 0, $backgroundColor);
    
        $text = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    
        // Set the font path
        $fontPath = $this->getFontPath();
    
        // Set the font color (white)
        $fontColor = imagecolorallocate($image, 255, 94, 91);
    
        // Set the font size (manually increase the size for better visibility)
        $fontSize = 80;
    
        // Calculate the bounding box of the text
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $textHeight = abs($bbox[5] - $bbox[1]);
    
        // Center the text horizontally and vertically
        $textX = ($width - $textWidth) / 2;
        $textY = ($height - $textHeight) / 2 + $textHeight; // Adjust the Y position for better centering
    
        // Add the text to the image
        imagettftext($image, $fontSize, 0, $textX, $textY, $fontColor, $fontPath, $text);
    
        $imagePath = 'Photos/users/'.$firstName.'-'.$lastName.$id.'.png'; // Replace with the path where you want to save the image
    
        // Save the image to the specified path
        imagepng($image, $imagePath);
        imagedestroy($image);
    
        return $imagePath;
    }

    private function getFontPath(): string
    {
        // Replace 'path/to/your/font.ttf' with the path to your TrueType font file
        return __DIR__ . '\..\..\public\plugins\fontawesome-free\webfonts\Roboto-Bold.ttf';
    }
    

    private function getTextWidth(int $fontSize, string $fontPath, string $text): float
    {
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        return abs($bbox[4] - $bbox[0]);
    }

    private function getTextHeight(int $fontSize, string $fontPath, string $text): float
    {
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        return abs($bbox[5] - $bbox[1]);
    }
}
