#!/bin/bash

# Include useful functions
source "$(dirname "$0")/wp-bin/wp-bin.sh"

# Exit if any command fails
set -e

PACKAGE_NAME="wc-combined-shipping"
PACKAGE_VERSION=$(get_package_version_number)
PACKAGE_VERSION_PLACEHOLDER="WC_COMBINED_SHIPPING_VERSION"

# Make sure there are no changes in the working tree.  Release builds should be
# traceable to a particular commit and reliably reproducible.
check_for_clean_cwd

# Do a dry run of the repository reset. Prompting the user for a list of all
# files that will be removed should prevent them from losing important files!
reset_cwd

# Change to the expected directory.
go_to_root

# Run the build
status_message "Installing dependencies..."
composer install

status_message "Generating .pot file..."
wp i18n make-pot . resources/languages/$PACKAGE_NAME.pot --domain=$PACKAGE_NAME

# Update version in files.
status_message "Replacing version number..."
sed -i "" "s|%${PACKAGE_VERSION_PLACEHOLDE}%|${PACKAGE_VERSION}|g" $PACKAGE_NAME.php

# Generate the theme zip file
status_message "Creating archive..."
zip -r $PACKAGE_NAME.zip \
	wc-combined-shipping.php \
	app \
	bootstrap \
	resources/languages/*.{po,mo,pot} \
	vendor/ \
	LICENSE \
	CHANGELOG.md \
	-x *.git*

# Rename and cleanup.
rezip_with_version $PACKAGE_NAME $PACKAGE_VERSION

# Reset generated files.
git reset head --hard

success_message "📦  Version ${PACKAGE_VERSION} build complete."
