# Implement a plugin

There are two parts to implementing a plugin: telling the system where it is,
and implementing one or more `.inc` files that contain the plugin data.

## Telling the system where your plugins live

### How a module implements plugins

To implement any plugins at all, you must implement a single function for all
plugins: `hook_plugin_manager_directory`. Every time a module loads plugins,
this hook will be called to see which modules implement those plugins and in
what directory those plugins will live.

```php
function hook_plugin_manager_directory($module, $plugin) {
  if ($module == 'coffeemaker' && $plugin == 'brewer_types') {
    return 'plugins/brewer_types';
  }
}
```

The directory returned should be relative to your module. Another common usage
is to simply return that you implement all plugins owned by a given module (or
modules):

```php
function hook_plugin_manager_directory($module, $plugin) {
  if ($module == 'coffeemaker') {
    return 'plugins/' . $plugin;
  }
}
```

Typically, it is recommended that all plugins be placed into the 'plugins'
directory for clarity and maintainability. Inside the directory, any number of
subdirectories can be used. For plugins that require extra files, such as
templates, css, javascript or image files, this is highly recommended:

```text
mymodule.module
mymodule.info
plugins/
    brewer_types/
        my_brewer.inc
    grinder_types/
        my_grinder.inc
        my_grinder.css
        my_grinder.tpl.php
        my_grinder_image.png
```

### How a theme implements plugins**

Themes can implement plugins if the plugin owner specified that it's possible in
its `hook_plugin_manager_plugin_type()` call. If so, it is generally exactly
the same as modules, except for one important difference: themes don't get
`hook_plugin_manager_directory()`. Instead, themes add a line to their `.info`
file:

```text
plugins[module][type] = directory
```

### How to structure the .inc file

The top of the `.inc` file should contain an array that defines the plugin.
This array is simply defined in the global namespace of the file and does not
need a function. Note that previous versions of this plugin system required a
specially named function. While this function will still work, its use is now
discouraged, as it is annoying to name properly.

This array should look something like this:

```php
$plugin = array(
  'key' => 'value',
);
```

Several values will be filled in for you automatically, but you can override
them if necessary. They include 'name', 'path', 'file' and 'module'.
Additionally, the plugin owner can provide other defaults as well.

There are no required keys by the plugin system itself. The only requirements
in the `$plugin` array will be defined by the plugin type.

After this array, if your plugin needs functions, they can be declared.
Different plugin types have different needs here, so exactly what else will be
needed will change from type to type.
