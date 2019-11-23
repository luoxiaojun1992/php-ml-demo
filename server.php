<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imageBlob)
{
    $imagick = new Imagick();
    $imagick->readImageBlob($imageBlob);
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

try {
    $imagePath = file_get_contents('php://input');
    $model = (new \Phpml\ModelManager())->restoreFromFile(__DIR__ . '/models/mnist.model');

    echo json_encode([
        'code' => 0,
        'msg' => 'ok',
        'data' => [
            'number' => $model->predict(process($imagePath)),
        ]
    ]);
} catch (\Throwable $e) {
    echo json_encode([
        'code' => 0,
        'msg' => $e->getMessage() . ' | ' . $e->getTraceAsString(),
        'data' => [],
    ]);
}
