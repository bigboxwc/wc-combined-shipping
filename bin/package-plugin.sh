#!/bin/bash

# Include useful functions
. "$(dirname "$0")/includes.sh"

# Exit if any command fails
set -e

# Change to the expected directory
cd "$(dirname "$0")"
cd ..

# Get version number from package.json
PACKAGE_VERSION=$(cat package.json \
  | grep version \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')

# Make sure there are no changes in the working tree.  Release builds should be
# traceable to a particular commit and reliably reproducible.
changed=
if ! git diff --exit-code > /dev/null; then
	changed="file(s) modified"
elif ! git diff --cached --exit-code > /dev/null; then
	changed="file(s) staged"
fi
if [ ! -z "$changed" ]; then
	git status
	echo "ERROR: Cannot build theme zip with dirty working tree."
	echo "       Commit your changes and try again."
	exit 1
fi

branch="$(git rev-parse --abbrev-ref HEAD)"
if [ "$branch" != 'master' ]; then
	echo "WARNING: You should probably be running this script against the"
	echo "         'master' branch (current: '$branch')"
	echo
	sleep 2
fi

# Do a dry run of the repository reset. Prompting the user for a list of all
# files that will be removed should prevent them from losing important files!
status "Resetting the repository to pristine condition."
git clean -xdf --dry-run
warning "About to delete everything above! Is this okay?"
echo -n "[Y]es/[N]o: "
read answer
if [ "$answer" != "${answer#[Yy]}" ]; then
	# Remove ignored files to reset repository to pristine condition. Previous
	# test ensures that changed files abort the wc-combined-shipping build.
	status "Cleaning working directory..."
	git clean -xdf
else
	error "Aborting."
	exit 1
fi

# Run the build
status "Installing dependencies..."
composer install

status "Generating .pot file..."
wp i18n make-pot . resources/languages/wc-combined-shipping.pot --domain=wc-combined-shipping

# Update version in files.
sed -i "" "s|%WC_COMBINED_SHIPPING_VERSION%|${PACKAGE_VERSION}|g" style.css
sed -i "" "s|%WC_COMBINED_SHIPPING_VERSION%|${PACKAGE_VERSION}|g" wc-combined-shipping.php

# Remove any existing zip file
rm -f wc-combined-shipping*.zip

# Generate the theme zip file
status "Creating archive..."
zip -r wc-combined-shipping.zip \
	wc-combined-shipping.php \
	app \
	bootstrap \
	resources/languages/*.{po,mo,pot} \
	vendor/ \
	LICENSE \
	CHANGELOG.md \
	-x *.git*

# Rename and cleanup.
unzip wc-combined-shipping.zip -d wc-combined-shipping && zip -r "wc-combined-shipping-$PACKAGE_VERSION.zip" wc-combined-shipping
rm -rf wc-combined-shipping && rm -f wc-combined-shipping.zip

# Reset generated files.
git reset head --hard

success "📦  Version $PACKAGE_VERSION build complete."
