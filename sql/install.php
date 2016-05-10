<?php 

$sql_requests = array();
//$langs = Language::getLanguages(false);

// CREATION DE LA TABLE CATEGORY_ARTICLE

$sql_requests[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'article_category`(
					id_article_category INT(10) NOT NULL AUTO_INCREMENT,
					active TINYINT(1) UNSIGNED DEFAULT 0,
					position INT(10) UNSIGNED DEFAULT 0,
				    PRIMARY KEY (`id_article_category`)
					)
   					ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// CREATION DE LA TABLE ARTICLE CATEGORY_ARTICLE_LANG

$sql_requests[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'article_category_lang`(
					id_article_category INT(10) NOT NULL AUTO_INCREMENT,
					id_shop INT(10) NOT NULL,					
					id_lang INT(10) NOT NULL,					
					name VARCHAR(60) NOT NULL,
					description TEXT,
					meta_title VARCHAR(128),
					meta_keywords VARCHAR(255),
					meta_description VARCHAR(255),
					link_rewrite VARCHAR(64) NOT NULL,
    				UNIQUE KEY `article_category_index` (`id_article_category`, `id_shop`, `id_lang`)
					)
   					ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';


// CREATION DE LA TABLE ARTICLE
// id_article_category = recupérer l'id de la catégorie de l'article

$sql_requests[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'article`(
					id_article INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					id_employee INT(10) UNSIGNED NOT NULL,
					id_article_category INT(10),
					active TINYINT(1) UNSIGNED DEFAULT 0,
					date_add DATETIME NOT NULL,
					date_upd DATETIME NOT NULL,
				    PRIMARY KEY (`id_article`)
					)
   					ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
    
// CREATION DE LA TABLE ARTICLE ARTICLE_LANG

$sql_requests[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'article_lang`(
					id_article INT(10) NOT NULL,
					id_lang INT(10) NOT NULL,
					id_shop INT(10) NOT NULL,					
					title VARCHAR(70) NOT NULL,
					content TEXT,
					meta_title VARCHAR(128),
					meta_keywords VARCHAR(255),
					meta_description VARCHAR(255),
					link_rewrite VARCHAR(128) NOT NULL,
    				UNIQUE KEY `article_lang_index` (`id_article`, `id_lang`, `id_shop`)    
					)
   					ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql_requests[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'article_comments`(
					id_article_comments INT(10) NOT NULL AUTO_INCREMENT,
					id_article INT(10) NOT NULL,
	  				id_customer int(10) unsigned NOT NULL,
   					title varchar(64) NULL,
   					date_add DATETIME NOT NULL,
					content TEXT,
				    PRIMARY KEY (`id_article_comments`),
  					KEY `id_customer` (`id_customer`)
					)
   					ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';