# Kraken Image Optimization Module

## Maintainer Contact

* Ryan McLaren
  <ryansm (at) outlook (dot) com>

## Requirements

* SilverStripe >= 3.1
* Kraken.io library for PHP <https://github.com/kraken-io/kraken-php>

## Installation

* Extract all files into a folder called "kraken" under your SilverStripe root. Install the Kraken PHP API into a folder called "vendor" under your Silverstripe root, or use composer (recommended):
```
composer require rsmclaren/silverstripe-kraken
```
* Run dev/build?flush=all

* Sign up for the [Kraken API](http://kraken.io/plans/) and obtain your unique API Key and API Secret

* Enter your API Key and API Secret in the Settings > Kraken Tab fields

## Usage

After setting up your API keys, images will be automatically optimized using the Kraken API. You will likely notice that Uploading Images is slower after activating the module.

Existing images can be optimized using the Optimize Images button located in the Files section of the CMS. Individual images can also be optimized by viewing their details page and clicking the Optimize button.

You can enable the Kraken API Sandbox by using the following in your config.yml.

    Kraken:
        dev: true

You can read more about the API Sandbox at [https://kraken.io/docs/sandbox](https://kraken.io/docs/sandbox).

If you are using a version of SilverStripe <= 3.1.5 you must set the Image backend through your _config.php

    Image::set_backend('KrakenGDBackend'); //for GD
    Image::set_backend('KrakenImagickBackend'); // for ImageMagick

## Notes

Please note that images optimized using this module are replaced. A backup of the original, unoptimized image will not be saved. Please consider creating a backup of your assets folder before using this module.
