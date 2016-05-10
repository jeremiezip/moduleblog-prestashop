<?php

class AdminBlogCommentsTableController extends ModuleAdminController 
{

	public function __construct()
	{
			
		$this->bootstrap = true;	
			
		$this->table = 'article_comments';
		$this->className = 'ArticleComments';
		$this->noLink = true;
        $this->addRowAction('delete');
        $this->context = Context::getContext();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
                )
            );		


		parent::__construct();
		
	}


    public function renderList()
    {
        $this->fields_list = array(
            'id_article_comments' => array(
                'title' => $this->l('ID du commentaire'),
                'type' => 'int',
            ),
            'id_article' => array(
                'title' => $this->l('ID article'),
                'type' => 'int',
            ),
            'title' => array(
                'title' => $this->l('Titre du commentaire'),
                'type' => 'text',
            ),  
            'content' => array(
                'title' => $this->l('content'),
     
                'type' => 'text',
            ),  
            'date_add' => array(
                'title' => $this->l('Date d\'ajout'),
                'align' => 'center',
                'type' => 'datetime',
            ),     
            'customer' => array(
                'title' => $this->l('Auteur'),
                'align' => 'center',
                'type' => 'text',
            ),  
        );

            $this->_select .= 'CONCAT(c.firstname, \' \', c.lastname) customer';
            $this->_join .= ' INNER JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer)';

        $this->list_no_link = true;
        $this->_use_found_rows = false;
        $lists = parent::renderList();

        parent::initToolbar();

        return $lists;
    }	



}

