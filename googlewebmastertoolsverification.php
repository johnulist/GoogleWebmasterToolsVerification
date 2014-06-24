<?php

/*
 * Google Webmaster Tools Verification
 *
 * @author LBAB <contact@lbab.fr>
 * @copyright Copyright (c) 2014 LBAB.
 * @license GNU/LGPL version 3
 * @version 1.0.0
 * @link www.lbab.fr
 */

if (!defined('_PS_VERSION_'))
    exit;
	
class googlewebmastertoolsverification extends Module
{
	public function __construct()
	{
	    $this->bootstrap = true;
	    
		$this->name = 'googlewebmastertoolsverification';
		$this->tab = 'seo';
		$this->version = '1.0.0';
		$this->author = 'LBAB';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');

		parent::__construct();

		$this->displayName = $this->l('Activate Google webmaster tools');
        $this->description = $this->l('Add Google webmaster tools verification code');

		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        
        if (!Configuration::get('lbabgooglewebmastertools_code'))
            $this->warning = $this->l('You must enter your code from the Google Webmaster Tools');

	}
	
	public function install()
	{
	    if (Shop::isFeatureActive())
	        Shop::setContext(Shop::CONTEXT_ALL);
	    
		return (parent::install() && 
		    $this->registerHook('displayHeader') 
		);
	}
	
	public function uninstall()
	{		
		return (parent::uninstall() && 
		    Configuration::deleteByName('lbabgooglewebmastertools_code')
		);
	}
	
	private function _displayInfos()
	{
	    $this->context->smarty->assign(
	        array(
	            'moduleName' => $this->displayName,
	            'description' => $this->description,
	            'version' => $this->version
	        )
	    );
	    
	    return $this->display(__FILE__, 'infos.tpl');
	}
	
	public function getContent()
	{
	    $output = '';
	    
	    if (Tools::isSubmit('submit'.$this->name)){
            $code = strval(Tools::getValue('lbabgooglewebmastertools_code'));

            if (!$code  || empty($code)) {
                $output .= $this->displayError( $this->l('Invalid code') );
            } else {
                Configuration::updateValue('lbabgooglewebmastertools_code', $code);

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
	    }
	    
	    $output .= $this->_displayInfos();
	    return $output.$this->renderForm();
	}
	
	public function renderForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	            'icon' => 'logo'
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Google code'),
	                'name' => 'lbabgooglewebmastertools_code',
	                'required' => false,
		            'desc' => $this->l('Copy the code from the Google Webmaster Tools verification page (choose html header method).'),
                    'class' => 'fixed-width-xl'
	            ),
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'btn btn-default pull-right'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	     
	    // Load current value
	    $helper->fields_value['lbabgooglewebmastertools_code'] = htmlentities(Configuration::get('lbabgooglewebmastertools_code'));
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookDisplayHeader($params)
	{
		return Configuration::get('lbabgooglewebmastertools_code')?Configuration::get('lbabgooglewebmastertools_code'):'';
	}
}
