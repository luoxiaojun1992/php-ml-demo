<?php

require_once __DIR__ . '/vendor/autoload.php';

function process($imageBlob)
{
    $imagick = new Imagick();
    $imagick->readImageBlob($imageBlob);
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
