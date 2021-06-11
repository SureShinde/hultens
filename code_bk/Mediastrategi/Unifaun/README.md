# Unifaun Online for Magento 2

## Features:

* Create shipment labels manually or automatically
* Track shipments
* Multiple shipping methods with custom constraints

## Technical Standard

* Follow guidelines at <http://devdocs.magento.com/guides/v2.2/coding-standards/technical-guidelines/technical-guidelines.html>
* Follow instructions on <https://github.com/magento/marketplace-eqp> to validate and fix coding-style.

## Release

* Run `make test-coding` to test coding
* Run `make release` to make a release zip in parent folder
* Run `make test-release` to test release archive
* Run from directory `Libraries`: `markdown-pdf shipping_label_dummy.md` to generate dummy PDF

## Technical Standard

* Follow guidelines at <http://devdocs.magento.com/guides/v2.2/coding-standards/technical-guidelines/technical-guidelines.html>
* Follow instructions on <https://github.com/magento/marketplace-eqp> to validate and fix coding-style.
* Use Marketplace tools at <https://github.com/magento/marketplace-tools> to verify archive

## Setup

* Run `bin/magento cache:enable msunifaun_cache_checkout` to enable checkout cache

## Change-log

* **[2.3.22]** Set certain configuration values to be sensitive/system-specific
* **[2.3.21]** Added support for english tracking links
* **[2.3.20]** Customs declaration changed so copies are always 1 and valuesPerItem is false
* **[2.3.19]** Added support for customs declarations and better support for configurable product weights
* **[2.3.18]** Added setting for Pacsoft Online
* **[2.3.17]** Contact person is always the person not the company
* **[2.3.16]** Sending receiver state instead of senders state
* **[2.3.15]** Sending state when needed to API
* **[2.3.14]** Improved error-handling when tracking without valid credentials
* **[2.3.13]** Improved calculation of total height, length and width and made it easier to override
* **[2.3.12]** Improved calculation of package height, depth and width
* **[2.3.11]** All agents are sent with only quickId parameter
* **[2.3.10]** Added option for automatic shipment notifications
* **[2.3.9]** Always send Pick Up Location id to PostNord to avoid fee
* **[2.3.8]** Added contact as a default field on receivers
* **[2.3.7]** Fixed a rare bug where admin form URL was wrong and fixed case where shipping was disabled in a specific store
* **[2.3.6]** 2.2.6 compatibility
* **[2.3.5]** Improvements to Klarna and regular checkout templates and styles
* **[2.3.4]** Added a force refresh on cart initialization
* **[2.3.3]** Fixed bug with stored shipments
* **[2.3.2]** Added unifaun_assignments field under extension_attributes to the order API
* **[2.3.1]** Added more package-types and add-ons for carriers and services
* **[2.3.0]** Added support for company names as recipients
* **[2.2.9]** Added support for having Order Number as order reference
* **[2.2.8]** Added support for Svea Checkout
* **[2.2.7]** Added POSTI and DPD as pick up location services, improved error capture when doing stored requests
* **[2.2.6]** Removed PDF configuration from stored shipment requests
* **[2.2.5]** Better support for errors with stored shipments and Norwegian service add-ons
* **[2.2.4]** Added support for Stored Shipments and more Finnish services
* **[2.2.3]** Updated Unifaun Online library
* **[2.2.2]** Fixed bug where errors were not displayed properly and zero weight, width, length or heights were sent instead of empty string
* **[2.2.1]** Fixed bug where Custom Pick Up locations stopped working in original checkout
* **[2.2.0]** Using lighter library, storing tracking link on order, support for custom pickup locations in Klarna Checkout
* **[2.1.6]** Code styling fixes and improved release flow
* **[2.1.5]** Fixed bug with wrong variable name in tracking
* **[2.1.4]** Improved multi-store support, sendEmail and errorTo fields to Unifaun
* **[2.1.3]** Now supports multiple parcels in same shipment
* **[2.1.2]** Moved Custom Pick Up Location to step one, displaying custom pick up location on order
* **[2.1.1]** Fixed handling of multiple packages
* **[2.1.0]** Fixed measurement-units when creating packages manually on new shipment, fixed bug with configuration on installations that use a number for region setting
* **[2.0.9]** Fixed measurement-units when creating new packages manually on existing shipments
* **[2.0.8]** Added error messages in admin, added mobile field for PLAB_P17 service
