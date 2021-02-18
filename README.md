PHP Client
================

This repository contains the PHP SDK for MailWizz EMA.  
You'll find proper example on how to manage lists, subscribers, campaigns, templates and more.

The documentation website at https://api-docs.mailwizz.com/ showcases all the API endpoints.  
You can find them in the example folder as well.

Implementations using MailWizz PHP SDK:
- https://github.com/thangtx/mailwizzphpapi-wrap - A small rest app that acts as a proxy between mailwizz and any other software.

Looking for Node.js implementations instead?
- https://www.npmjs.com/package/node-mailwizz

### Install
You can either download latest version of the code or you can install it via composer as follows:  
`composer require ems-api/php-client`  
Then follow the instructions from `example/setup.php` file.

## Test
Following environment variables have to be set, with their proper values:  
`EMS_API_URL`  
`EMS_API_PUBLIC_KEY`  

Then you can run the tests:
```bash
$ composer test
``` 

