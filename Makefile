lint:
	./vendor/bin/phpcs --standard=.phpcs.xml

analyze:
	./vendor/bin/phpstan analyze -c phpstan.neon

fix:
	./vendor/bin/phpcbf --standard=.phpcs.xml

quality: lint analyze

test:
	./vendor/bin/codecept run

test-fast:
	./vendor/bin/codecept run functional
