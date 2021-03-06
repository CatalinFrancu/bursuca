<?php

class SmartyWrap {
  private static $theSmarty = null;
  private static $cssFiles = array();
  private static $jsFiles = array();

  static function init($smartyClass) {
    self::$theSmarty = new Smarty();
    self::$theSmarty->template_dir = Util::$rootPath . '/templates';
    self::$theSmarty->compile_dir = Util::$rootPath . '/templates_c';
    self::assign('wwwRoot', Util::$wwwRoot);
    self::assign('user', Session::getUser());
    self::addCss('main');
    self::addJs('main', 'jquery');
  }

  static function assign($name, $value) {
    self::$theSmarty->assign($name, $value);
  }

  static function fetchEmail($templateName) {
    $result = self::$theSmarty->fetch('email/' . $templateName);
    return str_replace("\n", "\r\n", $result); // Acording to specs
  }

  static function display($templateName) {
    self::assign('cssFiles', self::$cssFiles);
    self::assign('jsFiles', self::$jsFiles);
    self::assign('templateName', $templateName);
    self::assign('flashMessage', FlashMessage::getMessage());
    self::assign('flashMessageType', FlashMessage::getMessageType());
    self::$theSmarty->display('layout.tpl');
  }

  static function addCss(/* Variable-length argument list */) {
    // Note the priorities. This allows files to be added in any order, regardless of dependencies
    foreach (func_get_args() as $id) {
      switch($id) {
        case 'jqueryui':           self::$cssFiles[1] = 'smoothness/jquery-ui.min.css'; break;
        case 'jqgrid':             self::$cssFiles[2] = 'ui.jqgrid.css'; break;
        case 'rainbow':            self::$cssFiles[3] = 'rainbow/solarized-light.css'; break;
        case 'select2':            self::$cssFiles[4] = 'select2/select2.css'; break;
        case 'main':               self::$cssFiles[5] = 'main.css?v=6'; break;
        default:
          FlashMessage::add("Cannot load CSS file {$id}");
          Util::redirect(Util::$wwwRoot);
      }
    }
  }

  static function addJs(/* Variable-length argument list */) {
    // Note the priorities. This allows files to be added in any order, regardless of dependencies
    foreach (func_get_args() as $id) {
      switch($id) {
        case 'jquery':
          self::$jsFiles[1] = 'jquery-1.10.2.min.js'; break; 
        case 'jqueryui':
          self::$jsFiles[2] = 'jquery-ui-1.10.3.custom.min.js'; break; 
        case 'jqgrid':
          self::$jsFiles[3] = 'grid.locale-ro.js';
          self::$jsFiles[4] = 'jquery.jqGrid.min.js';
          break; 
        case 'rainbow':
          self::$jsFiles[5] = 'rainbow-custom.min.js'; break;
        case 'select2':
          self::$jsFiles[6] = 'select2.min.js';
          self::$jsFiles[7] = 'select2_locale_ro.js';
          break;
        case 'main':
          self::$jsFiles[8] = 'main.js?v=8'; break;
        case 'replay':
          self::$jsFiles[9] = 'replay.js?v=1'; break;
        default:
          FlashMessage::add("Cannot load JS script {$id}");
          Util::redirect(Util::$wwRoot);
      }
    }
  }
}

?>
