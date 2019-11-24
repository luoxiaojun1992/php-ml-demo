<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imagePath)
{
    $imagick = new Imagick($imagePath);
    $imagick->cropImage(399, 399, 0, 0);
    $imagick->scaleImage(28, 28);

    $pixels = [];
    $pixelsIterator = $imagick->getPixelIterator();
    foreach ($pixelsIterator as $row => $pixelList) {
        foreach ($pixelList as $col => $pixel) {
            $blueValue = $pixel->getColorValue(Imagick::COLOR_BLUE);
            $greenValue = $pixel->getColorValue(Imagick::COLOR_GREEN);
            $redValue = $pixel->getColorValue(Imagick::COLOR_RED);
            if ($blueValue >= 0.99 && $greenValue >= 0.99 && $redValue >= 0.99) {
                $blueValue = $greenValue = $redValue = 0;
            }
            $pixels[] = $blueValue + $greenValue + $redValue;
        }
    }

    $imagick->destroy();

    $samples = [$pixels];

    return $samples[0];
}

$imagePath = $argv[1];
$model = (new \Phpml\ModelManager())->restoreFromFile(__DIR__ . '/models/mnist.model');
var_dump($model->predict(process($imagePath)));
