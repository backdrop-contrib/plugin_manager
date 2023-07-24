<?php

/**
 * @file
 * Hooks provided by Plugin Manager.
 *
 * This file is divided into static hooks (hooks with string literal names) and
 * dynamic hooks (hooks with pattern-derived string names).
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Inform Plugin API about plugin types.
 *
 * @return array
 *   An array of plugin types, keyed by the type name.
 *   See the advanced help topic 'plugins-creating' for details of the array
 *   properties.
 */
function hook_plugin_manager_plugin_type() {
  $plugins['my_type'] = array(
    'load themes' => TRUE,
  );

  return $plugins;
}

/**
 * Tells Plugin API where to find module-defined plugins.
 *
 * This hook is used to inform the Plugin API plugin system about the location of a
 * directory that should be searched for files containing plugins of a
 * particular type. Plugin API invokes this same hook for all plugins, using the
 * two passed parameters to indicate the specific type of plugin for which it
 * is searching.
 *
 * The $plugin_type parameter is self-explanatory - it is the string name of the
 * plugin type (e.g., 'brewer_types' 'grinder_types'). The $owner parameter is
 * necessary because Plugin API internally namespaces plugins by the module that
 * owns them. This is an extension of Backdrop best practices on avoiding global
 * namespace pollution by prepending your module name to all its functions.
 * Consequently, it is possible for two different modules to create a plugin
 * type with exactly the same name and have them operate in harmony. In fact,
 * this system renders it impossible for modules to encroach on other modules'
 * plugin namespaces.
 *
 * Given this namespacing, it is important that implementations of this hook
 * check BOTH the $owner and $plugin_type parameters before returning a path.
 * If your module does not implement plugins for the requested module/plugin
 * combination, it is safe to return nothing at all (or NULL). As a convenience,
 * it is also safe to return a path that does not exist for plugins your module
 * does not implement - see form 2 for a use case.
 *
 * Note that modules implementing a plugin also must implement this hook to
 * instruct Plugin API as to the location of the plugins. See form 3 for a use case.
 *
 * The conventional structure to return is "plugins/$plugin_type" - that is, a
 * 'plugins' subdirectory in your main module directory, with individual
 * directories contained therein named for the plugin type they contain.
 *
 * @param string $owner
 *   The system name of the module owning the plugin type for which a base
 *   directory location is being requested.
 * @param string $plugin_type
 *   The name of the plugin type for which a base directory is being requested.
 *
 * @return string
 *   The path where Plugin API' plugin system should search for plugin files,
 *   relative to your module's root. Omit leading and trailing slashes.
 */
function hook_plugin_manager_directory($owner, $plugin_type) {
  // Form 1 - for a module implementing only the 'brewer_types' plugin owned
  // by Plugin API, this would cause the plugin system to search the
  // <moduleroot>/plugins/brewer_types directory for .inc plugin files.
  if ($owner == 'plugin' && $plugin_type == 'brewer_types') {
    return 'plugins/brewer_types';
  }

  // Form 2 - if your module implements only Coffee Maker plugins, and has
  // 'brewer_types' and 'grinder_types' plugins but no 'cache' or
  // 'display_renderers', it is OK to be lazy and return a directory for a
  // plugin you don't actually implement (so long as that directory doesn't
  // exist). This lets you avoid ugly in_array() logic in your conditional, and
  // also makes it easy to add plugins of those types later without having to
  // change this hook implementation.
  if ($owner == 'coffeemaker') {
    return "plugins/$plugin_type";
  }

  // Form 3 - Plugin API makes no assumptions about where your plugins are located,
  // so you still have to implement this hook even for plugins created by your
  // own module.
  if ($owner == 'mymodule') {
    // Yes, this is exactly like Form 2 - just a different reasoning for it.
    return "plugins/$plugin_type";
  }
  // Finally, if nothing matches, it's safe to return nothing at all (== NULL).
}

/**
 * Alter a plugin before it has been processed.
 *
 * This hook is useful for altering flags or other information that will be
 * used or possibly overriden by the process hook if defined.
 *
 * @param array $plugin
 *   An associative array defining a plugin.
 * @param array $info
 *   An associative array of plugin type info.
 */
function hook_plugin_manager_pre_alter(array &$plugin, array &$info) {
  // Override a function defined by the plugin.
  if ($info['type'] == 'my_type') {
    $plugin['my_flag'] = 'new_value';
  }
}

/**
 * Alter a plugin after it has been processed.
 *
 * This hook is useful for overriding the final values for a plugin after it
 * has been processed.
 *
 * @param array $plugin
 *   An associative array defining a plugin.
 * @param array $info
 *   An associative array of plugin type info.
 */
function hook_plugin_manager_post_alter(array &$plugin, array &$info) {
  // Override a function defined by the plugin.
  if ($info['type'] == 'my_type') {
    $plugin['my_function'] = 'new_function';
  }
}

/**
 * @} End of "addtogroup hooks".
 */
