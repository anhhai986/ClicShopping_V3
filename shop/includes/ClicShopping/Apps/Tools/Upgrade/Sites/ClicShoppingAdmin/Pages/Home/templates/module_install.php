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
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Github;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Upgrade = Registry::get('Upgrade');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Github = new Github();
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if ($CLICSHOPPING_MessageStack->exists('header')) {
    echo $CLICSHOPPING_MessageStack->get('header');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row col-md-12">
<?php
  echo HTML::form('upgrade', $CLICSHOPPING_Upgrade->link('ModuleInstall'), 'post', null, ['session_id' => true]);
?>
            <div class="col-md-12 form-group row">
              <div class="col-md-3">
                <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/apps.png', $CLICSHOPPING_Upgrade->getDef('heading_title'), '40', '40'); ?></span>
                <span class="col-md-11 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('heading_title'); ?></span>
              </div>

              <div class="col-md-2"><?php echo HTML::selectMenu('install_module_directory', $CLICSHOPPING_Github->getModuleDirectory(), $_POST['template_directory']); ?></div>
              <div class="col-md-2"><?php echo HTML::selectMenu('install_module_template_directory', $CLICSHOPPING_Github->getModuleTemplateDirectory(), $_POST['template_directory']); ?></div>
              <div class="col-md-2"><?php echo HTML::inputField('module_search', '', 'id="search" placeholder="' . $CLICSHOPPING_Upgrade->getDef('text_search') . '"'); ?></div>
              <div class="col-md-3 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset'), null, $CLICSHOPPING_Upgrade->link('Upgrade&ResetCache'), 'danger', null, 'sm') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset_temp'), null, $CLICSHOPPING_Upgrade->link('Upgrade&ResetCacheTemp'), 'warning', null, 'sm') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_back'), null, $CLICSHOPPING_Upgrade->link('Upgrade'), 'primary', null, 'sm') . '&nbsp;';
?>
              </div>
            </div>
            <div class="row col-md-12">
              <div class="col-md-12 form-group row">
                <span class="col-md-4"></span>
                <span class="col-md-4 text-md-center"><?php echo $CLICSHOPPING_Github->getDropDownMenuSearchOption(); ?></span>
                <span class="col-md-4"><?php echo HTML::button($CLICSHOPPING_Upgrade->getDef('text_search'), null, null, 'primary');?></span>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
<?php
  if (isset($_POST['module_search'])) {
    $module_directory = HTML::sanitize($_POST['module_search']);
  } elseif (isset($_GET['template_directory'])) {
    $module_directory = HTML::sanitize($_POST['template_directory']);
  }

  if (isset($_POST['install_module_template_directory'])) {
    $module_directory = HTML::sanitize($_POST['install_module_template_directory']);
  } elseif (isset( $_POST['install_module_directory'])) {
    $module_directory = HTML::sanitize($_POST['install_module_directory']);
  }

  if (isset($module_directory)) {
    $file_cache_temp_array = $CLICSHOPPING_Template->getSpecificFiles($CLICSHOPPING_Github->cacheGithubTemp, $module_directory . '*', 'json');

    if (is_array($file_cache_temp_array)) {
      foreach ($file_cache_temp_array as $value) {
        if (is_file($CLICSHOPPING_Github->cacheGithubTemp . $value['name'] . '.json')) {
          $result[] = $CLICSHOPPING_Github->getSearchInsideRepo($CLICSHOPPING_Github->cacheGithubTemp . $value['name'] . '.json');
        }
      }
      $count_file = count($file_cache_temp_array);
    } else {
      $result = $CLICSHOPPING_Github->getSearchInsideRepo();
      $count_file = $CLICSHOPPING_Github->getSearchTotalCount();
    }

    if ($count_file == 0) {
?>
      <div class="alert alert-warning" role="alert">
<?php
          echo $CLICSHOPPING_Upgrade->getDef('warning_no_module');
          exit;
?>
      </div>
<?php
    } else {
?>
    <div class="alert alert-warning" role="alert">
<?php
        echo $CLICSHOPPING_Upgrade->getDef('text_count_search') . ' ' . $count_file;
?>
    </div>
<?php
    }
?>
    <div class="d-flex flex-wrap">

<?php
//    $count = $CLICSHOPPING_Github->getSearchTotalCount();

    for ($i=0, $n=$count_file; $i<$n; $i++) {
      if (is_null($result->items[$i])) {
        $item = $result[$i];
        $module_real_name = $item->title;
        $link_html =  'https://github.com/ClicShoppingOfficialModulesV3/'. $item->title;
      } else {
        $item = $result->items[$i];
        $module_real_name = $item->name;
        $link_html =  $item->html_url;
      }

      $directory = $item->owner->login;

      $local_version = '';
      $temp_version = '';
      $temp_check = false;
      $installed_check = false;

      if (!is_null($CLICSHOPPING_Github->getCacheFile($module_real_name . '.json')) === true || !is_null($CLICSHOPPING_Github->getCacheFileTemp($module_real_name . '.json'))) {
        if (!is_null($CLICSHOPPING_Github->getCacheFile($module_real_name . '.json')) === true ) {
          $result_module_real_name = $CLICSHOPPING_Github->getCacheFile($module_real_name . '.json');
          $file_cache_information =  '<span class="badge badge-primary"> - File Installed Cached</span>';

          $item = $result_module_real_name;
          $content_module_name = $item->title . '.json';
          $local_version = $CLICSHOPPING_Upgrade->getDef('text_installed_version')  . ' <span class="badge badge-primary">'. $item->version . '</span>';
          $description = $item->description;
          $installed_check = true;
        } else {
          $result_module_real_name = $CLICSHOPPING_Github->getCacheFileTemp($module_real_name . '.json');
          $file_cache_information =  $CLICSHOPPING_Upgrade->getDef('text_local_version') . ' <span class="badge badge-info">  - Temp Cached</span>';

          $item = $result_module_real_name;
          $content_module_name = $item->title . '.json';
          $local_version = $CLICSHOPPING_Upgrade->getDef('text_temp_version') . ' <span class="badge badge-info">' . $item->version . '</span>';
          $description = $item->description;
          $temp_check = true;
        }


        if (!is_null($CLICSHOPPING_Github->getCacheFile($module_real_name . '.json')) === true ) {
          $result_module_real_name = $CLICSHOPPING_Github->getCacheFileTemp($module_real_name . '.json');
          $temp_version = $CLICSHOPPING_Upgrade->getDef('text_temp_version') . ' <span class="badge badge-info">' . $item->version . '</span>';
        }

        if ($content_module_name == $module_real_name . '.json') {
?>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <span class="col-md-12">
                  <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/header/logo_clicshopping.png', '50', '50'); ?>
                  <?php echo $module_real_name; ?></span><?php echo $file_cache_information; ?>
                </span>
              </div>
              <div class="card-block">
              <div class="row">
                <div class="card-text">
                  <div class="col-md-12"><?php echo $description; ?></div>
                  <div class="col-md-12" style="color: #FF0000;"><?php echo $local_version; ?></div>
                  <div class="col-md-12" style="color: #312eff;"><?php echo $temp_version; ?></div>

                  <div class="col-md-6 float-md-left">
                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#myModal_<?php echo $i;?>"><?php echo $CLICSHOPPING_Upgrade->getDef('button_more_infos'); ?></button>
                    <!-- Modal -->
                    <div id="myModal_<?php echo $i;?>" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title"><a href="<?php echo $link_html; ?>/archive/master.zip"><?php echo $module_real_name; ?></a></h4>
                          </div>
                          <div class="modal-body">
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_description') . $description; ?></p>
                            <p>
<?php
          if (strtolower($item->type) == 'apps') {
            echo $CLICSHOPPING_Upgrade->getDef('text_activate') . ' : ' . HTTP::typeUrlDomain('ClicShoppingAdmin')  . 'index.php?A&' . $item->module_directory . '\\' . $item->apps_name;
          }
?>
                            </p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_licence') . $item->license; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_vendor') . $item->authors[0]->name; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_tag') . $item->tag; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_module_type') . $item->type;?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_directory_install') . $item->install . $item->module_directory; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_more_infos') . '<a href="' . $link_html .'" target="_blank" rel="noreferrer">Github</a>';  ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_download') . '<a href="' . $link_html .'/archive/master.zip">' . $module_real_name . '</a>';  ?></p>
<?php
          if (!is_null($item->image) || !empty($item->image)) {
            if ($directory == 'ClicShoppingOfficialModulesV3') {
?>
                            <p><img src="https://raw.github.com/<?php echo $directory . '/' . $module_real_name; ?>/master/<?php echo $item->image; ?>" alt="<?php echo $module_real_name; ?>" class="img-fluid"></img></p>
<?php
            } else {
?>
                            <p><img src="https://raw.github.com/<?php echo $directory . '/' . $module_real_name; ?>/master/<?php echo $item->image; ?>" alt="<?php echo $module_real_name; ?>" class="img-fluid"></img></p>
<?php
            }
          }
?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $CLICSHOPPING_Upgrade->getDef('text_close'); ?></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="float-md-right">
                    <div class="col-md-12">
<?php
          if ($temp_check === true) {

            $error = false;

            if (strtolower($item->is_free) == 'no') {
              if (!empty($item->website_link_to_sell)) {
                if (strpos("https://www.clicshopping.org/forum/files/file/", "https://www.clicshopping.org")) {
                  $message = $CLICSHOPPING_Upgrade->getDef('error_link_not_allowed');
                  $error = true;
                } else {
                  $marketplace_link = $item->website_link_to_sell;
                }
              }

              if ($error === true) {
                echo  '<span class="text-md-right"> ' . $message . '</span>';
              } else {
                echo  '<span class="text-md-right"><a href="' .$marketplace_link . '" target="_blank" rel="noreferrer" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">' . $CLICSHOPPING_Upgrade->getDef('button_not_free') . '</a></span>';
              }
            } else {
               echo HTML::form('install', $CLICSHOPPING_Upgrade->link('Upgrade&ModuleInstall'));
               echo  '<span class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_install'), null, null, 'warning', null, 'sm') . '</span>';
            }

            echo HTML::hiddenField('githubLink', $link_html .'/archive/master.zip');
            echo HTML::hiddenField('type_module', $item->type_module);
            echo HTML::hiddenField('module_real_name', $module_real_name);
            echo HTML::hiddenField('module_directory',$module_directory);

            if (strtolower($item->is_free) == 'yes') {
              echo '</form>';
            }


            if (strtolower($item->is_core) == 'yes') {
              echo  '<span class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_core'), null, null, 'danger', null, 'sm') . '</span>';
            }

          }

          if (strtolower($item->type) == 'apps') {
            $module = CLICSHOPPING::link(null, 'A&' . $item->module_directory . '\\' . $item->apps_name);
          } else {
            $module = 'index.php?A&Configuration\Modules&Modules&set=' . $item->module_directory;
          }

          if ($local_version != -1) {
            if ($installed_check === true) {
              echo '<span class="text-align-right">' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_setting'), null, $module, 'success', null, 'sm') . '</span>';
            }
          }
