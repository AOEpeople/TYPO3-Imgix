# TYPO3-Imgix
[![Build Status](https://github.com/AOEpeople/TYPO3-Imgix/workflows/CI/badge.svg?branch=master)](https://github.com/AOEpeople/TYPO3-Imgix/actions)
[![Code Coverage](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/?branch=master)

The "imgix" TYPO3 extension provides the Auto Responsive Images feature of [imgix][3] called [imgix.fluid()][4].
This allows you to deliver perferctly custom sized images for the client without using local capaticities. 
For more details have a look at [imgix][3], [imgix.fluid()][4] and the JS library [imgix.js][5].

In addition this extension provides some additional features like:

 - Supports different integrations  (jQuery and AngularJS)
 - Fallback scenario if you disable the fluid feature
 - Observation of asynchronously added images

## Missing/Upcoming features:

 - Currently this extension is limited to images on which you have access to manipulate the way the image is outputted to the browser. 
The reason for that is that img tags must have a specific class set and a data-src attribute in which the image url is stored.
In future releases we will implement this in TYPO3´s standard rendering.
 - Version 3 of [imgix.js][5] is not supported yet

## Download / Installation

```bash
composer require aoe/imgix
```
## Documentation

### Activate Extension
Once the "imgix" extension is installed you have to activate the extension in the TYPO3 "Extension Manager".
You can do that by using the the TYPO3 Backend Module or using the comand line tool.

### Configure Extension
Click the "configure" action button to open the configuration. 
You can also do that by clicking the extension title.

The configuration is seperated in two parts: "basic" and "imgix"

#### basic
The basic configuration includes specific settings of the extension which you need to set up for your project.

##### basic.apiKey
This is the apiKey you have specified in the imgix webapp. The API-key must be configured when you want to purge images in imgix.
For further information have a look at the [imigx doumentation][10].

##### basic.host
This is the host you have specified in the imgix webapp as source for your project. Be aware that you have to use the "Web Folder" source in imgix.
For further information have a look at the [imigx doumentation][6].

##### basic.enabled
If basic.enabeld is set to false, the JS will never be rendered into browser.

##### basic.enableFluid
Check this configuration if the image urls should be replaced by the configured basic.host configuration.
If basic.enabeld is set to false, it will cause a fallback behavior: all image urls will be used as they are.
This is helpful if you want to disable the responsive images from imgix using a simple checkbox without having broken or missing images.

##### basic.enableObservation
If your JS adds images dynamically/asynchroniously, this setting will observe these changes to the DOM
and will add the responsive image feature to new HTML image tags.
Be careful by enabling this option. This feature is realized by [Mutation Observers][7] which is not supported by all browsers at the moment.

#### imgix
The imigx configurations are [imgix.js][5] related settings.
For a detailed description about the options, take a look at the [documentation][8].

##### imgix.defaultUrlParameters
If some of the [URL API Parameters][9] should be set by default, list them here as a GET-Parameter string, e.g. q=75&fit=max


### Include
To use this extension you have to add a static template file to your template record.

 1. "imgix: Common Constants (imgix)"

Now you have to take a decision for one of the supported integrations:

#### AngularJS
For AngularJS you have to add these two static template files to your template record:

 1. imgix: Load Angular Extbase-Plugin for further usage (imgix)
 2. imgix: Include Angular-Module files into page (imgix)

#### jQuery
For jQuery you have to add these two static template files to your template record:

 1. imgix: Load Jquery Extbase-Plugin for further usage (imgix)
 2. imgix: Include Jquery-Plugin files into page (imgix)

### Usage

#### AngularJS Module

##### Add the module dependency

```html
angular.module('your-module', ['aoe.imgixify']);
```

##### Use the "aoe-imgix" directive for images

```html
<img aoe-imgix class="imgix-fluid" data-src="fileadmin/my-fancy-image.jpg">
```

#### jQuery Plugin

To use responsive images you have to add the following class and attribute to you HTML image tag:
- add the "image-fluid" class defined in [fluidClass][8] 
- add the the data-src attribute.

```html
<img class="imgix-fluid" data-src="fileadmin/my-fancy-image.jpg">
```

## License

License: GPLv3 or later. See LICENSE.

## Contributing

	1. Fork the repository on Github
	2. Create a named feature / bugfix branch (like `feature/add-something-new` or `bugfix/thing-which-does-not-work`)
	3. Write your change
	4. Write tests for your change (if applicable)
	5. Run the tests, ensuring they all pass
	6. Submit a Pull Request using Github

[1]: http://typo3.org/extensions/repository/view/imgix
[2]: https://docs.typo3.org/typo3cms/extensions/imgix/
[3]: https://www.imgix.com/
[4]: https://www.imgix.com/imgix-js#section-3
[5]: https://github.com/imgix/imgix.js/
[6]: https://docs.imgix.com/setup/creating-sources#source-web-folder
[7]: http://caniuse.com/#feat=mutationobserver
[8]: http://github.com/imgix/imgix.js/blob/master/docs/api.md#imgix.fluid
[9]: https://docs.imgix.com/apis/url
[10]: https://docs.imgix.com/setup/purging-images
