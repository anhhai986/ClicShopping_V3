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
  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;

  class CurrenciesGeolocalisation implements \ClicShopping\OM\ServiceInterface {

    public static function start() {

// hook has impact in all shop
      Registry::get('Hooks')->call('AllShop', 'CurrenciesGeolocalisation');

      return true;
    }

    public static function stop() {
      return true;
    }
  }
