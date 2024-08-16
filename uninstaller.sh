#!/bin/bash

BUILD_EXECUTABLE=builds/ssh-cli
BIN_PATH=~/.local/bin/ssh-cli
DATA_PATH=~/.local/share/ssh-cli
DATABASE_PATH="$DATA_PATH/database.sqlite"

rm -f $BIN_PATH
echo "link deleted"

rm -f $BUILD_EXECUTABLE
echo "build deleted"

if [ -d "$DATA_PATH" ]; then
    rm -rf $DATA_PATH
fi
echo "data path deleted"

echo "ssh-cli uninstalled successfully"
