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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

/**
 * The Address class handles address related functions such as the format and country and zone information
 */

  class Address {

/*
* Return a formatted address
*  TABLES: address_format
*/
  public static function addressFormat($address_format_id, $address, $html, $boln, $eoln) {
    $CLICSHOPPING_Customer = Registry::get('Customer');

    if (!empty($CLICSHOPPING_Customer) ) {
      $customer_group_id = $CLICSHOPPING_Customer->getCustomersGroupID();
    } else {
      $customer_group_id = 0;
    }

    $Qformat = Registry::get('Db')->get('address_format', 'address_format', ['address_format_id' => (int)$address_format_id]);

    $replace = [
              '$company' => HTML::outputProtected($address['company']),
              '$firstname' => '',
              '$lastname' => '',
              '$street' => HTML::outputProtected($address['street_address']),
              '$suburb' => HTML::outputProtected($address['suburb']),
              '$city' => HTML::outputProtected($address['city']),
              '$state' => HTML::outputProtected($address['state']),
              '$postcode' => HTML::outputProtected($address['postcode']),
              '$country' => ''
              ];

    if (isset($address['firstname']) && !empty($address['firstname'])) {
      $replace['$firstname'] = HTML::outputProtected($address['firstname']);
      $replace['$lastname'] = HTML::outputProtected($address['lastname']);
    } elseif (isset($address['name']) && !empty($address['name'])) {
      $replace['$firstname'] = HTML::outputProtected($address['name']);
    }

    if (isset($address['country_id']) && !empty($address['country_id'])) {
      $replace['$country'] = self::getCountryName($address['country_id']);

      if (isset($address['zone_id']) && !empty($address['zone_id'])) {
        $replace['$state'] = static::getZoneName($address['country_id'], $address['zone_id'], $replace['$state']);
      }
    } elseif (isset($address['country']) && !empty($address['country'])) {
      if (CLICSHOPPING::getSite() == 'ClicShoppingAdmin') {
        $replace['$country'] = HTML::outputProtected($address['country']);
      } else {
        $replace['$country'] = HTML::outputProtected($address['country']['title']); // bug osc à tester
      }
    }

    $replace['$zip'] = $replace['$postcode'];

    if ($html) {
// HTML Mode
      $HR = '<hr />';
      $hr = '<hr />';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br />';
        $cr = '<br />';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $replace['$CR'] = $CR;
    $replace['$cr'] = $cr;
    $replace['$HR'] = $HR;
    $replace['$hr'] = $hr;

    $replace['$statecomma'] = '';
    $replace['$streets'] = $replace['$street'];

    if ($replace['$suburb'] != '') $replace['$streets'] = $replace['$street'] . $replace['$cr'] . $replace['$suburb'];
    if ($replace['$state'] != '') $replace['$statecomma'] = $replace['$state'] . ', ';

    $address = strtr($Qformat->value('address_format'), $replace);

    if ((($customer_group_id == 0) && (ACCOUNT_COMPANY == 'true') && (!empty($replace['$company']))) || (($customer_group_id != 0) && (ACCOUNT_COMPANY_PRO == 'true') && (!is_null($replace['$company'])))) {
      $address = $replace['$company'] . $replace['$cr'] . $address;
    }

    return $address;
  }

/**
 * Returns the address_format_id for the given country
 * @param $country_id
 * @return int
 */

    public static function getAddressFormatId($country_id) {

      $format_id = 1;

      $Qformat = Registry::get('Db')->get('countries', 'address_format_id', ['countries_id' => (int)$country_id]);

      if ($Qformat->fetch() !== false) {
        $format_id = $Qformat->valueInt('address_format_id');
      }

      return $format_id;
    }



/**
 * Return the zone code
 *
 * @param int $id The ID of the zone
 * @access public
 * @return string
 */

    public static function getZoneCode($country_id, $zone_id, $default_zone) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qzone = $CLICSHOPPING_Db->prepare('select zone_code
                                          from :table_zones
                                          where zone_country_id = :zone_country_id
                                          and zone_id = :zone_id
                                          and zone_status = 0
                                         ');

      $Qzone->bindInt(':zone_country_id', (int)$country_id);
      $Qzone->bindInt(':zone_id', (int)$zone_id );

      $Qzone->execute();

      if ($Qzone->fetch() !== false) {
        return $Qzone->value('zone_code');
      } else {
        return $default_zone;
      }
    }



/**
 * Return the zone name
 *
 * @param int $id The ID of the zone
 * @access public
 * @return string
 */

    public static function getZoneName($country_id, $zone_id, $default_zone) {
      $Qzone = Registry::get('Db')->get('zones', 'zone_name', ['zone_country_id' => (int)$country_id,
                                                               'zone_id' => (int)$zone_id,
                                                               'zone_status' => 0
                                                              ]
                                        );

      if ($Qzone->fetch() !== false) {
        return $Qzone->value('zone_name');
      } else {
        return $default_zone;
      }
    }

/**
 *  Returns an array with countries
 * @param string $countries_id
 * @param string $with_iso_codes
 * @access public
 */

    public static function getCountries($countries_id = null, $with_iso_codes = false) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $countries_array = [];

      if (!is_null($countries_id)) {
        if ($with_iso_codes === true) {
          $Qcountries = $CLICSHOPPING_Db->prepare('select countries_name,
                                                          countries_iso_code_2,
                                                          countries_iso_code_3
                                                   from :table_countries
                                                   where countries_id = :countries_id
                                                   and status = 1
                                                   order by countries_name
                                                  ');
          $Qcountries->bindInt(':countries_id', (int)$countries_id);
          $Qcountries->execute();

          $countries_array = $Qcountries->toArray();
        } else {

          $Qcountries = $CLICSHOPPING_Db->prepare('select countries_name
                                                   from :table_countries
                                                   where countries_id = :countries_id
                                                   and status = 1
                                                  ');
          $Qcountries->bindInt(':countries_id', (int)$countries_id);
          $Qcountries->execute();

          $countries_array = $Qcountries->toArray();
        }
      } else {
        $countries_array = $CLICSHOPPING_Db->query('select countries_id,
                                                          countries_name,
                                                          countries_iso_code_2
                                                   from :table_countries
                                                   where status = 1
                                                    order by countries_name
                                                  ')->fetchAll();
      }

      return $countries_array;
    }

/**
 * Retour the name of the country
 * @param $country_id
 * @access public
 */
    public static function getCountryName($country_id) {

      $country_array = self::getCountries($country_id);

      return $country_array['countries_name'];

    }

/**
 *  Alias function to getCountries, which also returns the countries iso codes
 * @param string $countries_id
 * @access public
 */
    public function getCountriesWithIsoCodes($countries_id) {
      return static::getCountries($countries_id, true);
    }

/**
 * Return the zones belonging to a country, or all zones
 *
 * @param int $id The ID of the country
 * @access public
 * @return array
*/

    public static function getZones($id = null) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $zones_array = [];

      $sql_query = 'select z.zone_id,
                           z.zone_country_id,
                           z.zone_name,
                           z.zone_status,
                           c.countries_name
                    from :table_zones z,
                         :table_countries c
                    where';

      if ( !empty($id) ) {
        $sql_query .= ' z.zone_country_id = :zone_country_id and';
      }

      $sql_query .= ' z.zone_country_id = c.countries_id
                      and z.zone_status = 0
                      order by c.countries_name,
                                z.zone_name';

      if ( !empty($id) ) {
        $Qzones = $CLICSHOPPING_Db->prepare($sql_query);
        $Qzones->bindInt(':zone_country_id', $id);
      } else {
        $Qzones = $CLICSHOPPING_Db->query($sql_query);
      }

      $Qzones->execute();

      while ( $Qzones->fetch() ) {
        $zones_array[] = ['id' => $Qzones->valueInt('zone_id'),
                          'name' => $Qzones->value('zone_name'),
                          'country_id' => $Qzones->valueInt('zone_country_id'),
                          'country_name' => $Qzones->value('countries_name')
                         ];

      return $zones_array;

      }
    }

/**
 * Get the country zone
 *
 * @param $country_id, if ogf the country
 * @return array $zones_array, zone of the country
 * @access public
 * Shop and Admin
 */
    public static function getCountryZones($country_id) {

      $zones_array = [];

      $Qzones = Registry::get('Db')->get('zones', [
                                                  'zone_id',
                                                  'zone_name'
                                                  ], [
                                                  'zone_country_id' => (int)$country_id,
                                                  'zone_status' => 0
                                                  ], 'zone_name'
                                         );

      while ($Qzones->fetch()) {
        $zones_array[] = [
                          'id' => $Qzones->valueInt('zone_id'),
                          'text' => $Qzones->value('zone_name')
                          ];
      }


      return $zones_array;
    }

/**
 * Get pull down of the country zone
 *
 * @param $country_id, if ogf the country
 * @return array $zones_array, zone of the country
 * @access public
 * Shop and Admin
 */
    public static function getPrepareCountryZonesPullDown($country_id = '') {

      $zones = self::getCountryZones($country_id);

      if (count($zones) > 0) {
        $zones_select = array(array('id' => '',
                                    'text' => CLICSHOPPING::getDef('text_selected')
                              )
        );
        $zones = array_merge($zones_select, $zones);

      } else {

        $zones = array(array('id' => '',
                             'text' => CLICSHOPPING::getDef('text_selected'))
        );
      }

      return $zones;
    }
 }

