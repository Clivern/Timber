<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Timber
 */

namespace Timber\Libraries;

/**
 * Provide Support to Plugins
 *
 * @since 1.0
 */
class Plugins {

      /**
       * Plugins configs
       *
       * @since 1.0
       * @access private
       * @var array $this->configs
       */
      private $configs = array(
            'active_plugins' => array(),
            'plugins_data' => array(),
      );

      /**
       * Instance of timber app
       *
       * @since 1.0
       * @access private
       * @var object $this->timber
       */
      private $timber;

      /**
       * Holds an instance of this class
       *
       * @since 1.0
       * @access private
       * @var object self::$instance
       */
      private static $instance;

      /**
       * Create instance of this class or return existing instance
       *
       * @since 1.0
       * @access public
       * @return object an instance of this class
       */
      public static function instance()
      {
            if ( !isset(self::$instance) ) {
                  self::$instance = new self();
            }
            return self::$instance;
      }

      /**
       * Set class dependencies
       *
       * @since 1.0
       * @access public
       * @param object $timber
       * @return object
       */
      public function setDepen($timber)
      {
            $this->timber = $timber;
            return $this;
      }

      /**
       * Config class properties
       *
       * @since 1.0
       * @access public
       */
      public function config()
      {
            $this->configs['active_plugins'] = unserialize($this->timber->config('_active_plugins'));
            $this->configs['plugins_data']  = unserialize($this->timber->config('_plugins_data'));

            $this->includePlugins();
      }

      /**
       * Run activation callaback for plugin
       *
       * @since 1.0
       * @access public
       * @param string $plugin
       */
      public function activatePlugin($plugin)
      {
            $plugin_path = TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $plugin . '/' . $plugin . '.php';
            $plugin_info_path = TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $plugin . '/info.php';

            if( !(is_file($plugin_path)) || !(file_exists($plugin_path)) || !(is_file($plugin_info_path)) || !(file_exists($plugin_info_path)) ){
                  return false;
            }

            # Incude Plugin
            @include_once $plugin_path;

            $this->timber->applyHook('timber.activate_plugin', $this->timber, $plugin);
            $key = array_search($plugin, $this->configs['active_plugins']);

            # If Plugin Not Exist, Add and Save
            if( $key === false ) {
                  $this->configs['active_plugins'][] = $plugin;

                  $this->timber->option_model->updateOptionByKey(array(
                        'op_key' => '_active_plugins',
                        'op_value' => serialize($this->configs['active_plugins'])
                  ));
            }
            return true;
      }

      /**
       * Run deactivation callaback for plugin
       *
       * @since 1.0
       * @access public
       * @param string $plugin
       */
      public function deactivatePlugin($plugin)
      {
            $this->timber->applyHook('timber.deactivate_plugin', $this->timber, $plugin);
            $key = array_search($plugin, $this->configs['active_plugins']);

            if( $key !== false ) {
                  unset($this->configs['active_plugins'][$key]);
                  $this->timber->option_model->updateOptionByKey(array(
                        'op_key' => '_active_plugins',
                        'op_value' => serialize($this->configs['active_plugins'])
                  ));
            }
            return true;
      }

      /**
       * Run deletion callaback for plugin
       *
       * @since 1.0
       * @access public
       * @param string $plugin
       */
      public function deletePlugin($plugin)
      {
            $this->timber->applyHook('timber.delete_plugin', $this->timber, $plugin);
            $path = TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $plugin;
            $this->dumpPlugin($path);
            $key = array_search($plugin, $this->configs['active_plugins']);

            # Remove from Active Plugins
            if( $key !== false ) {
                  unset($this->configs['active_plugins'][$key]);
                  $this->timber->option_model->updateOptionByKey(array(
                        'op_key' => '_active_plugins',
                        'op_value' => serialize($this->configs['active_plugins'])
                  ));
            }
            # Remove From Plugins Data
            if( isset($this->configs['plugins_data'][$plugin]) ) {
                  unset($this->configs['plugins_data'][$plugin]);
                  $this->timber->option_model->updateOptionByKey(array(
                        'op_key' => '_plugins_data',
                        'op_value' => serialize($this->configs['plugins_data'])
                  ));
            }

            return true;
      }

      /**
       * Load active plugins
       *
       * @since 1.0
       * @access public
       * @return object
       */
      public function includePlugins()
      {
            $plugins = $this->configs['active_plugins'];

            if( !(count($plugins) > 0) ){
                  return true;
            }

            # Include Plugins
            foreach ( $plugins as $plugin_slug ) {
                  $plugin = TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . strtolower($plugin_slug) . '/' . strtolower($plugin_slug) . '.php';
                  if( !(is_file($plugin)) || !(file_exists($plugin)) ){
                        $key = array_search($plugin_slug, $this->configs['active_plugins']);
                        if( $key !== false ) {
                              unset($this->configs['active_plugins'][$key]);
                        }
                        continue;
                  }
                  @include_once $plugin;
            }

            # Auto fix
            if( count($this->configs['active_plugins']) != count($plugins) ){
                  $this->timber->option_model->updateOptionByKey(array(
                        'op_key' => '_active_plugins',
                        'op_value' => serialize($this->configs['active_plugins'])
                  ));
            }

            return true;
      }

