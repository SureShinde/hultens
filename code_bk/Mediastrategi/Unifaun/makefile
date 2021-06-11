# Replace values below with your own:
PHPCS := ~/Sites/magento-coding-standard/vendor/bin/phpcs
VALIDATE_M2_PACKAGE := ~/Sites/marketplace-tools/validate_m2_package.php
ZIP_BIN := zip

VERSION := $(shell git tag | tail -n 1)
ARCHIVE_BASE := ../Mediastrategi_Unifaun_
ARCHIVE_SUFFIX := .zip
ARCHIVE_FULL := $(ARCHIVE_BASE)$(VERSION)$(ARCHIVE_SUFFIX)
SED_PREFIX := 's/\"name\"/\"version\": \"
SED_SUFFIX := \", \"name\"/g'
SED_STRING := $(SED_PREFIX)$(VERSION)$(SED_SUFFIX)

.PHONY: test-coding
test-coding: *
	$(PHPCS) --standard=Magento2 --severity=10 --extensions=php,phtml .

.PHONY: test-release
test-release: *
	$(VALIDATE_M2_PACKAGE) $(ARCHIVE_FULL)

.PHONY: release
release: *
	sed -i -e $(SED_STRING) composer.json
	$(ZIP_BIN)  -r $(ARCHIVE_FULL) . -x \*.git\* -x \*.editorconfig -x \*.DS_Store\* -x \*.tern-port -x \*.dir-locals.el -x \*ac-php-conf.json -x \*.el -x composer.json-e
	sed -i -e 's/\"version\":.*, \"name\"/\"name\"/g' composer.json
