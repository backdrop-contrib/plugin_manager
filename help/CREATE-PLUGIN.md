# Create a plugin

There are two primary pieces to using plugins. The first is getting the data,
and the second is using the data.

## Defining a plugin

To define that you offer a plugin that modules can implement, you first must
implement `hook_plugin_manager_plugin_type()` to tell the plugin system about
your plugin.

```php
/**
 * Implements hook_plugin_manager_plugin_type() to inform plugin_manager about
 * the layout plugin.
 */
function coffeemaker_plugin_manager_plugin_type() {
  $plugins['brewer_type'] = array(
    'load themes' => TRUE,
  );

  return $plugins;
}
```

The following information can be specified for each plugin type:

### cache

*Defaults to:* `FALSE`

If set to `TRUE`, the results of `plugin_manager_get_plugins` will be cached in
the `'cache'` table (by default), thus preventing `.inc` files from being
loaded. `plugin_manager_get_plugins` looking for a specific plugin will always
load the appropriate `.inc` file.

### cache table

*Defaults to:* `'cache'`

If `'cache'` is `TRUE`, then this value specifies the cache table where the
ached plugin information will be stored.

### classes

*Defaults to:* `array()`

An array of *class identifiers*(i.e. plugin array keys) which a plugin of this
type uses to provide classes to the plugin_manager. For example, if `classes` is
set to array('class'), then plugin_manager will search each `$plugin['class']`
for a class to load. A *class identifier* must be the class name. Then the class
should be listed in an implementation of `hook_autoload_info()` like this:

```php
/**
 * Implements hook_autoload_info().
 */
function coffeemaker_autoload_info() {
  return array(
    'pluginBurrGrinder' => 'plugins/grinder_type/pluginBurrGrinder.php',
    'pluginDripBrewer' => 'plugins/brewer_type/pluginDripBrewer.php',
  );
}
```

### defaults

*Defaults to:* `array()`

An array of defaults that should be added to each plugin; this can be used to
ensure that every plugin has the basic data necessary. These defaults will not
ovewrite data supplied by the plugin. This could also be a function name, in
which case the callback will be used to provide defaults. NOTE, however, that
the callback-based approach is deprecated as it is redundant with the 'process'
callback, and as such will be removed in later versions. Consequently, you
should only use the array form for maximum cross-version compatibility.

### load themes

*Defaults to:* `FALSE`

If set to TRUE, then plugins of this type can be supplied by themes as well as
modules. If this is the case, all themes that are currently enabled will provide
a plugin: NOTE: Due to a slight UI bug in Backdrop, it is possible for the
default theme to be active but not enabled. If this is the case, that theme will
NOT provide plugins, so if you are using this feature, be sure to document that
issue. Also, themes set via $custom_theme do not necessarily need to be enabled,
but the system has no way of knowing what those themes are, so the enabled flag
is the only true method of identifying which themes can provide layouts.

### hook

*Defaults to:* (dynamic value)

The name of the hook used to collect data for this plugin. Normally this is
`$module . '_' . $type` -- but this can be changed here. If you change this, you
MUST be sure to document this for your plugin implementors as it will change the
format of the specially named hook.

### process

*Defaults to:* `''`

An optional function callback to use for processing a plugin. This can be used
to provide automated settings that must be calculated per-plugin instance (i.e.,
it is not enough to simply append an array via 'defaults'). The parameters on
this callback are: `callback(&$plugin, $info)` where $plugin is a reference to
the plugin as processed and $info is the fully processed result of
`hook_plugin_manager_api_info()`.

### extension

*Defaults to:* `'inc'`

Can be used to change the extension on files containing plugins of this type. By
default the extension will be "inc", though it will default to "info" if "info
files" is set to true. Do not include the dot in the extension if changing it,
that will be added automatically.

### info file

*Defaults to:* `FALSE`

If set to TRUE, then the plugin will look for a .info file instead of a .inc.
Internally, this will look exactly the same, though obviously a .info file
cannot contain functions. This can be good for styles that may not need to
contain code.

### use hooks

*Defaults to:* `TRUE`

Use to enable support for plugin definition hooks instead of plugin definition
files. NOTE: using a central plugin definition hook is less optimal for the
plugins system, and as such this will default to FALSE in later versions.

### child plugins

*Defaults to:* `FALSE`

If set to TRUE, the plugin type can automatically have 'child plugins' meaning
each plugin can actually provide multiple plugins. This is mostly used for
plugins that store some of their information in the database, such as views,
blocks or exportable custom versions of plugins.

