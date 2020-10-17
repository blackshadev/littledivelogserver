
.PHONY: start
start:
	vagrant up

.PHONY: test
test:
	vagrant ssh -c 'cd code && php artisan test'

cs-fix:
	vagrant ssh -c 'cd code && vendor/bin/php-cs-fixer fix --config=.php_cs.dist  --using-cache=0'

cs-check:
	vagrant ssh -c 'cd code && vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run -v --diff --using-cache=0'
