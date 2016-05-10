<?php

class Article extends ObjectModel {
    
    /** @var int Id article category*/
    public $id_article_category;
    
    /** @var int Id employee */
    public $id_employee;

    /** @var string Title */
    public $title;

    /** @var string Content */
    public $content;    	

    /** @var string Meta title */
    public $meta_title;

    /** @var bool Active for display */
    public $active = 1;

    /** @var string Meta description */
    public $meta_description;	

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string used in rewrited URL */
    public $link_rewrite;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;    
	

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'article',
		'primary' => 'id_article',
		'multilang' => true,
        'multilang_shop' => true,
		'fields' => array(
            'id_article_category' =>            array('type' => self::TYPE_INT),   
            'id_employee' =>            array('type' => self::TYPE_INT),                     
            'active' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),			
            'link_rewrite' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 64),	
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'title' =>        		array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),            
            'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false,  'validate' => 'isGenericName', 'size' => 255),
            'content' =>            array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),		
		),
	);

    /**
     * Select all articles 
     * @return array with all articles found
     */

    public static function getAllArticles()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'article`
            '
        );
    }

    /**
     * Select article with his ID
     * @param int $id_shop Id of the article
     * @param int $id_shop Current shop
     * @param int $id_lang Current lang 
     * @return Array of the article
     */    
    public static function getArticleById($id, $id_lang, $id_shop)
    {

        $article = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'article_lang` as al
            LEFT JOIN `'._DB_PREFIX_.'article` AS a ON al.id_article = a.id_article           
            INNER JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)
            WHERE al.id_lang = '.$id_lang.' 
            AND al.id_shop = '.$id_shop.'
            AND al.id_article = '.$id.'
            '
        );            
        // AJOUT CATEGORY SEPAREMENT POUR EVITER BUG JOINTURE
        if(!empty($article)){
            $article['category']= ArticleCategory::getCategoryById((int)$article['id_article_category'],(int)$id_lang, (int)$id_shop);
        }
        return $article;
    }

    /**
     * Select the total of number page
     *
     * @param int $messagesParPage Number of messages per page
     * @param int $id_article_category DEFAULT FALSE Id of the category
     * @return int The total number of page
     */
    public function getTotalNbrPages($messagesParPage, $id_article_category=false)
    { 
        $sql ='
            SELECT COUNT(*) AS TOTAL 
            FROM `'._DB_PREFIX_.'article`
            WHERE active = 1';

        if(!empty($id_article_category)){
        $sql.= ' AND id_article_category = '.(int)$id_article_category.'';
        }
        
        $total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $nombreDePages=ceil($total/$messagesParPage);
        return $nombreDePages;
    }

    /**
     * Select the article by page
     *
     * @param int $page current page
     * @param int $id_lang current Lang
     * @param int $id_shop
     * @param int $id_article_category DEFAULT FALSE Id of the category
     * @return array List of article by page
     */

    public function getArticlesByPage($page,$id_lang,$id_shop,$id_article_category=false)
    {

        (Configuration::get('NBR_ARTICLE')) ? $messagesParPage = Configuration::get('NBR_ARTICLE') : $messagesParPage = 10;
        
        // ON RECUPERE LE NOMBRE DE PAGE TOTAL
        $nombreDePages = $this->getTotalNbrPages($messagesParPage,$id_article_category);

        // SI LE NOMBRE DE PAGE EST EGAL A ZERO IL N'Y PAS DARTICLE DONC ON RETOURN FALSE
        if($nombreDePages == 0) {
            return false;
        }
        else {
            if($page>$nombreDePages) {$page=$nombreDePages;}
            $premiereEntree=($page-1)*$messagesParPage; 

             $sql ='
                SELECT * 
                FROM `'._DB_PREFIX_.'article_lang` AS al
                LEFT JOIN `'._DB_PREFIX_.'article` AS a ON al.id_article = a.id_article           
                INNER JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)
                AND al.id_lang = '.(int)$id_lang.'';
           
           // SI LA SELECTION PAR CATEGORY EXISTE ON AJOUTE UNE CONDITION A LA REQUETE
            if(!empty($id_article_category)) {
                $sql.=' AND a.id_article_category ='.(int)$id_article_category.'' ;                
            }

            $sql.=' AND al.id_shop='.(int)$id_shop.' 
                AND a.active = 1
                ORDER BY a.date_add DESC
                LIMIT '.(int)$premiereEntree.', '.(int)$messagesParPage.'
                ';
        // on recupere le résultat de notre requete
        $list_articles = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
         // AJOUT CATEGORY SEPAREMENT POUR EVITER BUG JOINTURE
            foreach($list_articles as $k => $art) {
                $list_articles[$k]['category']= ArticleCategory::getCategoryById((int)$art['id_article_category'],(int)$id_lang, (int)$id_shop);
            }

        return $list_articles;
        }
    }

}