?>
                  </div>
                </div>
<?php
        }
      } else {
//****************************************
//  Github version
//****************************************
?>
                  <div class="col-md-4">
                    <div class="card">
                      <div class="card-header">
                        <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/header/logo_clicshopping.png', '50', '50'); ?></span>
                        <span class="col-md-11"><a href="<?php echo $link_html; ?>/archive/master.zip"><?php echo $module_real_name . ' - Github'; ?></a></span>
                      </div>
                      <div class="card-block">
                        <div class="row">
                          <div class="card-text">
<?php
        $result_module_real_name = $CLICSHOPPING_Github->getJsonRepoContentInformationModule($module_real_name);

        if (is_array($result_module_real_name)) {
         foreach ($result_module_real_name as $content) {
           $content_module_name = $content->name;
           $content_module_sha = $content->sha;

           if ($content_module_name == $module_real_name . '.json') {
              $result_content_module = $CLICSHOPPING_Github->getJsonModuleInformaton($content->download_url);
              $description = $result_content_module->description;
              $current_version_github = $result_content_module->version;
?>
                            <div class="col-md-12"><?php echo $result_content_module->description; ?></div>
                            <div class="col-md-12"><?php echo $CLICSHOPPING_Upgrade->getDef('text_server_version') . $current_version_github; ?></div>

                            <div class="col-md-6 float-md-left">
                              <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#myModal_<?php echo $i;?>"><?php echo $CLICSHOPPING_Upgrade->getDef('button_more_infos'); ?></button>
                              <!-- Modal -->
                              <div id="myModal_<?php echo $i; ?>" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                  <!-- Modal content-->
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h4 class="modal-title"><a href="<?php echo $link_html; ?>/archive/master.zip"><?php echo $module_real_name; ?></a></h4>
                                    </div>
                                    <div class="modal-body">
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_description') . $result_content_module->description; ?></p>
                                      <p>
<?php
          if ($result_content_module->type == 'apps' || $result_content_module->type == 'Apps') {
            echo $CLICSHOPPING_Upgrade->getDef('text_activate') . ' : ' . HTTP::typeUrlDomain('ClicShoppingAdmin')  . 'index.php?A&' . $result_content_module->activate_link . $result_content_module->module_directory . '\\' . $result_content_module->apps_name;
          }
?>
                                      </p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_licence') . $result_content_module->license; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_vendor') . $result_content_module->authors[0]->name; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_tag') . $result_content_module->tag; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_module_type') . $result_content_module->type;?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_directory_install') . $result_content_module->install; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_dependance') . ' '. $result_content_module->dependance;  ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_more_infos') . '<a href="' . $link_html .'" target="_blank" rel="noreferrer">Github</a>'; ?></p>
                                      <p>
<?php
          if (strtolower($result_content_module->is_free) != 'no') {
            echo $CLICSHOPPING_Upgrade->getDef('text_download') . '<a href="' . $link_html .'/archive/master.zip">' . $module_real_name . '</a>';
          }
?>
                                      </p>
<?php
          if (!is_null($result_content_module->image) || !empty($result_content_module->image)) {
            if ($directory == 'ClicShoppingOfficialModulesV3') {
?>
                                      <p><img src="https://raw.github.com/<?php echo $directory . '/' . $module_real_name; ?>/master/<?php echo $result_content_module->image; ?>" alt="<?php echo $module_real_name; ?>" class="img-fluid"></img></p>
<?php
            } else {
?>
                                     <p><img src="https://raw.github.com/<?php echo $directory . '/' . $module_real_name; ?>/master/<?php echo $result_content_module->image; ?>" alt="<?php echo $module_real_name; ?>" class="img-fluid"></img></p>
<?php
            }
          }
?>                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $CLICSHOPPING_Upgrade->getDef('text_close'); ?></button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6 text-md-right float-md-right">
<?php
            if (strtolower($result_content_module->is_free) != 'yes') {
              echo  '<span class="text-md-right"><a href="' . $result_content_module->website_link_to_sell . '" target="_blank" rel="noreferrer" class="btn btn-success btn-sm active" role="button" aria-pressed="true">' . $CLICSHOPPING_Upgrade->getDef('button_not_free') . '</a></span>';
            } else {
              echo HTML::form('install', $CLICSHOPPING_Upgrade->link('Upgrade&ModuleInstall'));
              echo  '<span class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_install'), null, null, 'warning', null, 'sm') . '</span>';
            }

            if (strtolower($result_content_module->is_core) == 'yes') {
              echo  '<span class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_core'), null, null, 'danger', null, 'sm') . '</span>';
            }

            echo HTML::hiddenField('type_module', $result_content_module->type_module);
            echo HTML::hiddenField('module_real_name', $module_real_name);
            echo HTML::hiddenField('module_directory',$module_directory);

            if (strtolower($result_content_module->is_free) == 'yes') {
               echo '</form>';
            }

            if (strtolower($result_content_module->type) == 'apps') {
              $module = CLICSHOPPING::link(null, 'A&' . $result_content_module->module_directory . '\\' . $result_content_module->apps_name);
            } else {
              $module = 'index.php?A&Configuration\Modules&Modules&set=' . $result_content_module->module_directory;
            }
