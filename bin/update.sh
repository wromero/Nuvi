#!/bin/sh

if [[ $EUID -eq 0 ]]; then
   echo "This script must not be run as root probably apache 'su - apache -s /bin/sh' " 1>&2
   exit 1
fi

command=$1

function update() {
    composer install -o
    app/console doctrine:migrations:migrate --env=prod
    rm -rf app/cache/*
}

function getLatest() {
    git pull > /dev/null 2>&1 || { echo >&2 "Unable to pull latest version"; exit 1; }
    update
}

function getLatestRelease() {
    TAG=`git tag -l | tail -n1`
    echo "UPDATING TO RELEASE $(TAG)";
    git checkout $TAG
    update
}

if [[ -n "$command" ]]; then
   git fetch > /dev/null 2>&1 || { echo >&2 "git is required.  Aborting."; exit 1; }
   git stash
   case "$command" in
     'latest')
        getLatest
        ;;
     'latest-release')
        getLatestRelease
        ;;
     *)
        echo 'unknown command';
        ;;
   esac
   git stash pop
else
    echo "Missing arguments [latest|latest-release]";
fi

