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

After setting up your API keys, images will be automatically optimized using the Kraken API when uploaded through any UploadField in the CMS. You will likely notice that Uploading Images is slower after activating the module. This feature can be disable from the Settings > Kraken tab.

Images that have already been uploaded to the CMS can be optimized using the Optimize Images button located in the Files section of the CMS. Individual images can also be optimized by viewing their details page and clicking the Optimize button.

## Notes

Please note that images optimized using this module are replaced. A backup of the original, unoptimized image will not be saved. Please consider creating a backup of your assets folder before using this module.
