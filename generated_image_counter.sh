#! /bin/bash

until false; do echo -n 'Generated samples: '; ls samples/train/ | wc -l; sleep 1; clear; done;
