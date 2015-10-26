#!/bin/bash

# ensure running bash
if ! [ -n "$BASH_VERSION" ];then
    echo "this is not bash, calling self with bash....";
    SCRIPT=$(readlink -f "$0")
    /bin/bash $SCRIPT
    exit;
fi

# Get the path to script just in case executed from elsewhere.
SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
cd $SCRIPTPATH

# Move everythign to within /tmp/TIMESTAMP/mongo-filesystem, which we can use 
# ADD on in the Dockerfile
# Doing this keeps the context passed to docker to a minimum during the build
# and ensures we know the name of the root folder of the project.
TIMESTAMP=`date +%s`
BUILD_PATH="/tmp/$TIMESTAMP"
PROJECT_PATH="$BUILD_PATH/mongo-filesystem"
mkdir -p $PROJECT_PATH
cd $SCRIPTPATH/../.
cp -rf * $PROJECT_PATH/.

# Load the variables from settings file.
source $PROJECT_PATH/settings/docker_settings.sh

# Copy the docker file up and run it in order to build the container.
# We need to move the dockerfile up so that it can easily add everything to the container.
cp -f $SCRIPTPATH/Dockerfile $BUILD_PATH/.
cd $BUILD_PATH

ls

# Ask the user if they want to use the docker cache
read -p "Do you want to use a cached build (y/n)? " -n 1 -r
echo ""   # (optional) move to a new line
if [[ $REPLY =~ ^[Yy]$ ]]
then
    docker build --tag $PROJECT_NAME .
else
    docker build --no-cache --pull --tag $PROJECT_NAME .
fi

# Remove the duplicated Dockerfile after the build.
rm $BUILD_PATH/Dockerfile


echo "Run the container with the following command:"
echo "bash run-container.sh"
