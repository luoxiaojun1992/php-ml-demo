<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imagePath, $processedImagePath)
{
    $imagick = new Imagick($imagePath);
    $imagick->scaleImage(28, 28);

    $pixels = [];
    for ($i = 0; $i < 28; ++$i) {
        for ($j = 0; $j < 28; ++$j) {
            $pixelColor = $imagick->getImagePixelColor($i, $j);
            $blueValue = $pixelColor->getColorValue(Imagick::COLOR_BLUE);
            $greenValue = $pixelColor->getColorValue(Imagick::COLOR_GREEN);
            $redValue = $pixelColor->getColorValue(Imagick::COLOR_RED);
            $pixels[] = $blueValue + $greenValue + $redValue;
        }
    }

    $imagick->writeImage($processedImagePath);

    $imagick->destroy();

    $samples = [$pixels];

    return $samples[0];
}

$csv = fopen(__DIR__ . '/data/train.csv', 'wb');

$dir = opendir(__DIR__ . '/samples/train');
while ($file = readdir($dir)) {
    if (!in_array($file, ['.', '..'])) {
        $fileNameParts = explode('.', $file);
        $imageNameParts = explode('_', $fileNameParts[0]);
        $label = $imageNameParts[1];

        $samples = process(
            __DIR__ . '/samples/train/' . $file,
            __DIR__ . '/samples/processed/train/' . $file
        );
        $samples[] = $label;

        fputcsv($csv, $samples);
    }
}
closedir($dir);

fclose($csv);

$csv = fopen(__DIR__ . '/data/test.csv', 'wb');

$dir = opendir(__DIR__ . '/samples/test');
while ($file = readdir($dir)) {
    if (!in_array($file, ['.', '..'])) {
        $fileNameParts = explode('.', $file);
        $imageNameParts = explode('_', $fileNameParts[0]);
        $label = $imageNameParts[1];

        $samples = process(
            __DIR__ . '/samples/test/' . $file,
            __DIR__ . '/samples/processed/test/' . $file
        );
        $samples[] = $label;

        fputcsv($csv, $samples);
    }
}
closedir($dir);

fclose($csv);
