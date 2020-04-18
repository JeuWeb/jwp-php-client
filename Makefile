test:
	./vendor/bin/phpunit tests --colors=auto
dev:
	./vendor/bin/phpunit tests --colors=auto --stop-on-defect --verbose