?>
          </div>
<?php
          }
        }
      } else {
?>
                <div class="col-md-12">
                  <div class="alert alert-warning" role="alert">
                    <?php echo $CLICSHOPPING_Upgrade->getDef('error_rate_exceed'); ?>
                  </div>
                </div>
<?php
      }
    }
?>
              </div>
            </div>
          </div>
        </div>
        <div class="separator"></div>
      </div>
<?php
    }
  }
?>
    </div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div class="alert alert-info" role="alert">
        <div class="row">
          <span class="col-md-12">
            <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Upgrade->getDef('title_help')); ?>
            <strong><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('title_help'); ?></strong>
          </span>
        </div>
        <div class="separator"></div>
        <div class="row">
          <span class="col-md-12"><?php echo $CLICSHOPPING_Upgrade->getDef('text_install_files'); ?></span>
        </div>
        <div class="separator"></div>
        <div class="row">
          <span class="col-md-12">
<?php
  echo $CLICSHOPPING_Upgrade->getDef('text_search_limit') . ' 20 <br />';
  echo $CLICSHOPPING_Upgrade->getDef('text_core_limit') . ' 60 <br />';
  echo $CLICSHOPPING_Upgrade->getDef('text_rate_limit') . ' 30 <br />';
  echo $CLICSHOPPING_Upgrade->getDef('text_cache_file');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>