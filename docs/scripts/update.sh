#!/bin/bash

####################################################################################################
#                                                                                                  #
# This script allows you to update a local install of the LDM Guild Website with a single command  #
#                                                                                                  #
# It can run both in PROD or DEV mode.                                                             #
#                                                                                                  #
####################################################################################################

# Comment out the correct line depending on the environment you wish to activate
#SYMFENV=prod
SYMFENV=dev

# First we clear the cache to be sure that any code and composer updates will not fail due to a stale cache
php bin/console cache:clear --env=${SYMFENV}

# Pull in any changes from origin
git pull origin

# Run a composer install based on the composer.lock we just pulled in from Git
SYMFONY_ENV=${SYMFENV} composer install --optimize-autoloader

# If present run any database migrations
php bin/console doctrine:migrations:migrate --env=${SYMFENV} --allow-no-migration --no-interaction

# Install symbolic links for web assets (this should in fact already be done by the composer post-install scripts
php bin/console assets:install web --env=${SYMFENV} --symlink

# Since we cleared the cache we need to run the bower:install command again
php bin/console sp:bower:install --env=${SYMFENV} --no-debug

# Process and dump all assets
php bin/console assetic:dump --env=${SYMFENV} --no-debug

# Refresh the guild members stored in the database
php bin/console ladanse:refreshGuildMembers --env=${SYMFENV}

# Since we cleared the cache we need to refresh wowhead news
php bin/console ladanse:refreshWowheadNews --env=${SYMFENV}