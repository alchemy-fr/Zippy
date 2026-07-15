#!/bin/sh

./tests/bootstrap.sh stop
./tests/bootstrap.sh start
sleep 1
./vendor/bin/phpunit
FAILURES="";$(foreach adapter,$(adapters),ZIPPY_ADAPTER=$(adapter) ./vendor/bin/phpunit -c phpunit-functional.xml.dist || FAILURES=1;)test -z "$$FAILURES"
-./tests/bootstrap.sh stop
