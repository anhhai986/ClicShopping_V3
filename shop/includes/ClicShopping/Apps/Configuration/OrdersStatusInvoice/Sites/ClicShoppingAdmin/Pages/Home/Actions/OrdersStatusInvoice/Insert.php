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


  namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersStatusInvoice;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('OrdersStatusInvoice');
    }

    public function execute() {
      $CLICSHOPPING_Language = Registry::get('Language');

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i=0, $n=count($languages); $i<$n; $i++) {
        $orders_status_invoice_name_array = $_POST['orders_status_invoice_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = ['orders_status_invoice_name' => HTML::sanitize($orders_status_invoice_name_array[$language_id])];


        if (empty($orders_status_invoice_id)) {

          $Qnext = $this->app->db->get('orders_status_invoice', 'max(orders_status_invoice_id) as orders_status_invoice_id');
          $orders_status_invoice_id = $Qnext->valueInt('orders_status_invoice_id') + 1;
        }

        $insert_sql_data = ['orders_status_invoice_id' => (int)$orders_status_invoice_id,
                            'language_id' => (int)$language_id
                           ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('orders_status_invoice', $sql_data_array);
      }

      if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
        $this->app->db->save('configuration', [
                                              'configuration_value' => $orders_status_invoice_id
                                              ], [
                                                'configuration_key' => 'DEFAULT_ORDERS_STATUS_INVOICE_ID'
                                              ]
                            );
      }

      $this->app->redirect('OrdersStatusInvoice&page=' . $_GET['page'] . '&oID=' . $orders_status_invoice_id);
    }
  }