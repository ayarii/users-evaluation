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

    public function generateImage(string $firstName, string $lastName, string $id, int $width = 200, int $height = 200): string
    {
        $image = imagecreatetruecolor($width, $height);

        // Set the background color
        /*$backgroundColor = imagecolorallocate($image, 237, 28, 36);
        imagefill($image, 0, 0, $backgroundColor);*/
        $backgroundColor = $this->generateRandomPastelColor();
        list($r, $g, $b) = $this->hex2rgb($backgroundColor);
        $bgColor = imagecolorallocate($image, $r, $g, $b);
        imagefill($image, 0, 0, $bgColor);

        $text = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

        // Set the font path
        $fontPath = $this->getFontPath();

        // Set the font color (white)
        //$fontColor = imagecolorallocate($image, 255, 94, 91);
        $fontColor = $this->generateRandomPastelColor();
        list($r, $g, $b) = $this->hex2rgb($fontColor);
$fontColorInt = imagecolorallocate($image, $r, $g, $b);

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
        imagettftext($image, $fontSize, 0, $textX, $textY, $fontColorInt, $fontPath, $text);

        $imagePath = 'Photos/users/' . $firstName . '-' . $lastName . $id . '.png'; // Replace with the path where you want to save the image

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
    public  function generateRandomPastelColor(): string
    {
        // Générer des valeurs RVB dans la plage des teintes pastel (150-255)
        $red = mt_rand(150, 255);
        $green = mt_rand(150, 255);
        $blue = mt_rand(150, 255);

        // Convertir les valeurs RVB en une chaîne hexadécimale
        $hexColor = sprintf("#%02x%02x%02x", $red, $green, $blue);

        return $hexColor;
    }

    public function hex2rgb($hexColor)
    {
        $hexColor = str_replace("#", "", $hexColor);

        if (strlen($hexColor) == 6) {
            list($r, $g, $b) = sscanf($hexColor, "%02x%02x%02x");
            return array($r, $g, $b);
        } elseif (strlen($hexColor) == 3) {
            list($r, $g, $b) = sscanf($hexColor, "%01x%01x%01x");
            return array($r * 17, $g * 17, $b * 17);
        }

        return array(0, 0, 0);
    }
}
