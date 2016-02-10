adapters:="ZipAdapter" "ZipExtensionAdapter" "GNUTar\\TarGNUTarAdapter" "GNUTar\\TarGzGNUTarAdapter" "GNUTar\\TarBz2GNUTarAdapter" "BSDTar\\TarBSDTarAdapter" "BSDTar\\TarGzBSDTarAdapter" "BSDTar\\TarBz2BSDTarAdapter"

.PHONY: test clean ocular scrutinizer

test: node_modules
	-./tests/bootstrap.sh stop
	./tests/bootstrap.sh start
	sleep 1
	./vendor/bin/phpunit
	FAILURES="";$(foreach adapter,$(adapters),ZIPPY_ADAPTER=$(adapter) ./vendor/bin/phpunit -c phpunit-functional.xml.dist || FAILURES=1;)test -z "$$FAILURES"
	-./tests/bootstrap.sh stop

node_modules:
	npm install connect serve-static

clean:
	rm -rf node_modules

ocular:
	[ ! -f ocular.phar ] && wget https://scrutinizer-ci.com/ocular.phar

ifdef OCULAR_TOKEN
scrutinizer: ocular
	@php ocular.phar code-coverage:upload --format=php-clover tests/output/coverage.clover --access-token=$(OCULAR_TOKEN);
else
scrutinizer: ocular
	php ocular.phar code-coverage:upload --format=php-clover tests/output/coverage.clover;
