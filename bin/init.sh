#!/usr/bin/env bash

# Database connection for drush site-install.
DB_URL='mysql://admin:admin@db/drupal_octane'

CONFIRM=''
while [[ $# -gt 0 ]]; do
  case $1 in
    -y)
    CONFIRM='-y'
    shift # past argument
    ;;
    *)    # profile name
    PROFILE="$1"
    shift # past argument
    ;;
  esac
done

if [ -z "$PROFILE" ]; then
  # Default install profile is Acquia Lightning.
  PROFILE="lightning"
fi

THEME_PATH="src/themes/particle"
# Only download particle theme if it doesn't already exist.
if [ ! -e ${THEME_PATH} ]; then
  # Install latest Particle theme
  npx phase2/create-particle ${THEME_PATH}
  # @TODO: Remove this once Particle removes Husky.
  # Need to remove the default git hooks added by Husky from Particle.
  # They interfere with project hooks and with Drupal config import/export.
  rm -rf .git/hooks
fi

# Install project requirements.
composer clear-cache
COMPOSER_PROCESS_TIMEOUT=2000 COMPOSER_DISCARD_CHANGES=1 composer install

# Install Drupal site
if [ -e "src/config/default/system.site.yml" ]; then
  # If config exists, install using it.
  echo "Installing Drupal from existing config..."
  drush si --db-url=$DB_URL --account-pass="admin" --existing-config $CONFIRM
else
  # Otherwise install clean from profile.
  echo "Installing Drupal profile: ${PROFILE}..."
  drush si --db-url=$DB_URL --account-pass="admin" ${PROFILE} $CONFIRM
fi
