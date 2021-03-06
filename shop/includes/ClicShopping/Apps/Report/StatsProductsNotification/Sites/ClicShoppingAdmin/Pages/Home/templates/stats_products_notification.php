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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_StatsProductsNotification = Registry::get('StatsProductsNotification');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  // show customers for a product
  if (isset($_GET['show_customers']) && (int)$_GET['pID']) {
  $products_id = (int)$_GET['pID'];
?>

  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading""><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/client.gif', $CLICSHOPPING_StatsProductsNotification->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_StatsProductsNotification->getDef('heading_title'); ?></span>
            <span class="col-md-6 text-md-right"><?php echo HTML::button($CLICSHOPPING_StatsProductsNotification->getDef('button_back'), null, $CLICSHOPPING_StatsProductsNotification->link('StatsProductsNotification'), 'primary'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="5">
          <tr class="dataTableHeadingRow">
            <td><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_number'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_name'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_email'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_date'); ?></td>
            <td class="text-md-right"><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_action'); ?></td>
          </tr>
<?php
    $Qcustomers = $CLICSHOPPING_StatsProductsNotification->dbprepare('select  SQL_CALC_FOUND_ROWS   c.customers_firstname,
                                                                                             c.customers_lastname,
                                                                                             c.customers_email_address,
                                                                                             pn.date_added
                                                                from :table_customers c,
                                                                     :table_products_notifications pn
                                                                where c.customers_id = pn.customers_id
                                                                and pn.products_id = :products_id
                                                                order by c.customers_firstname,
                                                                 c.customers_lastname";
                                                                limit :page_set_offset,
                                                                      :page_set_max_results
                                                                ');

    $Qcustomers->bindInt(':products_id',(int)$products_id);
    $Qcustomers->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qcustomers->execute();

    $listingTotalRow = $Qcustomers->getPageSetTotalRows();

    if ($listingTotalRow > 0) {

    while ($customers = $Qcustomers->fetch()) {

      $rows++;

      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
?>
                <tr>
                  <td width="30" nowrap class="dataTableContent"><?php echo $rows; ?>.</td>
                  <td class="dataTableContent"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Customers&search=' . $Qcustomers->value('customers_lastname')), $Qcustomers->value('customers_firstname') . ' ' . $Qcustomers->value('customers_lastname')); ?></td>
                  <td class="dataTableContent"><?php echo HTML::link(CLICSHOPPING::link(null,'A&Communication\EMail&EMail&customer=' . $Qcustomers->value('customers_email_address')), $Qcustomers->value('customers_email_address'))?></td>
                  <td class="dataTableContent"><?php echo DateTime::getLong($Qcustomers->value('date_added')); ?>&nbsp;</td>
                  <td class="dataTableContent text-md-right">
<?php
  echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Customers&search=' . $Qcustomers->value('customers_lastname')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_StatsProductsNotification->getDef('icon_edit_customer')));
  echo '&nbsp;';
  echo HTML::link(CLICSHOPPING::link(null,'A&Communication\EMail&EMail&customer=' . $Qcustomers->value('customers_email_address')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/email.gif', $CLICSHOPPING_StatsProductsNotification->getDef('icon_email')));
?>
                  </td>
                </tr>
<?php
    }
?>
          </table></td>
      </tr>
      <tr>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qcustomers->getPageSetLabel($CLICSHOPPING_StatsProductsNotification->getDef('text_display_number_of_link')); ?></div>
            <div class="float-md-right text-md-right"><?php echo $Qcustomers->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
          </div>
        </div>
      </tr>
    </table>
<?php
  } // end $listingTotalRow
  // default
  } else {

  if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * (int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN - (int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN;
?>
    <!-- body //-->
    <div class="contentBody">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-block headerCard">
            <div class="row">
              <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/categorie_produit.gif', $CLICSHOPPING_StatsProductsNotification->getDef('heading_title'), '40', '40'); ?></span>
              <span class="col-md-3 pageHeading" width="250"><?php echo '&nbsp;' . $CLICSHOPPING_StatsProductsNotification->getDef('heading_title'); ?></span>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <td>
          <table class="table table-sm table-hover table-striped">
            <thead>
            <tr class="dataTableHeadingRow">
              <th width="20"></th>
              <th width="50"></th>
              <th><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_number'); ?></th>
              <th><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_products'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_model'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_count'); ?>&nbsp;</th>
              <th class="text-md-right"><?php echo $CLICSHOPPING_StatsProductsNotification->getDef('table_heading_action'); ?></th>
            </tr>
            </thead>
            <tbody>
<?php

  $Qproducts = $CLICSHOPPING_StatsProductsNotification->db->prepare('select  SQL_CALC_FOUND_ROWS   count(pn.products_id) as count_notifications,
                                                                                          pn.products_id,
                                                                                          pd.products_name,
                                                                                          p.products_image,
                                                                                          p.products_model
                                                             from :table_products_notifications pn,
                                                                  :table_products_description pd,
                                                                  :table_products p,
                                                                  :table_customers c
                                                             where pn.products_id = pd.products_id
                                                             and pd.language_id = :language_id
                                                             and pn.customers_id = c.customers_id
                                                             and pn.products_id = p.products_id
                                                             group by pn.products_id order by count_notifications desc,
                                                                      pn.products_id
                                                            limit :page_set_offset,
                                                                  :page_set_max_results
                                                            ');

  $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $Qproducts->execute();

  $listingTotalRow = $Qproducts->getPageSetTotalRows();

  $rows = 0;

  if ($listingTotalRow > 0) {

    while ($products = $Qproducts->fetch()) {

      $rows++;

      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
?>
                  <tr>
                    <td scope="row" width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Preview&Preview&pID=' . $Qproducts->valueInt('products_id') . '?page=' . $_GET['page']), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_StatsProductsNotification->getDef('icon_preview'))); ?></td>
                    <td><?php echo  HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qproducts->value('products_image'), $Qproducts->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
                    <td><?php echo $rows; ?>.</td>
                    <td><?php echo HTML::link(CLICSHOPPING::link('StatsProductsNotification&show_customers&pID=' . $products['products_id'] . '&page=' . $page), $Qproducts->value('products_name')); ?></td>
                    <td class="text-md-center"><?php echo HTML::link(CLICSHOPPING::link(null ,'A&Catalog\Products&Products&pID=' . $Qproducts->valueInt('products_id') . '&action=new_product'), $Qproducts->value('products_model')); ?></td>
                    <td class="text-md-center"><?php echo $Qproducts->valueInt('count_notifications'); ?>&nbsp;</td>
                    <td class=text-md-right">
<?php
  echo HTML::link(CLICSHOPPING::link('StatsProductsNotification&show_customers&pID=' . $Qproducts->valueInt('products_id') . '&page=' . $page), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/client_b2b.gif', $CLICSHOPPING_StatsProductsNotification->getDef('icon_edit_customer')));
  echo '&nbsp;';
  echo HTML::link(CLICSHOPPING::link(null ,'A&Catalog\Products&Products&pID=' . $Qproducts->valueInt('products_id') . '&action=new_product'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_StatsProductsNotification->getDef('icon_edit')));
?>
                  </tr>
<?php
    } // end $listingTotalRow
  }
?>
            </tbody>
          </table></td>
        </tr>
      </table>

<?php
  if ($listingTotalRow > 0) {
?>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qproducts->getPageSetLabel($CLICSHOPPING_StatsProductsNotification->getDef('text_display_number_of_link')); ?></div>
              <div class="float-md-right text-md-right"><?php echo $Qproducts->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
            </div>
          </div>
<?php
    } // end $listingTotalRow
  } // end else
?>
  </div>


