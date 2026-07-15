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

doctum.phar:
	curl -L -o doctum.phar https://doctum.long-term.support/releases/latest/doctum.phar

apidoc: doctum.phar
	php doctum.phar update doctum.php

clean:
	rm -rf node_modules build doctum.phar
