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

	persistence {
		storagePid = 3
	}

	urlEntries {
		pages = 1
		pages {
			rootPageId = 100
		}
	}
}
