
.PHONY: start
start:
	vagrant up

.PHONY: test
test:
	vagrant ssh -c 'cd code && php artisan test'

cs-fix:
	vagrant ssh -c 'cd code && vendor/bin/php-cs-fixer fix'

cs-check:
	vagrant ssh -c 'cd code && vendor/bin/php-cs-fixer fix --dry-run -v --stop-on-violation'
