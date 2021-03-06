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

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Upload;

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ImageResample;

  class ProductsAttributesAdmin {
    protected $lang;
    protected $db;
    protected $app;

    public function __construct() {
      $this->lang = Registry::get('Language');
      $this->db = Registry::get('Db');
      $this->app = Registry::get('ProductsAttributes');
    }


/**
 * products options - attributes
 *
 * @param string $options_id
 * @return string $values_values['products_options_values_name'], the value of the option name
 * @access public
 */
    public function getOptionsName($options_id) {
      $Qoptions = Registry::get('Db')->get('products_options', 'products_options_name', ['products_options_id' => (int)$options_id,
                                                                                         'language_id' => (int)$this->lang->getId()
                                                                                        ]
                                          );

      return $Qoptions->value('products_options_name');

    }

/**
 * products options name - attributes
 *
 * @param string $values_id
 * @return string $values_values['products_options_values_name'], the name value of the option name
 * @access public
 */
    public function getValuesName($values_id) {
      $Qvalues = Registry::get('Db')->get('products_options_values', 'products_options_values_name', ['products_options_values_id' => (int)$values_id,
                                                                                                      'language_id' => (int)$this->lang->getId()
                                                                                                     ]
                                          );

      return $Qvalues->value('products_options_values_name');
    }

    public function UploadImage() {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      Registry::set('ImageResample', new ImageResample());
      $CLICSHOPPING_ImageResample = Registry::get('ImageResample');

      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      $dir_products_image = 'attributes_options/';


      $error = true;

// load originale image
      $image = new Upload('products_image_resize', $CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image, null, ['gif', 'jpg', 'png', 'jpeg']);

// When the image is updated

      if ($image->check() && $image->save()) {
        $error = false;
      }

      if ($error === false) {
        $sql_data_array['image'] = $dir_products_image . $image->getFilename();
      } else {
        $sql_data_array['image'] = '';
      }

      if ($image->check()) {
        $image_name = $image->getFilename();
        $CLICSHOPPING_ImageResample->load($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name);

        $CLICSHOPPING_ImageResample->resizeToWidth(50);

        $image_ext = 'opt';
        $rand_image = $CLICSHOPPING_ProductsAdmin->getGenerateRandomString();

        $image = $dir_products_image . $image_ext . '_' . $rand_image . '_' . $image_name;
        $image = str_replace(' ', '', $image);

        $CLICSHOPPING_ImageResample->save($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $image);

// delete the orginal files
        if (file_exists($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name)) {
          @unlink($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name);
        }

// Insertion images des produits via l'editeur FCKeditor (fonctionne sur les nouveaux produits et editions produits)
        $attribute_image_name = HTML::sanitize($image);
        $attribute_image_name = htmlspecialchars($attribute_image_name);
        $attribute_image_name = str_replace($CLICSHOPPING_Template->getDirectoryShopTemplateImages(), '', $attribute_image_name);
        $attribute_image_name_end = strstr($attribute_image_name, '&quot;');
        $attribute_image_name = str_replace($attribute_image_name_end, '', $attribute_image_name);
        $products_image_name = str_replace($CLICSHOPPING_Template->getDirectoryShopSources(), '', $attribute_image_name);
      }

      return $products_image_name;
    }

/**
 * Set attribut option type
 * @return array
 */
    public function setAttributeType() {
      $products_options_type =  [array('id' =>'select', 'text' => $this->app->getDef('text_select')),
                                 array('id' =>'radio', 'text' => $this->app->getDef('text_radio'))
                                ];

      return $products_options_type;
    }
 }