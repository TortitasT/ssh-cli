#!/bin/bash

BUILD_EXECUTABLE=builds/ssh-cli
BIN_PATH=~/.local/bin/ssh-cli
DATA_PATH=~/.local/share/ssh-cli
DATABASE_PATH="$DATA_PATH/database.sqlite"

php ssh-cli app:build --build-version=ssh-cli
chmod +x $BUILD_EXECUTABLE
echo "build successful"

if [ ! -d "$DATA_PATH" ]; then
    mkdir -p $DATA_PATH
fi
cp $BUILD_EXECUTABLE "$DATA_PATH/ssh-cli"
echo "move to bin successful"

echo "DB_DATABASE=$DATABASE_PATH" > "$DATA_PATH/.env"
echo ".env file created"

if [ ! -f "$DATABASE_PATH" ]; then
    php "$DATA_PATH/ssh-cli" migrate --force
    echo "migration successful"
fi

ln -s "$DATA_PATH/ssh-cli" $BIN_PATH
echo "link successful"

echo "ssh-cli installed successfully"
