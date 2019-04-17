<?php
/**
 * This software is provided "as is" without warranty of any kind.
 *
 * Visit my website (http://prestacraft.com) for future updates, new articles and other awesome modules.
 *
 * @author     PrestaCraft
 * @copyright  PrestaCraft
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$require = array(
    'classes/AgeVerificationDb.php',
);

foreach ($require as $item) {
    require_once(_PS_MODULE_DIR_.'ageverification/'.$item);
}

class Ageverification extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ageverification';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'PrestaCraft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Age verification');
        $this->description = $this->l('User has to choose his birthdate in order to access Your store.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }


    public function install()
    {
        // Initial values
        Configuration::updateValue("AGEVERIFICATION_AGE", 18);
        Configuration::updateValue("AGEVERIFICATION_TYPE", "date");
        Configuration::updateValue("AGEVERIFICATION_OPACITY", 100);
        Configuration::updateValue("AGEVERIFICATION_BG_COLOR", "#2b2e38");
        Configuration::updateValue("AGEVERIFICATION_VALIDATION_MODE", "live");
        Configuration::updateValue("AGEVERIFICATION_BG_POPUP_COLOR", "#ffffff");

        if (!Configuration::hasKey('AGEVERIFICATION_CAT_RESTRICTION')) {
            Configuration::updateValue("AGEVERIFICATION_CAT_RESTRICTION", false);
        }
		
		if (!Configuration::hasKey('AGEVERIFICATION_PROD_RESTRICTION')) {
            Configuration::updateValue("AGEVERIFICATION_PROD_RESTRICTION", false);
        }

        return parent::install() && $this->registerHook('displayFooter') && $this->registerHook('header');
    }

    public function uninstall()
    {
        $this->context->cookie->__set('ageverification_validated', false);

        return parent::uninstall();
    }

    public function hookDisplayFooter()
    {
        require_once(_PS_MODULE_DIR_ . 'ageverification/classes/AgeVerificationDb.php');

        $restrictCategory = (bool)Configuration::get('AGEVERIFICATION_CAT_RESTRICTION');
        $restrictProduct = (bool)Configuration::get('AGEVERIFICATION_PROD_RESTRICTION');
        $showPopup = false;

        if ($restrictProduct && $restrictCategory) {
            if ($this->categoryFound() || $this->productFound()) { // Enter category or product page
                $showPopup = true;
            }
        } else {
            if ($restrictCategory) {
                $showPopup = $this->categoryFound() ? true : false;
            } else {
                if ($restrictProduct) {
                    $showPopup = $this->productFound() ? true : false;
                } else {
                    if ((bool)$this->context->cookie->__get('ageverification_validated') === false) {
                        $showPopup = true;
                    }
                }
            }
        }


        if ($showPopup) {
            $this->context->smarty->assign(
                'title', Configuration::get("AGEVERIFICATION_TITLE_".$this->context->language->id)
            );
            $this->context->smarty->assign(
                'text', Configuration::get("AGEVERIFICATION_CONTENT_".$this->context->language->id)
            );
            $this->context->smarty->assign(
                'button_text', Configuration::get("AGEVERIFICATION_BUTTON_".$this->context->language->id)
            );
            $this->context->smarty->assign(
                'type', Configuration::get("AGEVERIFICATION_TYPE")
            );
            $this->context->smarty->assign(
                'age', Configuration::get("AGEVERIFICATION_AGE")
            );
            $this->context->smarty->assign(
                'age_text', Configuration::get("AGEVERIFICATION_BIRTH_".$this->context->language->id)
            );
            $this->context->smarty->assign(
                'mode', Configuration::get("AGEVERIFICATION_VALIDATION_MODE")
            );
            $this->context->smarty->assign(
                'bg_color', Configuration::get("AGEVERIFICATION_BG_POPUP_COLOR")
            );
            $this->context->smarty->assign(
                'font_color', Configuration::get("AGEVERIFICATION_FONT_COLOR")
            );
            $this->context->smarty->assign(
                'selected_color', Configuration::get("AGEVERIFICATION_SELECTED_FONT_COLOR")
            );
            $this->context->smarty->assign(
                'selected_bg', Configuration::get("AGEVERIFICATION_SELECTED_BG_COLOR")
            );

            $this->context->smarty->assign(
                'header_font', Configuration::get("AGEVERIFICATION_HEADER_FONT")
            );

            $this->context->smarty->assign(
                'header_size', Configuration::get("AGEVERIFICATION_HEADER_SIZE")
            );

            $this->context->smarty->assign(
                'header_size_mobile', Configuration::get("AGEVERIFICATION_HEADER_SIZE_MOBILE")
            );

            $this->context->smarty->assign(
                'content_font', Configuration::get("AGEVERIFICATION_FONT")
            );

            $this->context->smarty->assign(
                'content_size', Configuration::get("AGEVERIFICATION_FONT_SIZE")
            );

            $this->context->smarty->assign(
                'content_size_mobile', Configuration::get("AGEVERIFICATION_FONT_SIZE_MOBILE")
            );

            $this->context->smarty->assign('ps_version', Ageverification::getVersion());

            $hex = Configuration::get("AGEVERIFICATION_BG_COLOR");
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

            $opa = (int)Configuration::get("AGEVERIFICATION_OPACITY");

            $this->context->smarty->assign('color_r', $r);
            $this->context->smarty->assign('color_g', $g);
            $this->context->smarty->assign('color_b', $b);
            $this->context->smarty->assign('opacity', (float)$opa/100);

            return $this->display(__FILE__, 'av.tpl');
        }
    }

    public function hookHeader()
    {

        if (Ageverification::getVersion() != "1.6") {
            // Remodal
            $this->context->controller->registerStylesheet(
                'av-remodal', 'modules/'.$this->name.'/views/css/remodal.css'
            );
            $this->context->controller->registerStylesheet(
                'av-remodal-theme', 'modules/'.$this->name.'/views/css/remodal-default-theme.css'
            );
            $this->context->controller->registerJavascript('av-remodal', 'modules/'.$this->name.'/views/js/remodal.js');

            // Bootstrap select
            $this->context->controller->registerStylesheet(
                'av-bootstrap-select', 'modules/'.$this->name.'/views/css/bootstrap-select.min.css'
            );

            $this->context->controller->registerJavascript(
                'av-bootstrap-select', 'modules/'.$this->name.'/views/js/bootstrap-select.min.js',
                array('priority' => '999')
            );

            // Custom css
            $this->context->controller->registerStylesheet('av-av', 'modules/'.$this->name.'/views/css/av.css');

            $this->context->controller->registerStylesheet(
                'av-bootstrap', 'modules/'.$this->name.'/views/css/bootstrap-dropdowns.css'
            );

            // Custom js
            $this->context->controller->registerJavascript(
                'av-functions', 'modules/'.$this->name.'/views/js/av-functions-17.js'
            );
        } else {
            // Remodal
            $this->context->controller->addCSS(($this->_path).'/views/css/remodal.css', 'all');
            $this->context->controller->addCSS(($this->_path).'/views/css/remodal-default-theme.css', 'all');
            $this->context->controller->addJS(($this->_path).'/views/js/remodal.js');

            // Bootstrap select
            $this->context->controller->addCSS(($this->_path).'/views/css/bootstrap-select.min.css', 'all');
            $this->context->controller->addJS(($this->_path).'/views/js/bootstrap-select.min.js', 'all');

            // Bootstrap
            $this->context->controller->addCSS(($this->_path).'/views/css/bootstrap-dropdowns.css', 'all');

            // Custom css
            $this->context->controller->addCSS(($this->_path).'/views/css/av.css', 'all');

            // Custom js
            $this->context->controller->addJS(($this->_path).'/views/js/av-functions.js');
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitAgeverificationModule')) == true || isset($_POST["deleterows"])) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAgeverificationModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $data = file_get_contents('http://prestacraft.com/version_checker.php?module='.$this->name.'&version='.$this->version.'');

        $html = '<style> #firstline, #secondline, #thirdline, #fourthline, #fifthline { display: none; } 
                .mColorPicker { width: 100px !important; } </style>
                <div class="panel" style="text-align: center;">
                 <h3 style="text-align: center;">'.$this->l('Version checker').'</h3>
        '.$data.'
                </div>';

        return $html.$helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $base = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => '<i class="icon-wrench" style="color: #2eacce;"></i><span style="font-size: 14px;
                                    color: #2eacce;font-weight:bold;">
                                    &nbsp;&nbsp;'.$this->l("Settings").'</span>',
                        'name' => 'firstline',
                    ),
                    array(
                        'type'      => 'radio',
                        'label'     => $this->l('Age verification type'),
                        'name'      => 'AGEVERIFICATION_TYPE',
                        'required'  => true,
                        'values'    => array(
                            array(
                                'id'    => 'date',
                                'value' => 'date',
                                'label' => $this->l('1# Select Day/Month/Year of birth')
                            ),
                            array(
                                'id'    => 'accept',
                                'value' => 'accept',
                                'label' => $this->l('2# Simple acceptation via button')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'AGEVERIFICATION_AGE',
                        'label' => $this->l('Min. age required'),
                        'desc' => $this->l('This will take effect only for 1# verification type'),
                    ),
                    array(
                        'type'      => 'radio',
                        'label'     => $this->l('Validation mode'),
                        'name'      => 'AGEVERIFICATION_VALIDATION_MODE',
                        'desc'      => $this->l(
                            'Works only if "Age verification type" - "1# Select Day/Month/Year of birth" is selected'
                        ),
                        'required'  => true,
                        'values'    => array(
                            array(
                                'id'    => 'live',
                                'value' => 'live',
                                'label' => $this->l('Live mode : validation right after change of any part of date
                                 (day, month or year)')
                            ),
                            array(
                                'id'    => 'classic',
                                'value' => 'classic',
                                'label' => $this->l('Classic mode : validation after confirmation button click
                                ')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => '<i class="icon-folder" style="color: #2eacce;"></i><span style="font-size: 14px;
                        color: #2eacce;font-weight:bold;">
                        &nbsp;&nbsp;'.$this->l("Categories & Products").'</span>',
            'name' => 'fifthline',
        );

        $selected_cat = explode(',', Configuration::get("AGEVERIFICATION_CATEGORIES"));

        $base['form']['input'][] = array(
            'type'  => 'switch',
            'label' => $this->l('Enable category restriction?'),
            'desc' => $this->l('If you enable this option - popup will show only in categories selected below. If you disable - popup will show everywhere.'),
            'name'  => 'AGEVERIFICATION_CAT_RESTRICTION',
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
        );

        $base['form']['input'][] = array(
            'type'  => 'categories',
            'label' => $this->l('Select categories'),
            'name'  => 'AGEVERIFICATION_CATEGORIES',
            'tree'  => array(
                'id'                  => 'categories-tree',
                'root_category'       => $this->context->shop->getCategory(),
                'selected_categories' => $selected_cat,
                'use_search'          => true,
                'use_checkbox'        => true
            )
        );

        $base['form']['input'][] = array(
            'type'  => 'switch',
            'label' => $this->l('Display on product pages?'),
            'desc' => $this->l('If you enable this option - popup will show also on the product pages belonging to selected categories and their children.'),
            'name'  => 'AGEVERIFICATION_PROD_RESTRICTION',
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
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => '<i class="icon-desktop" style="color: #2eacce;"></i><span style="font-size: 14px;
                        color: #2eacce;font-weight:bold;">
                        &nbsp;&nbsp;'.$this->l("Appearance").'</span>',
            'name' => 'secondline',
        );

        $list = array();

        for ($i = 80; $i <= 100; $i++) {
            $list[] = array (
                'id' => $i,
                'name' => $i.'%'
            );
        }

        $base['form']['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Popup outside: opacity percentage'),
            'name' => 'AGEVERIFICATION_OPACITY',
            'options' => array(
                'query' => $list,
                'id' => 'id',
                'name' => 'name',
            ),
            'col' => '3',
            'desc' => $this->l("100% if you want to completely hide Your page content until age acceptation. 
            80% if you want to leave your content a bit visible.")
        );

        $base['form']['input'][] = array(
            'type' => 'color',
            'label' => $this->l('Popup outside: background color'),
            'name' => 'AGEVERIFICATION_BG_COLOR',
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'color',
            'label' => $this->l('Popup: background color'),
            'name' => 'AGEVERIFICATION_BG_POPUP_COLOR',
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'color',
            'label' => $this->l('Font color'),
            'name' => 'AGEVERIFICATION_FONT_COLOR',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'color',
            'label' => $this->l('Active selection: font color'),
            'name' => 'AGEVERIFICATION_SELECTED_FONT_COLOR',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'color',
            'label' => $this->l('Active selection: background color'),
            'name' => 'AGEVERIFICATION_SELECTED_BG_COLOR',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => '<i class="icon-pencil" style="color: #2eacce;"></i><span style="font-size: 14px;
                        color: #2eacce;font-weight:bold;">
                        &nbsp;&nbsp;'.$this->l("Typography").'</span>',
            'name' => 'fourthline',
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Header: Font family'),
            'name' => 'AGEVERIFICATION_HEADER_FONT',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3',
            'hint' => $this->l("If you want to set your font, make sure your font is uploaded to your server and works with CSS")
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Header: Font size'),
            'name' => 'AGEVERIFICATION_HEADER_SIZE',
            'suffix' => 'px',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Header: Font size (mobile devices)'),
            'name' => 'AGEVERIFICATION_HEADER_SIZE_MOBILE',
            'suffix' => 'px',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Content: Font family'),
            'name' => 'AGEVERIFICATION_FONT',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3',
            'hint' => $this->l("If you want to set your font, make sure your font is uploaded to your server and works with CSS")
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Content: Font size'),
            'name' => 'AGEVERIFICATION_FONT_SIZE',
            'suffix' => 'px',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Content: Font size (mobile devices)'),
            'name' => 'AGEVERIFICATION_FONT_SIZE_MOBILE',
            'suffix' => 'px',
            'desc' => $this->l('Leave empty to use default value'),
            'col' => '3'
        );

        $base['form']['input'][] = array(
            'type' => 'text',
            'label' => '<i class="icon-globe" style="color: #2eacce;"></i><span style="font-size: 14px;
                        color: #2eacce;font-weight:bold;">
                        &nbsp;&nbsp;'.$this->l("Languages").'</span>',
            'name' => 'thirdline',
        );

        $languages = Language::getLanguages(true);
        $cnt = 0;
        foreach ($languages as $lang) {
            $cnt = $cnt+1;
            $langid = $lang['id_lang'];
            $base['form']['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Popup title for').' <strong>'.$lang['name'].'</strong>:',
                'name' => 'AGEVERIFICATION_TITLE_'.$langid.'',
                'col' => '3'
            );
            $base['form']['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Confirmation button text for').' <strong>'.$lang['name'].'</strong>:',
                'name' => 'AGEVERIFICATION_BUTTON_'.$langid.'',
                'col' => '3'
            );
            $base['form']['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Text when entered birthday results with too low age for').' 
                <strong>'.$lang['name'].'</strong>:',
                'name' => 'AGEVERIFICATION_BIRTH_'.$langid.'',
                'col' => '3'
            );
            $base['form']['input'][] = array(

                'type' => 'textarea',
                'label' => $this->l('Content for').' <strong>'.$lang['name'].'</strong>:',
                'name' => 'AGEVERIFICATION_CONTENT_'.$langid.'',
                'cols' => 10,
                'rows' => 3,
                'col' => 3
            );
        }

        return $base;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $values = array();
        $cnt = 0;

        // Lang values
        foreach ($languages as $lang) {
            $cnt = $cnt + 1;
            $values['AGEVERIFICATION_CONTENT_'.$lang['id_lang']] =
                Configuration::get('AGEVERIFICATION_CONTENT_'.$lang['id_lang']);
            $values['AGEVERIFICATION_TITLE_'.$lang['id_lang']] =
                Configuration::get('AGEVERIFICATION_TITLE_'.$lang['id_lang']);
            $values['AGEVERIFICATION_BIRTH_'.$lang['id_lang']] =
                Configuration::get('AGEVERIFICATION_BIRTH_'.$lang['id_lang']);
            $values['AGEVERIFICATION_BUTTON_'.$lang['id_lang']] =
                Configuration::get('AGEVERIFICATION_BUTTON_'.$lang['id_lang']);
        }

        // Global values
        $values2 = array(
            'firstline' => 'by prestacraft',
            'secondline' => 'by prestacraft',
            'thirdline' => 'by prestacraft',
            'fourthline' => 'by prestacraft',
            'fifthline' => 'by prestacraft',
            'AGEVERIFICATION_PROD_RESTRICTION' => Configuration::get('AGEVERIFICATION_PROD_RESTRICTION'),
            'AGEVERIFICATION_CAT_RESTRICTION' => Configuration::get('AGEVERIFICATION_CAT_RESTRICTION'),
            'AGEVERIFICATION_CATEGORIES' => Configuration::get('AGEVERIFICATION_CATEGORIES'),
            'AGEVERIFICATION_AGE' => Configuration::get('AGEVERIFICATION_AGE'),
            'AGEVERIFICATION_TYPE' => Configuration::get('AGEVERIFICATION_TYPE'),
            'AGEVERIFICATION_VALIDATION_MODE' => Configuration::get('AGEVERIFICATION_VALIDATION_MODE'),
            'AGEVERIFICATION_OPACITY' => Configuration::get('AGEVERIFICATION_OPACITY'),
            'AGEVERIFICATION_BG_COLOR' => Configuration::get('AGEVERIFICATION_BG_COLOR'),
            'AGEVERIFICATION_HEADER_FONT' => Configuration::get('AGEVERIFICATION_HEADER_FONT'),
            'AGEVERIFICATION_HEADER_SIZE' => Configuration::get('AGEVERIFICATION_HEADER_SIZE'),
            'AGEVERIFICATION_HEADER_SIZE_MOBILE' => Configuration::get('AGEVERIFICATION_HEADER_SIZE_MOBILE'),
            'AGEVERIFICATION_FONT' => Configuration::get('AGEVERIFICATION_FONT'),
            'AGEVERIFICATION_FONT_SIZE' => Configuration::get('AGEVERIFICATION_FONT_SIZE'),
            'AGEVERIFICATION_FONT_SIZE_MOBILE' => Configuration::get('AGEVERIFICATION_FONT_SIZE_MOBILE'),
            'AGEVERIFICATION_BG_POPUP' => Configuration::get('AGEVERIFICATION_BG_POPUP'),
            'AGEVERIFICATION_BG_POPUP_COLOR' => Configuration::get('AGEVERIFICATION_BG_POPUP_COLOR'),
            'AGEVERIFICATION_FONT_COLOR' => Configuration::get('AGEVERIFICATION_FONT_COLOR'),
            'AGEVERIFICATION_SELECTED_FONT_COLOR' => Configuration::get('AGEVERIFICATION_SELECTED_FONT_COLOR'),
            'AGEVERIFICATION_SELECTED_BG_COLOR' => Configuration::get('AGEVERIFICATION_SELECTED_BG_COLOR'),
        );

        return array_merge($values, $values2);
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $notAllowed = array(
            'firstline', 'secondline', 'thirdline', 'fourthline', 'fifthline'
        );

        foreach (array_keys($form_values) as $key) {
            if ($key == 'AGEVERIFICATION_CATEGORIES') {
                Configuration::updateValue($key, implode(',', Tools::getValue($key)));
            } else {
                if (!in_array($key, $notAllowed)) {
                    Configuration::updateValue($key, Tools::getValue($key));
                }
            }
        }

        $languages = Language::getLanguages(true);

        foreach ($languages as $lang) {
            $langid = $lang['id_lang'];
            Configuration::updateValue(
                "AGEVERIFICATION_CONTENT_{$langid}",
                Tools::getValue("AGEVERIFICATION_CONTENT_{$langid}"), 
				true
            );
            Configuration::updateValue(
                "AGEVERIFICATION_TITLE_{$langid}",
                Tools::getValue("AGEVERIFICATION_TITLE_{$langid}"), 
				true
            );
            Configuration::updateValue(
                "AGEVERIFICATION_BIRTH_{$langid}",
                Tools::getValue("AGEVERIFICATION_BIRTH_{$langid}"), 
				true
            );
            Configuration::updateValue(
                "AGEVERIFICATION_BUTTON_{$langid}",
                Tools::getValue("AGEVERIFICATION_BUTTON_{$langid}"), 
				true
            );
        }
    }

    public static function getVersion()
    {
        return Tools::substr(_PS_VERSION_, 0, 3);
    }

    private function categoryFound()
    {
        $categoryArray = explode(',',Configuration::get('AGEVERIFICATION_CATEGORIES'));

        if (Tools::getValue("controller") == 'category') {
            if (in_array(Tools::getValue("id_category"), $categoryArray) &&
                (bool)$this->context->cookie->__get('ageverification_validated') === false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function productFound()
    {
        $categoryArray = explode(',',Configuration::get('AGEVERIFICATION_CATEGORIES'));
        $productCategoriesArray = array();

        if (Tools::getValue("controller") == 'product') {
            $productCategories = Db::getInstance()->executeS(
                'SELECT id_category FROM '._DB_PREFIX_.'category_product 
                        WHERE id_product='.(int)Tools::getValue("id_product").''
            );

            if (count($productCategories) > 0) {
                foreach ($productCategories as $item) {
                    $productCategoriesArray[] = $item['id_category'];
                }
            }

            if ((bool)$this->context->cookie->__get('ageverification_validated') === false) {
                if (count($productCategoriesArray) > 0) {
                    foreach ($productCategoriesArray as $item) {
                        $cat = new Category($item);
                        $categories = $cat->getAllParents();
                        if (in_array($item, $categoryArray)) {
                            return true;
                        }
                        foreach ($categories as $cat) {
                            if (in_array($cat, $categoryArray)) {
                                return true;
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
