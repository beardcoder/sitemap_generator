config {
	no_cache = 1
	debug = 0
	disableAllHeaderCode = 1
	admPanel = 0
	metaCharset = utf-8
	additionalHeaders.10.header = Content-Type: text/xml; charset=utf-8
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
			rootPageId = 100
			hidePagesIfNotTranslated = 0
			allowedDoktypes = 1
		}
	}
}
