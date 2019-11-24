<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imagePath, $processedImagePath)
{
    $imagick = new Imagick($imagePath);
    $imagick->scaleImage(28, 28);

    $pixels = [];
    $pixelsIterator = $imagick->getPixelIterator();
    foreach ($pixelsIterator as $row => $pixelList) {
        foreach ($pixelList as $col => $pixel) {
            $blueValue = $pixel->getColorValue(Imagick::COLOR_BLUE);
            $greenValue = $pixel->getColorValue(Imagick::COLOR_GREEN);
            $redValue = $pixel->getColorValue(Imagick::COLOR_RED);
            if ($blueValue >= 0.99 && $greenValue >= 0.99 && $redValue >= 0.99) {
                $pixel->setColorValue(Imagick::COLOR_BLUE, 0);
                $pixel->setColorValue(Imagick::COLOR_GREEN, 0);
                $pixel->setColorValue(Imagick::COLOR_RED, 0);
                $blueValue = $greenValue = $redValue = 0;
            }
            $pixels[] = $blueValue + $greenValue + $redValue;
        }
        $pixelsIterator->syncIterator();
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
    if ((!in_array($file, ['.', '..'])) && (!(strpos($file, '.') === 0))) {
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
    if ((!in_array($file, ['.', '..'])) && (!(strpos($file, '.') === 0))) {
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
