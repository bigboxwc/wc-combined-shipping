#!/bin/bash

# Include useful functions
source "$(dirname "$0")/wp-bin/wp-bin.sh"

# Exit if any command fails
set -e

PACKAGE_NAME="wc-combined-shipping"
PACKAGE_VERSION=$(get_package_version_number)
PACKAGE_VERSION_PLACEHOLDER="WC_COMBINED_SHIPPING_VERSION"

# Setup.
source "$(dirname "$0")/setup-plugin.sh"

# Update version in files.
status_message "Replacing version number..."
sed -i "" "s|%${PACKAGE_VERSION_PLACEHOLDER}%|${PACKAGE_VERSION}|g" $PACKAGE_NAME.php

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
