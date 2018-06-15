# Document Cart Module

[![Build Status](https://travis-ci.org/silverstripe/silverstripe-dms-cart.svg?branch=master)](https://travis-ci.org/silverstripe/silverstripe-dms-cart) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/silverstripe/silverstripe-dms-cart/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-dms-cart/?branch=master) [![codecov](https://codecov.io/gh/silverstripe/silverstripe-dms-cart/branch/master/graph/badge.svg)](https://codecov.io/gh/silverstripe/silverstripe-dms-cart)
[![SilverStripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

## Overview

The module adds a new `DMSDocumentCart` model which allows for the request of physical copies of documents from a
Document Management System module to be recorded as requested for printing and sending out to a physical address.

## Features

 * Ability to request printed copies of documents which are marked as "Allowed in Cart"
 * Set a per document limit on total copies which can be requested
 * View and amend historical print requests

## Documentation

For information on configuring and using this module, please see [the documentation section](docs/en/index.md).

## Requirements

 * [silverstripe/dms](https://github.com/silverstripe/silverstripe-dms) ~2.0

## Contributing

### Translations

Translations of the natural language strings are managed through a
third party translation interface, transifex.com.
Newly added strings will be periodically uploaded there for translation,
and any new translations will be merged back to the project source code.

Please use [https://www.transifex.com/silverstripe/silverstripe-dms-cart](https://www.transifex.com/silverstripe/silverstripe-dms-cart) to contribute translations,
rather than sending pull requests with YAML files.

See the ["i18n" topic](http://doc.silverstripe.org/framework/en/3/topics/i18n) on docs.silverstripe.org for more details.
