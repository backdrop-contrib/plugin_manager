<?php

/**
 * @file
 * Plugin Manager include using a plugin array to declare a plugin.
 */

/**
 * Plugin array plugin definition.
 */
$plugin = array(
  'function' => 'plugin_test_plugin_array2_not_cached_test',
  'handler' => array(
    'class' => 'pluginNotCachedPluginArray2',
  ),
);

/**
 * Plugin array function plugin.
 */
function plugin_test_plugin_array2_not_cached_test() {
}
