# TYPO3-Imgix
[![Build Status](https://travis-ci.org/AOEpeople/TYPO3-Imgix.svg?branch=master)](https://travis-ci.org/AOEpeople/TYPO3-Imgix)
[![Code Coverage](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AOEpeople/TYPO3-Imgix/?branch=master)

The "imgix" TYPO3 extensions enables the Auto Responsive Images feature of [imgix][1] called [imgix.fluid()][4].
This allows you to deliver perferctly sized images depending on the client without using local capaticities. For more details have a look at [imgix][1], [imgix.fluid()][4] and the JS library [imgix.js][5].

In addition this extension provide some additional features like:
 - fallback scenario if you disable the fluid feature
 - observation of asynchoniously added images

## Missing/Upcoming features:

Currently this extension is limited on images on which you have access to manipulate the way the image is outputted to the browser. This is because of the fact that img tags must have a specific class set and a data-src attribute in which the image url is stored.
In future releases we will implement this in TYPO3´s standard rendering.

## Download / Installation

You can download and install this extension from the [TER (TYPO3 Extension Repository)][1].

Or use composer to install the "imgix" extension
```bash
composer require aoe/imgix
```
## Documentation

The documentation is available online at [docs.typo3.org][2].

## License

License: GPLv3 or later. See LICENSE.

## Contributing

	1. Fork the repository on Github
	2. Create a named feature / bugfix branch (like `feature_add_something_new` or `bugfix\thing_which_does_not_work`)
	3. Write your change
	4. Write tests for your change (if applicable)
	5. Run the tests, ensuring they all pass
	6. Submit a Pull Request using Github

[1]: http://typo3.org/extensions/repository/view/imgix
[2]: https://docs.typo3.org/typo3cms/extensions/imgix/
[3]: https://www.imgix.com/
[4]: https://www.imgix.com/imgix-js#section-3
[5]: https://github.com/imgix/imgix.js/
