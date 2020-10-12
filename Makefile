
.PHONY: start
start:
	vagrant up

.PHONY: test
test:
	vagrant ssh -c 'cd code && php artisan test'

