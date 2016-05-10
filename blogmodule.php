<?php 

require_once(dirname(__FILE__).'/classes/ArticleCategory.php');
require_once(dirname(__FILE__).'/classes/ArticleComments.php');
require_once(dirname(__FILE__).'/classes/Article.php');

  

if (!defined('_PS_VERSION_'))
  exit;


class BlogModule extends Module
{

    public function __construct()
    {

        // Construction du module   

        $this->name = 'blogmodule'; // nom du module qui doit être le nom du dossier du module
        $this->tab = 'front_office_features'; // Dans quelle tabulation le module sera affiché
        $this->version = '1.0.0';
        $this->author = 'Jeremie Zipfel';

        

        $this->need_instance = 0; // 1 : le module est chargé sur la page "Modules" et permet d'afficher un message d'alerte si nécessaire
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);  // Versions de PS Compatibles
        $this->bootstrap = true; // Chargement de bootstrap ou non


        parent::__construct();


        $this->displayName = $this->l('Module Blog'); // Nom du module
        $this->description = $this->l("Ajoute une fonctionnalité blog en front-office, avec gestion en back-office des articles et des catégories d\'article"); // Description du module

        $this->confirmUninstall = $this->l('Voulez-vous vraiment désinstaller le module Blog ?'); // Message d'alerte en cas de désinstallation

