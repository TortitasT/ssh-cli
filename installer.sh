#!/bin/bash

# Build
php ssh-cli app:build --build-version=ssh-cli
BUILD_EXECUTABLE=builds/ssh-cli
chmod +x $BUILD_EXECUTABLE
echo "Build successful"

# Move to bin
DATA_PATH=~/.local/share/ssh-cli
if [ ! -d "$DATA_PATH" ]; then
    mkdir -p $DATA_PATH
fi
cp $BUILD_EXECUTABLE "$DATA_PATH/ssh-cli"
echo "Move to bin successful"

# Create .env
DATABASE_PATH="$DATA_PATH/database.sqlite"
echo "DB_DATABASE=$DATABASE_PATH" > "$DATA_PATH/.env"
echo "Env file created"

# Link executable
ln -s "$DATA_PATH/ssh-cli" ~/.local/bin/ssh-cli
echo "Link successful"

echo "Installation successful"
