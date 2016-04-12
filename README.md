# Zoolanders Framework

## Basic Usage

```php
require_once JPATH_LIBRARIES . '/zoolanders/include.php';

$container = Zoolanders\Container\Container::getInstance();
```

## Services

This is a list of services that the container exposes by default.

### zoo

This service is the main entry point to any zoo related stuff.
By default it proxies any function call and any property access to the main ZOO App class (App::getInstance('zoo'));

```php
$container->zoo->table->item->save($data);
```

Also, it exposes several methods:

- **getApp**: get the zoo's app instance (App::getInstance('zoo'))
- **isLoaded**: check if zoo is loaded
- **load**: actually load zoo (if it's not already loaded)

### db

The usual database service

```php
$container->db->execute();
```

### language

Proxies the calls to the JLanguage class

```php
$container->language->load(...);
```

### filesystem

```php
$container->filesystem
```