        // Vous pouvez ajouter une icone PNG en 32x32px que vous appelerez logo.png

    }

    public function install()
    {

        if (!parent::install() ||
            !$this->installSql() ||
            !$this->installTab(0,'AdminBlog' ,$this->l('Blog Module')) ||
            !$this->installTab('AdminBlog','AdminBlogCategoryTable' ,$this->l('Liste des catégories')) ||
            !$this->installTab('AdminBlog','AdminBlogArticleTable' ,$this->l('Liste des articles')) ||
            !$this->installTab('AdminBlog','AdminBlogCommentsTable' ,$this->l('Liste des commentaires')) ||         
            !$this->registerHook('ActionAdminControllerSetMedia') ||
            !$this->registerHook('LeftColumnBlogFrontController') ||
            !$this->registerHook('CommentsArticleFrontController') ||
            !$this->registerHook('ModuleRoutes')
        )

        {return false;}

        else{

            // Creation du lien vers le blog, MERCI ALEXIS MESNARD POUR L'ASTUCE 
            require_once (_PS_ROOT_DIR_.'/modules/blocktopmenu/menutoplinks.class.php');
            $link=$this->context->link->getModuleLink('blogmodule', 'display', array());

                foreach (Language::getLanguages(true) as $lang){
                    $links[$lang['id_lang']]=$link;
                    $labels[$lang['id_lang']]=$this->l('Blog');
                }

            MenuTopLinks::add($links, $labels, 0, 1);
            // A defaut d'obtenir directement l'id ...
            $menulinks = MenuTopLinks::gets($this->context->language->id, null, $this->context->shop->id);
            $menulink = end($menulinks);

            // Assignation du lien au top menu
            $menuItems = Configuration::get('MOD_BLOCKTOPMENU_ITEMS');
            $menuItems_arr = explode(',', $menuItems);
            $menuItems_arr[]= 'LNK'.$menulink['id_linksmenutop'];
            $menuItems = implode(',', $menuItems_arr);
            Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $menuItems);

            // Creation des options du module
            Configuration::updateValue('MB_ID_TOPMENU', $menulink['id_linksmenutop']);

            }

         return true;

    }

    public function uninstall() 
    {

        if(!parent::uninstall() ||

            !$this->uninstallSql() ||
            !$this->uninstallTab('AdminBlog') ||
            !$this->uninstallTab('AdminBlogCategoryTable') ||
            !$this->uninstallTab('AdminBlogCommentsTable') ||
            !$this->uninstallTab('AdminBlogArticleTable')

        ) {return false;}
        else {
            //suppression link  MERCI ALEXIS MESNARD POUR L'ASTUCE 
            $id_link = Configuration::get('MB_ID_TOPMENU');
            require_once (_PS_ROOT_DIR_.'/modules/blocktopmenu/menutoplinks.class.php');
            MenuTopLinks::remove($id_link, $this->context->shop->id);

            // Suppression du lien du menu
            $menuItems = Configuration::get('MOD_BLOCKTOPMENU_ITEMS');
            $menuItems_arr = explode(',', $menuItems);
            $key = array_search('LNK'.$id_link, $menuItems_arr);
            if($key){
                unset($menuItems_arr[$key]);
                $menuItems = implode(',', $menuItems_arr);
                Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $menuItems);
            }
            // Suppression des options du module
            Configuration::deleteByName('MB_ID_TOPMENU');
        }
        return true;

    }

    private function installSql()
    {
        include(dirname(__FILE__).'/sql/install.php');
        $result = true;
        foreach ($sql_requests as $request){
            if (!empty($request))
            $result &= Db::getInstance()->execute(trim($request));

        }

        return $result;
    }

    private function uninstallSql()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        $result = true;

        foreach ($sql_requests as $request){
            if (!empty($request))
            $result &= Db::getInstance()->execute(trim($request));
        }

        return $result;
    }

    private function installTab($parent, $class_name, $name)
    {

        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->class_name = $class_name;
        $tab->module = $this->name;

        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang){
            $tab->name[$lang['id_lang']] = $name;
        }

        return $tab->save();
    }

    private function uninstallTab($class_name)
    {
        $idTab = (int)Tab::getIdFromClassName($class_name);
        $tab = new Tab($idTab);
        return $tab->delete();
    }   

    public function getContent()
    {
        if (Tools::isSubmit('submitModule'))
        {
            Configuration::updateValue('NBR_ARTICLE', Tools::getValue('nbr_article',''));
            Configuration::updateValue('ACTIVE_COMMENT_ARTICLE', Tools::getValue('active_comment_article',''));
        }
        return $this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration du module blog'),
                ),
            'input' => array(
                array(

                        'type'     => 'text',                             // This is a regular <input> tag.
                        'label'    => $this->l('Nombre articles à afficher par page'),                   // The <label> for this <input> tag.
                        'name'     => 'nbr_article',                             // The content of the 'id' attribute of the <input> tag.
                        'id'       => 'nbr_article',
                        'class'    => 'lg',                                // The content of the 'class' attribute of the <input> tag. To set the size of the element, use these: sm, md, lg, xl, or xxl.
                    ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Activer les commentaires articles'),
                    'name' => 'active_comment_article',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_comment_article',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_comment_article',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table =  $this->table;

        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;

        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;

        $helper->submit_action = 'submitModule';
        
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));

    }

    public function getConfigFieldsValues()
    {
        return array(
            'nbr_article' => Tools::getValue('nbr_article', Configuration::get('NBR_ARTICLE')), 
            'active_comment_article' => Tools::getValue('active_comment_article', Configuration::get('ACTIVE_COMMENT_ARTICLE')), 
            );
    }

    public function hookActionAdminControllerSetMedia()
    {

        $this->context->controller->addCSS(($this->_path).'views/css/blogmodule.css', 'all');
        $this->context->controller->addJS(($this->_path).'views/js/blogmodule.js', 'all');

    }

    /**
     * Specific hook column left in page blog
     */
    public function hookLeftColumnBlogFrontController()
    {

        $list_category = ArticleCategory::getAllCategoriesActiveOrNot((int)1, (int)$this->context->language->id);
        $this->context->smarty->assign('categories',  $list_category);

        return $this->display(__FILE__, 'views/templates/hook/leftcolumnblog.tpl');

    }

    /**
     * Specifics hook comments article
     */
    public function hookCommentsArticleFrontController()
    {
        // SI DANS CONFIG ON A ACTIVER LES COMMENTAIRES
        if(Configuration::get('ACTIVE_COMMENT_ARTICLE') == true)
        {
            $comments = ArticleComments::getAllCommentsById(Tools::getValue('id_article'));
            $this->context->smarty->assign('comments',  $comments);

            // SI SUBMIT FORMULAIRE 
            if(Tools::isSubmit('content')&& $this->context->customer->isLogged()){
                // si il manque l'un des champs erreur sinon c'est bon
                if (!Tools::getValue('title') || !Validate::isName(Tools::getValue('title'))) {
                    $this->errors[] = $this->l('Veuillez mentionner un champs titre valide.');               
               }
               if(!Tools::getValue('content') || !Validate::isMessage(Tools::getValue('content'))){
                    $this->errors[] = $this->l('Veuillez mentionner un contenu valide.');                           
               }
                if (!isset($this->errors)) {
                    ArticleComments::setComments(
                        (int)Tools::getValue('id_article'),
                        (int)$this->context->customer->id,
                        Tools::getValue('title'),
                        Tools::getValue('content')
                    );
                    // FORCE L'ACTUALISATION DES COMMENTAIRES avec un refresh
                    // PAS TRES PROPRE MAIS FONCTIONNE
                    header("Refresh:0");
                }
                else{
                    $this->context->smarty->assign(array(
                    'errors' => $this->errors,
                    )); 

                }
            }

            return $this->display(__FILE__, 'views/templates/hook/commentsarticleblog.tpl');            
        }

    }

    public function hookModuleRoutes($params)
    {

        $slug = 'blogmodule';

        $routes = array(
            'module-blogmodule-display' => array(
                'controller' => 'display',
                'rule' =>       $slug,
                'keywords' => array(

                ),
                'params' => array(

                    'fc' => 'module',
                    'module' => 'blogmodule',

                ),
            ),

            'blogmodule_list' => array(
                'controller' => 'display',
                'rule' => $slug.'/category',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blogmodule',
                ),
            ),

            'blogmodule_list_pagination' => array(
                'controller' => 'display',
                'rule' =>       $slug.'/category/page/{page}',
                'keywords' => array(
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),

                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blogmodule',
                ),
            ),

            'blogmodule_pagination' => array(
                'controller' => 'display',
                'rule' =>       $slug.'/page/{page}',
                'keywords' => array(
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(

                    'fc' => 'module',
                    'module' => 'blogmodule',
                ),
            ),

            'blogmodule_category' => array(
                'controller' => 'display',
                'rule' =>        $slug.'/category/{id_article_category}',
                'keywords' => array(
                    'id_article_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_article_category'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blogmodule',
                ),

            ),

            'blogmodule_category_pagination' => array(
                'controller' => 'display',
                'rule' =>       $slug.'/category/{id_article_category}/page/{page}',
                'keywords' => array(
                    'id_article_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_article_category'),
                    'page' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),

                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blogmodule',

                ),
            ),

            'blogmodule_post' => array(
                'controller' => 'display',
                'rule' =>       $slug.'/{id_article}-{link_rewrite}',
                'keywords' => array(
                    'id_article' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_article'),
                    'link_rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'link_rewrite'),
                ),

                'params' => array(
                    'fc' => 'module',
                    'module' => 'blogmodule',
                ),

            ),



        );

    return $routes;

    }
 

}