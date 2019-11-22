#! /bin/bash

until false; do echo -n 'Processed samples: '; ls samples/processed/train/ | wc -l; echo -n 'Processed test samples: '; ls samples/processed/test/ | wc -l; sleep 1; clear; done;
