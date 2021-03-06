<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ht_googlefont {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = 'footer_scripts';
      $this->title = CLICSHOPPING::getDef('module_header_tags_google_font_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_google_font_description');

      if ( defined('MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_GOOGLE_FONT_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $google = '<script src="https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js"></script>';
      $google .= '<script>WebFont.load({google: {families: [\'Roboto:300,300i,400,400i,700,700i\']}});</script>';

      $CLICSHOPPING_Template->addBlock($google . "\n", $this->group);
    }


    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous activer ce module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_FONT_SORT_ORDER',
          'configuration_value' => '50',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '25',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS',
                   'MODULE_HEADER_TAGS_GOOGLE_FONT_SORT_ORDER');
    }
  }

