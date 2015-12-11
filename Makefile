adapters="ZipAdapter" "ZipExtensionAdapter" "GNUTar\\TarGNUTarAdapter" "GNUTar\\TarGzGNUTarAdapter" "GNUTar\\TarBz2GNUTarAdapter" "BSDTar\\TarBSDTarAdapter" "BSDTar\\TarGzBSDTarAdapter" "BSDTar\\TarBz2BSDTarAdapter"

node_modules:
	- npm install connect serve-static

test: node_modules
	- ./tests/bootstrap.sh stop
	- ./tests/bootstrap.sh start
	- sleep 1
	- ./vendor/bin/phpunit
	- for adapter in $(adapters); do \
	    echo $$adapter; \
	    ZIPPY_ADAPTER=$$adapter ./vendor/bin/phpunit -c phpunit-functional.xml.dist; \
	  done
	- ./tests/bootstrap.sh stop

clean:
	- rm -rf node_modules
