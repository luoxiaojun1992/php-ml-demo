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

$model = (new \Phpml\ModelManager())->restoreFromFile(__DIR__ . '/models/mnist.model');

$swHttpServer = new \Swoole\Http\Server(
    '0.0.0.0',
    8080
);

$swHttpServer->on('start', function ($server) {
    echo 'Server started...', PHP_EOL;
});

$swHttpServer->on('request', function($request, $response) use ($model) {
    try {
        $imagePath = $request->rawContent();

        $response->end(json_encode([
            'code' => 0,
            'msg' => 'ok',
            'data' => [
                'number' => $model->predict(process($imagePath)),
            ]
        ]));
    } catch (\Throwable $e) {
        $response->end(json_encode([
            'code' => 0,
            'msg' => $e->getMessage() . ' | ' . $e->getTraceAsString(),
            'data' => [],
        ]));
    }
});

$swHttpServer->set([
    'enable_coroutine' => false,
    'worker_num' => 1,
    'document_root' => __DIR__,
    'enable_static_handler' => true,
]);

$swHttpServer->start();
