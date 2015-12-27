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
		action = googleNewsList
		switchableControllerActions.Sitemap.1 = googleNewsList

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

	googleNewsUrlEntry = 1
	googleNewsUrlEntry {
		table = news
		name = title
		language = TEXT
		language.value = de
		access =
		genres = tags
		publicationDate = datetime
		title = title
		keywords = keywords
		stockTickers =
		url = TEXT
		url {
			typolink.parameter = 2
			typolink.additionalParams = &tx_news_pi1[controller]=News&tx_news_pi1[action]=detail&tx_news_pi1[news]={field:uid}
			typolink.additionalParams.insertData = 1
			typolink.useCacheHash = 1
			typolink.returnLast = url
			typolink.forceAbsoluteUrl = 1
		}
	}
}