      /**
       * Get all plugins
       *
       * All plugins should have alphanumeric lowercase name (eg. default, default12, .....)
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function getPlugins()
      {
            $plugins = array();

            @chmod( TIMBER_ROOT . TIMBER_PLUGINS_DIR , 0755);
            $dirs = @scandir( TIMBER_ROOT . TIMBER_PLUGINS_DIR );

            if( $dirs === false ){
                  return array();
            }

            foreach ( $dirs as $dir ) {
                  if ( ($dir === '.') || ($dir === '..') ){ continue; }
                  $dir = strtolower($dir);
                  if ( $dir != preg_replace('/[^a-z0-9]/i', '', $dir) ){ continue; }
                  if ( (is_dir(TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $dir)) && (is_file(TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $dir . '/info.php')) && (is_file(TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $dir . '/' . $dir . '.php')) ) {
                        $plugins[] = array(
                              'slug' => $dir,
                              'value' => ucfirst($dir),
                        );
                  }
            }

            return $plugins;
      }

      /**
       * Get plugins list
       *
       * All plugins should have alphanumeric lowercase name (eg. default, default12, .....)
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function getPluginsList()
      {
            $plugins = array();

            @chmod( TIMBER_ROOT . TIMBER_PLUGINS_DIR , 0755);
            $dirs = @scandir( TIMBER_ROOT . TIMBER_PLUGINS_DIR );

            if( $dirs === false ){
                  return array();
            }

            foreach ( $dirs as $dir ) {
                  if ( ($dir === '.') || ($dir === '..') ){ continue; }
                  $dir = strtolower($dir);
                  if ( $dir != preg_replace('/[^a-z0-9]/i', '', $dir) ){ continue; }
                  if ( (is_dir(TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $dir)) && (is_file(TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $dir . '/info.php')) && (is_file(TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $dir . '/' . $dir . '.php')) ) {
                        $plugins[] = $dir;
                  }
            }

            return $plugins;
      }

      /**
       * Sync Plugins and Reflect changes to DB
       *
       * @since 1.0
       * @access public
       * @return boolean
       */
      public function syncPlugins()
      {
            $plugins = $this->getPluginsList();
            $plugins_data = array();

            if( !(count($plugins) > 0) ){
                  //issue to read plugins dir
                  return false;
            }

            foreach ($plugins as $key => $plugin) {
                  $plugins_data[$plugin] = include_once TIMBER_ROOT . TIMBER_PLUGINS_DIR . '/' . $plugin . '/info.php';
            }

            if( !(count($this->configs['plugins_data']) > 0) ){
                  $this->configs['plugins_data'] = $plugins_data;
                  return (boolean) $this->timber->option_model->updateOptionByKey( array('op_key' => '_plugins_data', 'op_value' => serialize($plugins_data)) );
            }

            $changes_detector = false;

            foreach ($plugins_data as $plugin => $plugin_info) {
                  if( !(isset($this->configs['plugins_data'][$plugin])) || ($this->configs['plugins_data'][$plugin]['plugin_version'] != $plugin_info['plugin_version']) ){
                        $changes_detector = true;
                  }
            }
            if( $changes_detector ){
                  $this->configs['plugins_data'] = $plugins_data;
                  return (boolean) $this->timber->option_model->updateOptionByKey( array('op_key' => '_plugins_data', 'op_value' => serialize($plugins_data)) );
            }
            return true;
      }

      /**
       * Get plugins data after sync
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function getPluginsData()
      {
            return $this->configs['plugins_data'];
      }

      /**
       * Get active plugins
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function getActivePlugins()
      {
            return $this->configs['active_plugins'];
      }

      /**
       * Dump Plugin Files and Folders
       *
       * @since 1.0
       * @access public
       * @param string $path
       * @return boolean
       */
      public function dumpPlugin($path)
      {
            if( !(is_dir($path)) ){
                  return true;
            }

            $path = rtrim( $path, '/' ) . '/';
            @chmod($path, 0755);
            $dirs = @scandir( $path );
            if( !(is_array($dirs)) || !(count($dirs) > 0) ){
                  return true;
            }
            foreach ( $dirs as $dir ) {
                  if ( $dir === '.' || $dir === '..'){ continue; }
                  if ( is_dir( $path . $dir ) ) {
                        $this->dumpPlugin($path . $dir);
                        @chmod( $path . $dir , 0755);
                        @rmdir( $path . $dir );
                  }elseif( is_file( $path . $dir ) ) {
                        @chmod( $path . $dir, 0755);
                        @unlink( $path . $dir );
                  }
            }
            @chmod( $path , 0755);
            @rmdir( $path );
            return true;
      }
}