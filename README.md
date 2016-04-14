# Zoolanders Framework

## Basic Usage

```php
require_once JPATH_LIBRARIES . '/zoolanders/include.php';

$container = \Zoolanders\Container\Container::getInstance();
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

### system

Deals with system-related stuff (mostly related to the platform, ie: joomla).
It just exposes subservices.

#### language
Deals with language stuff

```php
$container->system->language->getTag();
```

#### application
Deals with application-level stuff

```php
$container->system->application->isAdmin();
```

#### document
An interface to JDocument

```php
$container->system->document->addScript(...);
```

Also, it exposes several methods:

- **addStilesheet($path, $version)**: Add a stylesheet to the document using also path parsable variables (media:system/file.css)
- **addScript($path, $version)**: Add a script to the document using also path parsable variables (media:system/file.js)

### db

The usual database service

```php
$container->db->execute();
```

### zoo
```php
$container->language->load(...);
```

### filesystem

```php
$container->filesystem
```