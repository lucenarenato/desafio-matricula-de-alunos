#!/bin/sh

set -e

# npm cache clear --force
# npm install --global cross-env

echo 'running prestart node script'
npm -v
node -v
echo 'running npm install'
# rm -rf node_modules
rm -rf node_modules && rm -rf package-lock.json
npm install
npm rebuild node-sass
# npm clean-install
# npm audit fix --force

echo 'initialization done, start watching'
# npm run watch-poll
#npm run dev
SHELL=/bin/sh exec npm run dev
