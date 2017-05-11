﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. highlight:: typoscript


.. _start:

Introduction
============

Build Status
------------

.. image:: https://travis-ci.org/markussom/sitemap_generator.svg?branch=master
   :target: https://travis-ci.org/markussom/sitemap_generator

.. image:: https://codeclimate.com/github/markussom/sitemap_generator/badges/gpa.svg
   :target: https://codeclimate.com/github/markussom/sitemap_generator

Installation
------------

**Preparation: Include static TypoScript**

*The extension ships some TypoScript code which needs to be included.*

-  Switch to the root page of your site.
-  Switch to the Template module and select Info/Modify.
-  Press the link Edit the whole template record and switch to the tab Includes.
-  Select Sitemap Generator (sitemap_generator) at the field Include static (from extensions)

-  You can override the root page ID
   plugin.tx\_sitemapgenerator.settings.urlEntries.pages.rootPageId
-  You can add custom doktypes.
   Per default the sitemap.xml only lists normal pages with "doktype=1". The option takes a comma-separated list of numbers.
   plugin.tx\_sitemapgenerator.settings.urlEntries.pages.allowedDoktypes
-  sitemap is available on rootpage with pagetype 1449874941
   "/index.php?id=1&type=1449874941"
-  You can activate the inclusion of mount points into the sitemap with "allowMountPoints". This way the subpages of the mounted page will be included as well.
   plugin.tx\_sitemapgenerator.settings.urlEntries.pages.allowMountPoints = 1

Pages
~~~~~

::

    plugin.tx_sitemapgenerator {
        urlEntries {
            pages = 1
            pages {
                rootPageId = 1
                allowedDoktypes = 1
                additionalWhere = doktype!=6
                allowMountPoints = 0
            }
        }
    }

Plugin integration
~~~~~~~~~~~~~~~~~~

::

    plugin.tx_sitemapgenerator {
        urlEntries {
            news = 1
            news {
                active = 1
                table = tx_news_domain_model_news
                additionalWhere = pid!=0
                orderBy = title DESC
                limit = 0,10
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

Hide if not translated
~~~~~~~~~~~~~~~~~~~~~~

A record that has no translation will not be shown.

::

    plugin.tx_sitemapgenerator.urlEntries.pages {
        hidePagesIfNotTranslated = 1
    }

    plugin.tx_sitemapgenerator.urlEntries.news {
        hideIfNotTranslated = 1
    }

Additional fields
~~~~~~~~~~~~~~~~~

::

    plugin.tx_sitemapgenerator.urlEntries.news {
        changefreq = dbfield_for_changefreq
        priority = dbfield_for_priority
    }

Custom value for fields
~~~~~~~~~~~~~~~~~~~~~~~

You can use the TYPO3 TypoScript syntax to fill fields

::

    plugin.tx_sitemapgenerator.urlEntries.news {
        changefreq = TEXT
        changefreq.value = daily
    }

RealURL for beautiful sitemap.xml url
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code:: php

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

Google News-Sitemap
-------------------

https://support.google.com/news/publisher/answer/74288?hl=en

Activate for tx\_news
~~~~~~~~~~~~~~~~~~~~~

::

    plugin.tx_sitemapgenerator.googleNewsUrlEntry = 1

RealURL for beautiful sitemap\_news.xml url
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code:: php

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
