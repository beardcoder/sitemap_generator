#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	exclude_from_sitemap int(11) DEFAULT '0' NOT NULL,
	sitemap_priority int(3) DEFAULT '5' NOT NULL,
	sitemap_changefreq varchar(255) DEFAULT '' NOT NULL,
);