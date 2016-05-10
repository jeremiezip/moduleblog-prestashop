<?php

class AdminBlogCategoryTableController extends ModuleAdminController 
{

	public function __construct()
	{
			
		$this->bootstrap = true;	
			
		$this->table = 'article_category';
		$this->className = 'ArticleCategory';
		$this->lang = true;
		$this->noLink = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('details');
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
        $this->fields_list = array(
            'id_article_category' => array(
                'title' => $this->l('ID de la catégorie'),
                'align' => 'center',
                'type' => 'int',
            ),
            'name' => array(
                'title' => $this->l('Nom de la catégorie'),
                'align' => 'center',
                'type' => 'text',
            ),
            'description' => array(
                'title' => $this->l('Description la catégorie'),
                'align' => 'center',
                'type' => 'text',
            ),  
            'active' => array(
                'title' => $this->l('Affiché'),
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => false
            ),
      /*      'position' => array('title' => $this->l('Position'),
                'filter_key' => 'position', 
                'align' => 'center', 
                'class' => 'fixed-width-sm', 
                'position' => 'position'),
    */
        );

        $this->list_no_link = false;

        $lists = parent::renderList();

        parent::initToolbar();

        return $lists;
    }	

	public function renderForm()
    {
        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
       
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Ajouter une catégorie'),
                'image' => '../img/admin/add.gif'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->l('Name:'),
                    'name' => 'name',
                    'required' => true,
                    'size' => 60,
                    'class' => 'copyNiceUrl'
                ),
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->l('Description:'),
                    'name' => 'description',
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
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Only letters and the hyphen (-) character are allowed.')
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

        return parent::renderForm();
    }


}

