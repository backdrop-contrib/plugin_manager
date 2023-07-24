<?php

/**
 * @file
 * Plugin Manager include using a plugin array to declare a plugin.
 */

/**
 * Plugin array plugin definition.
 */
$plugin = array(
  'function' => 'plugin_test_plugin_array_cached_test',
  'handler' => array(
    'class' => 'pluginCachedPluginArray',
  ),
);

/**
 * Plugin array function plugin.
 */
function plugin_test_plugin_array_cached_test() {
}
