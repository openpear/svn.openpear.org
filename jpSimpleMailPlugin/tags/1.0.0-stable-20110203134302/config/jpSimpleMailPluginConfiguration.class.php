<?php

/**
 * jpSimpleMailPlugin configuration.
 * 
 * @package     jpSimpleMailPlugin
 * @subpackage  config
 * @author      brt.river <brt.river@gmail.com>
 * @version     SVN: $Id$
 */
class jpSimpleMailPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if (sfConfig::get('app_jpSimpleMail_swift_debug', false)) {
      $this->dispatcher->connect('debug.web.load_panels', array(
        'JpSwiftWebDebugPanelMailer', 'listenToLoadDebugWebPanelEvent'
      ));
    }
  }
}
