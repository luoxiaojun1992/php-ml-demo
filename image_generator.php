<?php

require_once __DIR__ . '/vendor/autoload.php';

function processTrain($imagePath, $label, $degree)
{
    $imagick = new Imagick($imagePath);
    $imagick->cropImage(399, 399, 0, 0);
    $imagick->rotateImage('#ffffff', $degree);
    $trainDir = __DIR__ . '/samples/train/';
    if (!is_dir($trainDir)) {
        mkdir($trainDir, 0777, true);
    }
    $newImagePath = $trainDir . ((string)intval(microtime(true) * 1000)) .
        '_' . $label . '.png';
    $imagick->writeImage($newImagePath);
    $imagick->destroy();
}

function processTest($imagePath, $label)
{
    $imagick = new Imagick($imagePath);
    $imagick->cropImage(399, 399, 0, 0);
    $testDir = __DIR__ . '/samples/test/';
    if (!is_dir($testDir)) {
        mkdir($testDir, 0777, true);
    }
    $newImagePath = $testDir . ((string)intval(microtime(true) * 1000)) .
        '_' . $label . '.png';
    $imagick->writeImage($newImagePath);
    $imagick->destroy();
}

$dir = opendir(__DIR__ . '/samples/origin_train');
while ($file = readdir($dir)) {
    if ((!in_array($file, ['.', '..'])) && (!(strpos($file, '.') === 0))) {
        $fileNameParts = explode('.', $file);
        $imageNameParts = explode('_', $fileNameParts[0]);
        $label = $imageNameParts[1];
        for ($i = -45; $i <= 45; ++$i) {
            processTrain(__DIR__ . '/samples/origin_train/' . $file, $label, $i);
        }
    }
}
closedir($dir);

$dir = opendir(__DIR__ . '/samples/origin_test');
while ($file = readdir($dir)) {
    if ((!in_array($file, ['.', '..'])) && (!(strpos($file, '.') === 0))) {
        $fileNameParts = explode('.', $file);
        $imageNameParts = explode('_', $fileNameParts[0]);
        $label = $imageNameParts[1];
        processTest(__DIR__ . '/samples/origin_test/' . $file, $label);
    }
}
closedir($dir);
