<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imagePath, $label, $degree)
{
    $image = imagecreatefrompng($imagePath);
    $rotatedImage = imagerotate($image, $degree, imagecolorallocate($image,255,255,255));
    $newImagePath = __DIR__ . '/samples/train/' . ((string)intval(microtime(true) * 1000)) .
        '_' . $label . '.png';
    imagepng(
        $rotatedImage,
        $newImagePath
    );
    imagedestroy($rotatedImage);
    imagedestroy($image);
}

$dir = opendir(__DIR__ . '/samples/origin_train');
while ($file = readdir($dir)) {
    if (!in_array($file, ['.', '..'])) {
        $fileNameParts = explode('.', $file);
        $imageNameParts = explode('_', $fileNameParts[0]);
        $label = $imageNameParts[1];
        for ($i = -45; $i <= 45; ++$i) {
            if ($i === 0) {
                continue;
            }
            process(__DIR__ . '/samples/origin_train/' . $file, $label, $i);
        }
    }
}
closedir($dir);
