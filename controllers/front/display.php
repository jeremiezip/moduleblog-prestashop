<?php
class blogmoduledisplayModuleFrontController extends ModuleFrontController
{

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
	public function initContent()
	{
		// PAR DEFAUT DESACTIVER COLONNE DE GAUCHE ET DE DROITE, laisser place aux HOOKS CUSTOMS
  		$this->display_column_left = false;
  		$this->display_column_right = false;

		parent::initContent();
		// Si j'ai cliqué sur un article je l'affiche 
		if (Tools::getValue('id_article')) {
			$this ->viewArticle();
		}
		//sinon j'affiche la liste des articles en fonction de la page lors de l'initContent
		else {
			$this ->viewListArticle();
		}
	}

   	/**
     * Set default medias for this controller
     */
	public function setMedia()
	{
	    parent::setMedia();
	   	$this->addCSS(_MODULE_DIR_.'blogmodule/views/css/blogmodule.css', 'all');
	   	$this->addJS(_MODULE_DIR_.'blogmodule/views/js/blogmodule.js', 'all');

	}

	 /**
     * Set view list articles
     */
	public function viewListArticle(){

		$article = new Article();

		//par defaut la page est fixé à un 
		(!Tools::getValue('page')) ? $page = 1 : $page = Tools::getValue('page');

		// si la catégorie n'existe pas on la met à NULL, 
		//sinon ça veut dire que dans l'url rewrite on a cliqué sur une catégorie
		if(!Tools::getValue('id_article_category')){
		 	$id_article_category = NULL;
			$getCategory = FALSE;	
		}
		 else{
		 	$getCategory = ArticleCategory::getCategoryById(Tools::getValue('id_article_category'), (int)$this->context->language->id, (int)$this->context->shop->id);
			$id_article_category = Tools::getValue('id_article_category');	
		 } 	

	    // on récupere la liste des articles, params: num de page, id_lang,id_shop et id_article_category
		$list_articles = $article->getArticlesByPage((int)$page, (int)$this->context->language->id, (int)$this->context->shop->id, $id_article_category);
	   	
	   	// ON ASSIGNE LE NOMBRE DE PAGE POUR LA PAGINATION
	   	(Configuration::get('NBR_ARTICLE')) ? $messagesParPage = Configuration::get('NBR_ARTICLE') : $messagesParPage = 10;
	   	
	   	$this->context->smarty->assign(array(
	   		'articles' => $list_articles,
	   		'displayTitleCategory' => $getCategory,
	   		'nbr_page' => $article->getTotalNbrPages((int)$messagesParPage,$id_article_category),
			'meta_title'=> mb_convert_encoding($this->module->l('Liste des articles'), "UTF-8"),
			'page_actuel' => (int)$page,
	   		));

	   	// SI LA META TITLE EXISTE on les assigne
	   	if ($getCategory){
		   	$this->context->smarty->assign(array(
				'meta_title'=> mb_convert_encoding($getCategory['meta_title'], "UTF-8"),
				'meta_keywords'=> mb_convert_encoding($getCategory['meta_keywords'], "UTF-8"),
				'meta_description'=> mb_convert_encoding($getCategory['meta_description'], "UTF-8")
		   		));	   		
	   	}
	   	//si ajax existe
		if(Tools::getValue('ajax')){
		 echo ($this->context->smarty->fetch(_PS_MODULE_DIR_ .'blogmodule/views/templates/front/blogmodule_list_articles.tpl'));
		}
		else {
			$this->setTemplate('blogmodule_list.tpl');			
		}
	}

	 /**
     * Set view article
     */
	public function viewArticle(){
		// affiche l'article par l'id, la langue, l'id shop 
		$article = Article::getArticleById((int)Tools::getValue('id_article'),(int)$this->context->language->id,(int)$this->context->shop->id); 
		
		//si il n'y a pas d'article on redirige ver la page principale
		if(empty($article)){
			header('Location: '.$this->context->link->getModuleLink('blogmodule', 'display').'');
			exit;
		}
		else {
			// ASSIGNE LES META DE L'ARTICLE DANS LE TPL
			$this->context->smarty->assign(array(
				'meta_title'=> mb_convert_encoding($article['meta_title'], "UTF-8"),
				'meta_keywords'=> mb_convert_encoding($article['meta_keywords'], "UTF-8"),
				'meta_description'=> mb_convert_encoding($article['meta_description'], "UTF-8")
			));
	   		

	   		$this->context->smarty->assign('article', $article);
			$this->setTemplate('blogmodule_article.tpl');	
		}		
	}
}