DOCKER_COMPOSE_DIR=./docker_warpd
DOCKER_COMPOSE_FILE=$(DOCKER_COMPOSE_DIR)/docker-compose.yml
DOCKER_COMPOSE=docker-compose -f $(DOCKER_COMPOSE_FILE) --project-directory $(DOCKER_COMPOSE_DIR)
DOCKER_BUILD=docker build

CONTAINER_APP=warpd-app
CONTAINER_DB=warpd-dbase

DEFAULT_GOAL := help
help:
	@sed -ne '/@sed/!s/## //p' $(MAKEFILE_LIST)

## Start all docker containers. To only start one container, use CONTAINER=<service>
.PHONY: build-up
build-up:
	$(DOCKER_COMPOSE) build $(CONTAINER) && \
	$(DOCKER_COMPOSE) up --force-recreate $(CONTAINER)

## Build all docker images. Build a specific image by providing the service name via: make build CONTAINER=<service>
.PHONY: build
build:
	$(DOCKER_COMPOSE) build $(CONTAINER)

## Start all docker containers. To only start one container, use CONTAINER=<service>
.PHONY: up
up:
	$(DOCKER_COMPOSE) up $(CONTAINER)

## Stop all docker containers. To only stop one container, use CONTAINER=<service>
.PHONY: down
down:
	$(DOCKER_COMPOSE) down $(CONTAINER)

## Build all docker images from scratch, without cache etc. Build a specific image by providing the service name via: make rebuild CONTAINER=<service>
.PHONY: rebuild
rebuild:
	$(DOCKER_COMPOSE) rm -fs $(CONTAINER) && \
	$(DOCKER_COMPOSE) build --pull --no-cache $(CONTAINER)

## Build all docker images from scratch, without cache etc. Build a specific image by providing the service name via: make rebuild CONTAINER=<service>
.PHONY: rebuild-up
rebuild-up:
	$(DOCKER_COMPOSE) rm -fs $(CONTAINER) && \
	$(DOCKER_COMPOSE) build --pull --no-cache $(CONTAINER) && \
	$(DOCKER_COMPOSE) up --force-recreate $(CONTAINER)

.PHONY: start-service
start-service: ## Starts a specific service; Usage: make start-service CONTAINER={warpd-app|warpd-dbase...}
	$(DOCKER_COMPOSE) up -d --no-recreate $(CONTAINER)

.PHONY: stop-service
stop-service: ## Stop a specific service; Usage: make stop-service CONTAINER={warpd-app|warpd-dbase...}
	$(DOCKER_COMPOSE) rm --stop -f $(CONTAINER)

.PHONY: recreate-service
recreate-service: stop-service start-service ## Restarts a specific service; Usage: make recreate-service CONTAINER={warpd-app|warpd-dbase...}