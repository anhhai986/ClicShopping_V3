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

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="create_account_pro_success" id="create_account_pro_success">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-header">
        <h1>
<?php
  if (MEMBER == 'false') {
     echo CLICSHOPPING::getDef('heading_title_account_created');
} else {
    echo CLICSHOPPING::getDef('heading_title_account_wait');
}
?>
        </h1></div>
      <div class="separator"></div>
      <div><?php echo $CLICSHOPPING_Template->getBlocks('modules_create_account_pro'); ?></div>
    </div>
  </div>
</section>