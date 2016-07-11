#!/bin/bash

PROJECT_NAME=build_web
if [ $1 ]; then
    PROJECT_NAME=$1
fi

docker rm -f ${PROJECT_NAME}
docker -D rmi ${PROJECT_NAME}