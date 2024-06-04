# Admin Notices Manager for WordPress

* Contributors: WPWhiteSecurity
* Plugin URI: https://www.wpwhitesecurity.com/
* License: GPLv2
* License URI: http://www.gnu.org/licenses/gpl.html

## Description
Tired of ever-present notices in the WordPress administration?

## Dependencies

1. [Node >= 8.11 & NPM](https://www.npmjs.com/get-npm) - Build packages and 3rd party dependencies are managed through NPM, so you will need that installed globally.

## Getting Started

- Clone the repository
- `cd` into the plugin folder
- run `composer install --no-dev` to install necessary composer dependencies (for production build only)
- run `npm install` to install necessary npm dependencies

## NPM Commands

- `npm run translate` (regenerate the POT translation file)
- `npm run zip` (build ZIP file for release)

## Releasing a version update
All code changes in the plugin should be done in branches that are branched out of the `develop` branch. Whenever the code is ready and we are releasing a version update of both the premium and free edition of this plugin, follow the below procedure:

1. In case there are any, merge all open branches to the `develop` branch.
2. Check if any pull requests / changes need to be retrofitted from the `master` branch to the `develop` branch. These are not common, but sometimes we do branch out from the `master` branch to release hotfixes. **Important**: Merge any hotfixes from the `master` branch to the `develop` branch before the final testing.
3. Change the version number in the following:
    1. `readme.txt` file
    2. `admin-notices-manager.php` file: in the header and also constant `ADMIN_NOTICES_MANAGER_VERSION`
    3. `package.json` file
    4. `composer.json` file
4. Run the following command: `npm install`
5. Build the necessary files with this command: `gulp build`
6. Update the plugin translation files with this command: `gulp i18n`
8. Update change log.
9. If need be, commit any pending changes and push to the `develop` repo.

### Creating the plugin zip file

At this stage the code of the Admin Notices Manager plugin is ready for release. Run the command `npm run zip` to generate the zip file for testing.
