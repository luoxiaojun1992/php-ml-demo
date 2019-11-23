#! /bin/bash

nohup php -dmemory_limit=-1 image2mnist.php > /dev/null 2>&1 &
