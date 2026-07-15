#!/usr/bin/env bash
#
# Runs the full Zippy test suite: the unit tests, then the functional tests once
# per binary adapter. A small static HTTP server (see bootstrap.sh) is started
# for the tests that fetch remote fixtures, and stopped on exit.

set -eu

# Always operate from the project root, regardless of the caller's directory.
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

ADAPTERS=(
    'ZipAdapter'
    'ZipExtensionAdapter'
    'GNUTar\TarGNUTarAdapter'
    'GNUTar\TarGzGNUTarAdapter'
    'GNUTar\TarBz2GNUTarAdapter'
    'BSDTar\TarBSDTarAdapter'
    'BSDTar\TarGzBSDTarAdapter'
    'BSDTar\TarBz2BSDTarAdapter'
)

# The functional tests download fixtures from a small Node static file server.
if [ ! -d node_modules/connect ] || [ ! -d node_modules/serve-static ]; then
    npm install connect serve-static
fi

stop_server() {
    ./tests/bootstrap.sh stop
}
trap stop_server EXIT

./tests/bootstrap.sh stop
./tests/bootstrap.sh start
sleep 1

# Unit tests
./vendor/bin/phpunit

# Functional tests, one adapter at a time
failures=0
for adapter in "${ADAPTERS[@]}"; do
    ZIPPY_ADAPTER="$adapter" ./vendor/bin/phpunit -c phpunit-functional.xml.dist || failures=1
done

exit "$failures"
