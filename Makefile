
set-ids = USERID=$$(id -u) GROUPID=$$(id -g)

.PHONY: test
test:
	@${set-ids} docker-compose exec app sh -c 'php artisan test --env=unit-tests'

.PHONY: start
start:
	@${set-ids} docker-compose up -d

.PHONY: stop
stop:
	@${set-ids} docker-compose down

.PHONY: shell
shell:
	@${set-ids} docker-compose exec app /bin/sh

.PHONY: build build
build:
	@${set-ids} docker-compose build --build-arg USERID=$$(id -u) --build-arg GROUPID=$$(id -g)

build-clean:
	@${set-ids} docker-compose build --build-arg USERID=$$(id -u) --build-arg GROUPID=$$(id -g) --no-cache

push:
	@${set-ids} docker build --target=prod --build-arg USERID=$$(id -u) --build-arg GROUPID=$$(id -g) --tag=blackshadev/littledivelogserver:next-1 .
	docker push blackshadev/littledivelogserver:next-1

cs-fix:
	vagrant ssh -c 'cd code && vendor/bin/ecs check --fix --config=dev/ecs.php'

cs-check:
	vagrant ssh -c 'cd code && vendor/bin/ecs check --config=dev/ecs.php'
