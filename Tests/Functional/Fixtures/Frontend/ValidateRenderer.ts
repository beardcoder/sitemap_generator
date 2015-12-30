config {
	disableAllHeaderCode = 1
	admPanel = 0
	metaCharset = utf-8
	additionalHeaders = Content-Type:text/xml;charset=utf-8
	disablePrefixComment = 1
}

page = PAGE
page {
	10 = USER
	10 {
		userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
		extensionName = SitemapGenerator
		pluginName = Pi1
		vendorName = Markussom
		controller = Sitemap
		action = List

		view < plugin.tx_sitemapgenerator.view
		persistence < plugin.tx_sitemapgenerator.persistence
	}
}

plugin.tx_sitemapgenerator {
	view {
		templateRootPath = EXT:sitemap_generator/Resources/Private/Templates/
		partialRootPath = EXT:sitemap_generator/Resources/Private/Partials/
		layoutRootPath = EXT:sitemap_generator/Resources/Private/Layouts/
	}

	urlEntries {
		pages = 1
		pages {
			rootPageId = 1
		}

		news = 1
		news {
			active = 1
			table = news
			lastmod = tstamp
			priority = sitemap_priority
			changefreq = sitemap_changefreq
			url = TEXT
			url {
				typolink.parameter = 2
				typolink.additionalParams = &tx_news[controller]=News&tx_news[action]=detail&tx_news[news]={field:uid}
				typolink.additionalParams.insertData = 1
				typolink.useCacheHash = 1
				typolink.returnLast = url
				typolink.forceAbsoluteUrl = 1
			}
		}
	}
}
