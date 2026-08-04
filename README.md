PHP Client
================
This is the PHP Client for MailWizz's API.

### Install
Get started by installing it via composer as follows:   
`composer require ems-api/php-client`  

Then follow the instructions from `example/setup.php` file.

### Requirements
- PHP 8.2 or newer
- `ext-curl`
- `ext-json`

If you want to use the in-memory cache adapter, install `ext-apcu`. The legacy XCache adapter is no longer supported.

## Test
Following environment variables have to be set, with their proper values:  
`EMS_API_URL`  
`EMS_API_KEY`  

Then you can run the tests:
```bash
$ composer test
``` 

The API-backed endpoint tests are kept separately because they require a real MailWizz API URL and key:

```bash
$ composer test-integration
```

### Development
The `2.x` branch targets PHP 8.2+ and includes the following maintenance commands:

```bash
$ composer analyse
$ composer refactor-dry-run
$ composer refactor
```
