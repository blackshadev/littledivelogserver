
.PHONY: start
start:
	vagrant up

.PHONY: test
test:
	vagrant ssh -c 'cd code && php artisan test'

cs-fix:
	vagrant ssh -c 'cd code && vendor/bin/ecs check --fix --config=dev/ecs.php'

cs-check:
	vagrant ssh -c 'cd code && vendor/bin/ecs check --config=dev/ecs.php'
