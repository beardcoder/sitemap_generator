#
# Table structure for table 'news'
#
CREATE TABLE events (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	datetime int(11) DEFAULT '0' NOT NULL,
	title tinytext,

	sitemap_priority int(3) DEFAULT '5' NOT NULL,
	sitemap_changefreq varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid)
);