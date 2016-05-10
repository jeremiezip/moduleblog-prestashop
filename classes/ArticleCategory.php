<?php

class ArticleCategory extends ObjectModel {

    /** @var string Name */
    public $name;

    /** @var int Id_employee */
    public $id_employee;

    /** @var bool Active for display */
    public $active = 1;

    /** @var  int category position */
    public $position;

    /** @var string Description */
    public $description;

    /** @var string Meta title */
    public $meta_title;

    /** @var string Meta description */
    public $meta_description;	

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string used in rewrited URL */
    public $link_rewrite;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'article_category',
		'primary' => 'id_article_category',
        'multilang' => true,
        'multilang_shop' => true,
		'fields' => array(
            'name' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'description' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'required' => false,'validate' => 'isCleanHtml'),
            'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false,  'validate' => 'isGenericName', 'size' => 255),
            'active' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' =>            array('type' => self::TYPE_INT),
            'link_rewrite' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 64),	
        ),
	);


    /**
     * Select all categories, active or not
     *
     * @param int $active Bool (1 or 0) for select category active 
     * @param int $id_lang Current lang for select all the categories
     * @return array List of categories
     */
    public static function getAllCategoriesActiveOrNot($active,$id_lang)
    {
        // Categories should be active

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'article_category_lang` AS acl
            INNER JOIN `'._DB_PREFIX_.'article_category` AS ac ON acl.`id_article_category` = ac.`id_article_category`
            WHERE ac.`active` = '.$active.' 
            AND id_lang = '.$id_lang.'
            '
        );
    }

    /**
     * Select category by ID
     *
     * @param int $id Id of the category 
     * @param int $id_lang Current lang for select the category
     * @param int $id_shop Current shop
     * @return array Array of the category
     */
    public static function getCategoryById($id, $id_lang, $id_shop)
    {
        // Categories should be active

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'article` AS a
            LEFT JOIN `'._DB_PREFIX_.'article_category` AS ac ON a.`id_article_category` = ac.`id_article_category`
            LEFT JOIN `'._DB_PREFIX_.'article_category_lang` AS acl ON a.`id_article_category` = acl.`id_article_category`
            WHERE ac.`active` = 1 
            AND ac.id_article_category = '.(int)$id.' 
            AND acl.id_lang='.(int)$id_lang.' 
            AND acl.id_shop='.(int)$id_shop.'
            '
        );
    }




}