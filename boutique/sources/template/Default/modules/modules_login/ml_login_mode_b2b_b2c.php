<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ml_login_mode_b2b_b2c {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_login_mode_b2b_b2c');
      $this->description = CLICSHOPPING::getDef('module_login_mode_b2b_b2c_description');

      if (defined('MODULE_LOGIN_MODE_B2B_B2C_STATUS')) {
        $this->sort_order = MODULE_LOGIN_MODE_B2B_B2C_SORT_ORDER;
        $this->enabled = (MODULE_LOGIN_MODE_B2B_B2C_STATUS == 'True');
      }
     }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Account']) && isset($_GET['LogIn'])) {

        $content_width = (int)MODULE_LOGIN_MODE_B2B_B2C_CONTENT_WIDTH;

        if (MODE_MANAGEMENT_B2C_B2B == 'B2C_B2B' && MODE_B2B_B2C == 'true') {

          $login_mode_b2b_b2c = '<!-- login_mode_b2b_b2c start -->'. "\n";

          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/login_mode_b2b_b2c'));

          $login_mode_b2b_b2c .= ob_get_clean();

          $login_mode_b2b_b2c .= '<!-- login_mode_b2b_b2c  end-->' . "\n";

        } // end MODE_MANAGEMENT_B2C_B2B


        $CLICSHOPPING_Template->addBlock($login_mode_b2b_b2c, $this->group);
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_LOGIN_MODE_B2B_B2C_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_LOGIN_MODE_B2B_B2C_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULE_LOGIN_MODE_B2B_B2C_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do you want display the module ?',
          'configuration_key' => 'MODULE_LOGIN_MODE_B2B_B2C_POSITION',
          'configuration_value' => 'float-none',
          'configuration_description' => 'Select where you want display the module',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-right\', \'float-left\', \'float-none\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_LOGIN_MODE_B2B_B2C_SORT_ORDER',
          'configuration_value' => '10',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                                      ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                                    );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_LOGIN_MODE_B2B_B2C_STATUS',
        'MODULE_LOGIN_MODE_B2B_B2C_CONTENT_WIDTH',
        'MODULE_LOGIN_MODE_B2B_B2C_POSITION',
        'MODULE_LOGIN_MODE_B2B_B2C_SORT_ORDER'
      );
    }
  }