To implement, each plugin can have a 'get child' and 'get children' callback.
Both of these should be implemented for performance reasons, since it is best
to avoid getting all children if necessary, but if 'get child' is not
implemented, it will fall back to 'get children' if it has to.

Child plugins should be named parent:child, with the : being the separator, so
that it knows which parent plugin to ask for the child. The 'get children'
method should at least return the parent plugin as part of the list, unless it
wants the parent plugin itself to not be a choosable option, which is not
unheard of.

'get children' arguments are `($plugin, $parent)` and `'get child'` arguments
are `($plugin, $parent, $child)`.

In addition, there is a 'module' and 'type' settings; these are for internal use
of the plugin system and you should not change these.

## Getting the data

To create a plugin, a module only has to execute `plugin_manager_get_plugins()`
with the right data:

```php
  plugin_manager_get_plugins($module, $type, [$id]);
```

In the above example, `$module` should be your module's name and `$type` is the
type of the plugin. It is typically best practice to provide some kind of
wrapper function to make this easier. For example, coffeemaker module provides the
following functions to implement the 'brewer_type' plugin:

```php
/**
 * Fetch metadata on a specific brewer_type plugin.
 *
 * @param string $brewer_type
 *   Name of a brewer type.
 *
 * @return array
 *   An array with information about the requested brewer type.
 */
function coffeemaker_get_brewer_type($brewer_type) {
  return plugin_manager_get_plugins('coffeemaker', 'brewer_type', $brewer_type);
}

/**
 * Fetch metadata for all brewer type plugins.
 *
 * @return
 *   An array of arrays with information about all available coffeemaker
 *   brewer types.
 */
function coffeemaker_get_brewer_types() {
  return plugin_manager_get_plugins('coffeemaker', 'brewer_types');
}
```

## Using the data

Each plugin returns a packet of data, which is added to with a few defaults.
Each plugin is guaranteed to always have the following data:

### name

The name of the plugin. This is also the key in the array, of the full list of
plugins, and is placed here since that is not always available.

### module

The module that supplied the plugin.

### file

The actual file containing the plugin.

### path

The path to the file containing the plugin. This is useful for using secondary
files, such as templates, css files, images, etc, that may come with a plugin.

Any of the above items can be overridden by the plugin itself, though the most
likely one to be modified is the 'path'.

The most likely data (beyond simple printable data) for a plugin to provide is a
callback. The plugin system provides a pair of functions to make it easy and
consistent for these callbacks to be used. The first is
`plugin_manager_get_function()`, which requires the full `$plugin` object.

```php
/**
 * Get a function from a plugin, if it exists. If the plugin is not already
 * loaded, try plugin_manager_load_function() instead.
 *
 * @param $plugin
 *   The loaded plugin type.
 * @param $callback_name
 *   The identifier of the function. For example, 'settings form'.
 *
 * @return
 *   The actual name of the function to call, or NULL if the function
 *   does not exist.
 */
function plugin_manager_get_function($plugin, $callback_name)
```

The second does not require the full `$plugin` object, and will load it:

```php
/**
 * Load a plugin and get a function name from it, returning success only
 * if the function exists.
 *
 * @param $module
 *   The module that owns the plugin type.
 * @param $type
 *   The type of plugin.
 * @param $id
 *   The id of the specific plugin to load.
 * @param $callback_name
 *   The identifier of the function. For example, 'settings form'.
 *
 * @return
 *   The actual name of the function to call, or NULL if the function
 *   does not exist.
 */
function plugin_manager_load_function($module, $type, $id, $callback_name) {
```

Both of these functions will ensure any needed files are included. In fact, it
allows each callback to specify alternative include files. The plugin
implementation could include code like this:

```php
  'callback_name' => 'actual_name_of_function_here',
```

Or like this:

```php
  'callback_name' => array(
    'file' => 'filename',
    'path' => 'filepath', // optional, will use plugin path if absent
    'function' => 'actual_name_of_function_here',
  ),
```

An example, for 'plugin_example' type

```php
$plugin = array(
  'name' => 'my_plugin',
  'module' => 'my_module',
  'example_callback' => array(
    'file' => 'my_plugin.extrafile.inc',
    'function' => 'my_module_my_plugin_example_callback',
  ),
);
```

To utilize this callback on this plugin:

```php
if ($function = plugin_manager_get_function($plugin, 'example_callback')) {
  $function($arg1, $arg2, $etc);
}
```

## Document your plugins

Since the data provided by your plugin tends to be specific to your plugin type,
you really need to document what the data returned in the hook in the .inc file
will be or nobody will figure it out. Use advanced help and document it there.
If every system that utilizes plugins does this, then plugin implementors will
quickly learn to expect the documentation to be in the advanced help.
