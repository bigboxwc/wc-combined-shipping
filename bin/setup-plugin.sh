#!/bin/bash

# Include useful functions
source "$(dirname "$0")/wp-bin/wp-bin.sh"

# Exit if any command fails
set -e

# Make sure there are no changes in the working tree.  Release builds should be
# traceable to a particular commit and reliably reproducible.
check_for_clean_cwd

# Do a dry run of the repository reset. Prompting the user for a list of all
# files that will be removed should prevent them from losing important files!
reset_cwd

# Change to the expected directory.
go_to_root

status_message "Installing PHP dependencies..."
composer install

status_message "Generating .pot file..."
wp i18n make-pot . resources/languages/wc-combined-shipping.pot --domain=wc-combined-shipping
