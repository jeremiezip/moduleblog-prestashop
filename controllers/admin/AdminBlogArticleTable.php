<?php

class AdminBlogArticleTableController extends ModuleAdminController 
{

	public function __construct()
	{
			
		$this->bootstrap = true;	
			
		$this->table = 'article';
		$this->className = 'Article';
		$this->lang = true;
		$this->noLink = true;
        $this->context = Context::getContext();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
                )
            );		

		parent::__construct();
		
	}

    /**
     * Set default medias for this controller
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('tagify');
    }


    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('details');

            $this->fields_list = array(
            'id_article' => array(
                'title' => $this->l('ID article'),
                'align' => 'center',
                'type' => 'int',
            ),
            'title' => array(
                'title' => $this->l('Titre article'),
                'align' => 'center',
                'type' => 'text',
            ),  
            'id_article_category' => array(
                'title' => $this->l('Nom categorie'),
                'align' => 'center',
                'type' => 'int',
            ),  
            'author' => array(
                'title' => $this->l('Auteur'),
                'align' => 'center',
                'type' => 'text',
            ),  
            'date_add' => array(
                'title' => $this->l('Date d\'ajout'),
                'align' => 'center',
                'type' => 'datetime',
            ),  
            'date_upd' => array(
                'title' => $this->l('Date mise à jour de l\'article'),
                'align' => 'center',
                'type' => 'datetime',
            ),  
            'active' => array(
                'title' => $this->l('Affiché'),
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => false
            ),  
        );

        $this->list_no_link = false;
        // GET NAME AUTHOR 
        $article = Article::getAllArticles();
        //IF NO ARTICLE, NO JOINTURE ^^
        if(!empty($article)){
            $this->_select .= 'CONCAT(e.firstname, \' \', e.lastname) author';
            $this->_join .= ' INNER JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)';
        }
        $lists = parent::renderList();

        parent::initToolbar();

        return $lists;
    }


	public function renderForm()
    {
        global $cookie;


        // GET CATEGORIES
       $options = array();
        foreach (ArticleCategory::getAllCategoriesActiveOrNot((int)1, (int)$this->context->language->id) as $category)
        {
              $options[] = array(
                "id_article_category" => (int)$category['id_article_category'],
                "name" => $category['name']
              );
        }

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Ajouter un article'),
                'image' => '../img/admin/add.gif'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->l('Titre :'),
                    'name' => 'title',
                    'required' => true,
                    'size' => 60,
                    'class' => 'copyNiceUrl'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'maxchar' => 70,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 100,
                    'hint' => $this->l('Forbidden characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'maxchar' => 160,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 100,
                    'hint' => $this->l('Forbidden characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'lang' => true,
                    'label' => $this->l('Meta mot clés:'),
                    'name' => 'meta_keywords',
                    'hint' => $this->l('To add "tags," click in the field, write something, and then press "Enter."').'&nbsp;'.$this->l('Forbidden characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Contenu'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Only letters and the hyphen (-) character are allowed.')
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_employee',
                    'required' => true
                ),
                array(
                  'type' => 'select',                              // This is a <select> tag.
                  'label' => $this->l('Choisir une catégorie'),         // The <label> for this <select> tag.
                  'desc' => $this->l('Choisir une catégorie'),  // A help text, displayed right next to the <select> tag.
                  'name' => 'id_article_category',                     // The content of the 'id' attribute of the <select> tag.
                  'required' => true,                              // If set to true, this option must be set.
                  'options' => array(
                    'query' => $options,                           // $options contains the data itself.
                    'id' => 'id_article_category',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                    'name' => 'name'                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                  )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Affiché'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
        if (!($obj = $this->loadObject(true))) {
            return;
        }


        $this->fields_value = array('id_employee' => $this->context->cookie->id_employee);

        return parent::renderForm();
    }



}

