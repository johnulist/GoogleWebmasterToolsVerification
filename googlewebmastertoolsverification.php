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
    public $code;

	public function __construct()
	{
	    $this->bootstrap = true;
	    
		$this->name = 'googlewebmastertoolsverification';
		$this->tab = 'seo';
		$this->version = '1.0.0';
		$this->author = 'LBAB';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');

        $this->code = Configuration::get('LBAB_GOOGLEWEBMASTERTOOLS_CODE');
		parent::__construct();
        
		$this->displayName = $this->l('Google webmaster tools Verification');
        $this->description = $this->l('Add Google webmaster tools verification code');

		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        
        if (!isset($this->code) || empty($this->code))
            $this->warning = $this->l('You must enter your code from the Google Webmaster Tools');
	}
	
	public function install()
	{
	    if (Shop::isFeatureActive())
	        Shop::setContext(Shop::CONTEXT_ALL);
	    
		return (parent::install() && 
            Configuration::updateValue('LBAB_GOOGLEWEBMASTERTOOLS_CODE', '') &&
		    $this->registerHook('displayHeader') 
		);
	}
	
	public function uninstall()
	{		
		return (parent::uninstall() && 
		    Configuration::deleteByName('LBAB_GOOGLEWEBMASTERTOOLS_CODE')
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
            $code = Tools::getValue('code');

            if (!$code  || empty($code)) {
                $output .= $this->displayError( $this->l('Invalid code') );
            } else {
                Configuration::updateValue('LBAB_GOOGLEWEBMASTERTOOLS_CODE', $code, true);

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
	                'name' => 'code',
	                'required' => true,
		            'desc' => $this->l('Copy the code from the Google Webmaster Tools verification page (choose html header method).').'<br>  Ex : &lt;meta name="google-site-verification" content="fUhaGiNjck4SDoyzO5K3ur0tq8yA8iCU6Hz65Ug9r88" /&gt;',
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
	    $helper->fields_value['code'] = Configuration::get('LBAB_GOOGLEWEBMASTERTOOLS_CODE');
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookDisplayHeader($params)
	{
		return Configuration::get('LBAB_GOOGLEWEBMASTERTOOLS_CODE')?Configuration::get('LBAB_GOOGLEWEBMASTERTOOLS_CODE'):'';
	}
}
