# Acorn AcornQueueMonitor Package

This repo can be used to scaffold an Acorn package. See the [Acorn Package Development](https://roots.io/acorn/docs/package-development/) docs for further information.

## Installation

You can install this package with Composer:

```bash
composer require vendor-name/acorn-queue-monitor-package
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="DigitalNode\AcornQueueMonitorPackage\Providers\AcornQueueMonitorServiceProvider"
```

## Usage

From a Blade template:

```blade
@include('AcornQueueMonitor::acorn-queue-monitor')
```

From WP-CLI:

```shell
$ wp acorn acorn-queue-monitor
```
