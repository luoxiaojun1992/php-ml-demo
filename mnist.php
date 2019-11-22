<?php

require_once __DIR__ . '/vendor/autoload.php';

$start = microtime(true);

echo 'Collecting samples...', PHP_EOL;
$trainDataset = new \Phpml\Dataset\CsvDataset(
    __DIR__ . '/data/train.csv',
    784,
    false
);
$trainSamples = $trainDataset->getSamples();
$trainTargets = $trainDataset->getTargets();
$testDataset = new \Phpml\Dataset\CsvDataset(
    __DIR__ . '/data/test.csv',
    784,
    false
);
$testSamples = $testDataset->getSamples();
$testTargets = $testDataset->getTargets();

echo 'Processing samples...', PHP_EOL;
foreach ($trainSamples as $i => $row) {
    foreach ($row as $j => $item) {
        $trainSamples[$i][$j] = doubleval($item);
    }
}
foreach ($trainTargets as $i => $target) {
    $trainTargets[$i] = intval($target);
}
foreach ($testSamples as $i => $row) {
    foreach ($row as $j => $item) {
        $testSamples[$i][$j] = doubleval($item);
    }
}
foreach ($testTargets as $i => $target) {
    $testTargets[$i] = intval($target);
}

echo 'Network init...', PHP_EOL;
$classifier = new \Phpml\Classification\SVC();

echo 'Training...', PHP_EOL;
$classifier->train($trainSamples, $trainTargets);

echo 'Evaluating...', PHP_EOL;
$predicted = $classifier->predict($testSamples);
echo 'Score: ' . ((string)(\Phpml\Metric\Accuracy::score($testTargets, $predicted))), PHP_EOL;

echo 'Saving model...', PHP_EOL;
$modelsDir = __DIR__ . '/models/';
if (!is_dir($modelsDir)) {
    mkdir($modelsDir, 0777, true);
}
(new \Phpml\ModelManager())->saveToFile($classifier, $modelsDir . 'mnist.model');

$usage = microtime(true) - $start;
echo 'Usage: ' . ((string)$usage) . 's', PHP_EOL;
