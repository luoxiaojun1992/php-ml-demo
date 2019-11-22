#! /bin/bash

cd samples && rm -rf train && mkdir -p train && cd .. && \
cd samples/processed && rm -rf train && mkdir -p train && rm -rf test && mkdir -p test && cd ../..
