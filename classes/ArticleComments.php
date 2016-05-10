<?php

class ArticleComments extends ObjectModel {

    /** @var int Id_article */
    public $id_article;

    /** @var int Id_customer */
    public $id_customer;

    /** @var string Title */
    public $title;

    /** @var string Content */
    public $content;

    /** @var string Object creation date */
    public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'article_comments',
		'primary' => 'id_article_comments',
		'fields' => array(
            'id_article' =>     array('type' => self::TYPE_INT),   
            'id_customer' =>    array('type' => self::TYPE_INT),   
            'title' =>          array('type' => self::TYPE_STRING, 'size' => 64),
            'content' =>        array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 65535, 'required' => true),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
        
        ),
	);


    /**
     * Select all comment in terms of the ID_article
     *
     * @param int $id_article ID of the current article
     * @return array Comments list of the article
     */
    public static function getAllCommentsById($id_article)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT ac.id_article_comments,ac.id_article, 
            ac.title, ac.content, ac.date_add, c.firstname,c.lastname
            FROM `'._DB_PREFIX_.'article_comments` AS ac
            INNER JOIN `'._DB_PREFIX_.'customer` AS c ON ac.id_customer = c.id_customer
            WHERE ac.id_article ='.(int)$id_article.'
            LIMIT 0,50
            '
        );
    }

    /**
     * Inset comment in the article
     *
     * @param int $id_article ID of the current article
     * @param int $id_customer ID of the customer who posted the comment
     * @param string $title Title of the comment
     * @param string $content Content of the comment
     * @return bool 
     */
    public static function setComments($id_article, $id_customer, $title, $content)
    {
        $date_publication = date('Y-m-d H:i:s');

        return Db::getInstance()->insert('article_comments', array(
        'id_article' => (int)$id_article,
        'id_customer'=> (int)$id_customer,
        'title'=> pSQL($title),
        'content' => pSQL($content),
        'date_add' => $date_publication,
        ));
    }




}