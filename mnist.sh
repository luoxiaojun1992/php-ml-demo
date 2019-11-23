#! /bin/bash

nohup php -dmemory_limit=-1 mnist.php >> ./mnist_result 2>&1 &
