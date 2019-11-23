#! /bin/bash

nohup php -dmemory_limit=-1 image_generator.php > /dev/null 2>&1 &
