<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imagePath)
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

    $imagick->destroy();

    $samples = [$pixels];

    return $samples[0];
}

$imagePath = $argv[1];
$model = (new \Phpml\ModelManager())->restoreFromFile(__DIR__ . '/models/mnist.model');
var_dump($model->predict(process($imagePath)));
