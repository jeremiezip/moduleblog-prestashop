<?php 

/* Init */
$sql_requests = array();
$sql_requests[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'article_lang`;';
$sql_requests[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'article`;';
$sql_requests[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'article_category_lang`;';
$sql_requests[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'article_category`;';
$sql_requests[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'article_comments`;';