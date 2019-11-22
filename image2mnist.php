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

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$processedTrainDir = __DIR__ . '/samples/processed/train/';
if (!is_dir($processedTrainDir)) {
    mkdir($processedTrainDir, 0777, true);
}

$processedTestDir = __DIR__ . '/samples/processed/test/';
if (!is_dir($processedTestDir)) {
    mkdir($processedTestDir, 0777, true);
}

$csv = fopen($dataDir . '/train.csv', 'wb');

$dir = opendir(__DIR__ . '/samples/train');
while ($file = readdir($dir)) {
    if (!in_array($file, ['.', '..'])) {
        $fileNameParts = explode('.', $file);
        $imageNameParts = explode('_', $fileNameParts[0]);
        $label = $imageNameParts[1];

        $samples = process(
            __DIR__ . '/samples/train/' . $file,
            $processedTrainDir . $file
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
            $processedTestDir . $file
        );
        $samples[] = $label;

        fputcsv($csv, $samples);
    }
}
closedir($dir);

fclose($csv);
