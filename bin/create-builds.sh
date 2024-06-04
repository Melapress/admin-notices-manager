#!/usr/bin/env bash

noGdrive=0
doPremium=0
doFree=0
doNofs=0
doNotDelete=0
syncFree=0
syncNofs=0

premiumDir="wp-security-audit-log-premium"
freeDir="admin-notices-manager"
noFSDir="wp-security-audit-log-premium-nofs"

rootDir=$(printf "%q\n" "$(pwd)")

rm -rf builds/
mkdir builds

# Free version start
freeVersion() {
    mkdir builds/$freeDir
    eval cd "$rootDir"

    rsync -arv --exclude=.github \
        --exclude "third-party/freemius" \
        --exclude=node_modules \
        --exclude=config \
        --exclude=vendor \
        --exclude=extensions \
        --exclude=builds \
        --exclude=testing \
        --exclude=tests \
        --exclude=docker \
        --exclude=.git \
        --exclude=wsal-nofs-license.php \
        --exclude "/*.js" \
        --exclude "/*.md" \
        --exclude "/*.sh" \
        --exclude "/*.xml*" \
        . builds/$freeDir/;

    cd builds/$freeDir

	rm -rf classes/Helpers/Assets.php
	rm -rf classes/Sensors/Request.php
	rm -rf nofs/lib/class-wsal-freemius.php
	mv nofs/lib/class-wsal-freemius-free.php nofs/lib/class-wsal-freemius.php

    # mkdir extensions

    # ./bin/randomize-autoloader.sh
    # ./bin/install-dependencies.sh
    # ./bin/remove-premium.sh
    # ./bin/remove-nofs.sh
    # ./bin/set-assets.sh
    # ./bin/latestversion.sh
    # ./bin/latestversion-readme.sh
    # ./bin/plugin-name-substitution.sh
    # ./bin/make-pot.sh
    # ./bin/substitute-year.sh
    # ./bin/set-free-version.sh
 
    rm -rf bin php-scoper scoper.inc.php third-party/woocommerce
    rm -rf assets/css/
    rm -rf babel.config.js
    rm -rf composer.*
    rm -rf php-scoper/
    rm -rf phpunit.xml
    rm -rf README.md
    rm -rf scoper.inc.php
    rm -rf webpack*.js
    rm -rf .*
    rm -rf TESTS.MD
    rm -rf codeception.dist.yml
    rm -rf *.js
    rm -rf *.json
    rm -rf third-party/*.json
    rm -rf third-party/vendor/bin
    rm -rf css/jquery-ui
    rm -rf bin/
    rm -rf nofs/nofs.php
    rm -rf nofs/licensing.php
    rm -rf nofs/lib/WSAL_Plugin_Updater.php
    rm -rf third-party/*.json
    rm -rf "third-party/freemius"
    rm -rf mysqld.cnf

    rm -rf extensions/
    rm -rf css/dist/css
    rm -rf css/dist/images
    rm -rf css/dist/js
    rm -rf *.phar

    rm composer

    cd ../
    year=$(date +%Y)
    month=$(date +%m)
    day=$(date +%d)

    zip -r $year$month$day-$freeDir.zip $freeDir/* -x "**/.*"

    if [ $doNotDelete == 0 ]
    then
        rm -rf $freeDir/
    fi

    eval cd "$rootDir"
}
# Free version end

helpFunction()
{
   echo ""
   echo "Usage: $0 -g -h -p -f -n -d -s -o"
   echo -e "\t-g Upload the builds to the GDrive"
   echo -e "\t-h This help screeen"
   echo -e "\t-p Build the premium only"
   echo -e "\t-f Build the free only"
   echo -e "\t-n Build the NOFS only"
   echo -e "\t-d Do not delete the prepared directories after the build(s) are complete"
   echo -e "\t-s Create new branch in free repo with last version (branch name is 'YYYYmmdd-sync')"
   echo -e "\t-o Create new branch in NOFS repo with last version (branch name is 'YYYYmmdd-sync')"
   exit 1 # Exit script after printing help
}

while getopts "hgfpndso" opt
do
   case "$opt" in
      g ) noGdrive=$((noGdrive+1)) ;;
      h ) helpFunction ;;
      p ) doPremium=$((doPremium+1)) ;;
      f ) doFree=$((doFree+1)) ;;
      n ) doNofs=$((doNofs+1)) ;;
      d ) doNotDelete=$((doNotDelete+1)) ;;
      s ) syncFree=$((syncFree+1)) ;;
      o ) syncNofs=$((syncNofs+1)) ;;
   esac
done

if [ $doFree != 0 ]
then
    freeVersion
fi

if [ $doPremium == 0 ] && [ $doFree == 0 ] && [ $doNofs == 0 ] && [ $syncFree == 0 ] && [ $syncNofs == 0 ]
then
    freeVersion
fi