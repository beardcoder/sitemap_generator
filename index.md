# EXT:sitemap_generator

## Build Status
[![Build Status](https://travis-ci.org/markussom/sitemap_generator.svg?branch=travis-ci)](https://travis-ci.org/markussom/sitemap_generator)
[![Code Climate](https://codeclimate.com/github/markussom/sitemap_generator/badges/gpa.svg)](https://codeclimate.com/github/markussom/sitemap_generator)

## Installation

- Include TypoScript to root page
- You can override the root page ID plugin.tx_sitemapgenerator.settings.urlEntries.pages.rootPageId
- sitemap is available on rootpage with pagetype 1449874941 "/index.php?id=1&type=1449874941"

### Pages

```
plugin.tx_sitemapgenerator {
	urlEntries {
		pages = 1
		pages {
			rootPageId = 1
		}
	}
}
```

### Plugin integration

```
plugin.tx_sitemapgenerator {
	urlEntries {
		news = 1
		news {
			active = 1
			table = tx_news_domain_model_news
			lastmod = tstamp
			url = TEXT
			url {
				typolink.parameter = 9
				typolink.additionalParams = &tx_news_pi1[controller]=News&tx_news_pi1[action]=detail&tx_news_pi1[news]={field:uid}
				typolink.additionalParams.insertData = 1
				typolink.useCacheHash = 1
				typolink.returnLast = url
				typolink.forceAbsoluteUrl = 1
			}
		}
	}
}
```

### Additional fields

```
plugin.tx_sitemapgenerator.urlEntries.news {
	changefreq = dbfield_for_changefreq
	priority = dbfield_for_priority_as_float
}
```


### RealURL for beautiful sitemap.xml url

```php
$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = [
    'fileName' => [
        'defaultToHTMLsuffixOnPrev' => 0,
        'acceptHTMLsuffix' => 1,
        'index' => [
            'sitemap.xml' => [
                'keyValues' => [
                    'type' => 1449874941,
                ]
            ]
        ]
    ]
];

```

## Google News-Sitemap
https://support.google.com/news/publisher/answer/74288?hl=en

### Activate for tx_news

```
plugin.tx_sitemapgenerator.googleNewsUrlEntry = 1
```

### RealURL for beautiful sitemap_news.xml url

```php
$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = [
    'fileName' => [
        'defaultToHTMLsuffixOnPrev' => 0,
        'acceptHTMLsuffix' => 1,
        'index' => [
            'sitemap_news.xml' => [
                'keyValues' => [
                    'type' => 1451160842,
                ]
            ]
        ]
    ]
];

```