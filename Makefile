adapters:="ZipAdapter" "ZipExtensionAdapter" "GNUTar\\TarGNUTarAdapter" "GNUTar\\TarGzGNUTarAdapter" "GNUTar\\TarBz2GNUTarAdapter" "BSDTar\\TarBSDTarAdapter" "BSDTar\\TarGzBSDTarAdapter" "BSDTar\\TarBz2BSDTarAdapter"

.PHONY: test clean apidoc

test: node_modules
	-./tests/bootstrap.sh stop
	./tests/bootstrap.sh start
	sleep 1
	./vendor/bin/phpunit
	FAILURES="";$(foreach adapter,$(adapters),ZIPPY_ADAPTER=$(adapter) ./vendor/bin/phpunit -c phpunit-functional.xml.dist || FAILURES=1;)test -z "$$FAILURES"
	-./tests/bootstrap.sh stop

node_modules:
	npm install connect serve-static

# Pin Doctum to a specific, immutable release so `make apidoc` is reproducible and
# not exposed to a moving "latest" artifact. When bumping DOCTUM_VERSION, update
# DOCTUM_SHA256 with the checksum published for that exact release
# (https://github.com/code-lts/doctum/releases). Capture it once from a trusted
# download with `sha256sum doctum.phar`, or pass it ad hoc:
#   make apidoc DOCTUM_SHA256=<sha256>
DOCTUM_VERSION ?= 5.6.0
DOCTUM_SHA256  ?= bd9fee08e7672ffdafae294cb064c71b3f6582992a5f87ac9ac9b2bcd44a5fce

doctum.phar:
	@test -n "$(DOCTUM_SHA256)" || { echo "ERROR: DOCTUM_SHA256 is not pinned. Set it to the sha256 of Doctum $(DOCTUM_VERSION) in the Makefile (or pass DOCTUM_SHA256=...)." >&2; exit 1; }
	curl -fSL -o doctum.phar "https://github.com/code-lts/doctum/releases/download/v$(DOCTUM_VERSION)/doctum.phar"
	echo "$(DOCTUM_SHA256)  doctum.phar" | sha256sum -c - || { rm -f doctum.phar; exit 1; }

apidoc: doctum.phar
	php doctum.phar update doctum.php

clean:
	rm -rf node_modules build doctum.phar
