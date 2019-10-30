## availability-notifier-sylius
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/workouse/availability-notifier-sylius/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/workouse/availability-notifier-sylius/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/workouse/availability-notifier-sylius/badges/build.png?b=master)](https://scrutinizer-ci.com/g/workouse/availability-notifier-sylius/build-status/master)

This plugin provides "send notification when product is available" feature for products out of stock.

## Installation
```bash
$ composer require workouse/availability-notifier-sylius
```
Add plugin dependencies to your `config/bundles.php` file:
```php
return [
    ...

    Workouse\AvailabilityNotifierPlugin\WorkouseAvailabilityNotifierPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    
    - { resource: "@WorkouseAvailabilityNotifierPlugin/Resources/config/config.yml" }
```

Import routing in your `config/routes.yaml` file:

```yaml

# config/routes.yaml
...

workouse_availability_notifier_plugin:
    resource: "@WorkouseAvailabilityNotifierPlugin/Resources/config/routing.yml"
```

Finish the installation by updating the database schema and installing assets:
```
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
$ bin/console cache:clear
```

## Testing & running the plugin
```bash
$ composer install
$ cd tests/Application
$ yarn
$ yarn build
$ bin/console assets:install public -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ vendor/bin/behat
```
