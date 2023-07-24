# Plugin API

APIs are a form of plugins that are tightly associated with a module. Instead
of a module providing any number of plugins, each module provides only one file
for an API and this file can contain hooks that the module should invoke.

Modules support this API by implementing
`hook_plugin_manager_api($module, $api)`. If they support the API, they return a
packet of data:

```php
function mymodule_plugin_manager_api($module, $api) {
  if ($module == 'some module' && $api = 'some api') {
    return array(
      // The minimum API version this system supports. If this API version is
      // incompatible then the .inc file will not be loaded:
      'version' => 1,
      // Where to find the file. Optional; if not specified it will be the
      // module's directory:
      'path' => 'alternative_path',
      // An alternative version of the filename. If not specified it will be
      // $module.$api.inc:
      'file' => 'alternative_filename.php'
    );
  }
}
```

This implementation must be in the `.module` file.

Modules utilizing this can invole `plugin_manager_api_include()` in order to
ensure all modules that support the API will have their files loaded as
necessary. It's usually easiest to create a small helper function like this:

```php
define('MYMODULE_MINIMUM_VERSION', 1);
define('MYMODULE_VERSION', 1);

function mymodule_include_api() {
  return plugin_manager_api_include('mymodule', 'myapi', MYMODULE_MINIMUM_VERSION, MYMODULE_VERSION);
}
```

Using a define will ensure your use of version numbers is consistent and easy
to update when you make API changes. You can then use the usual module_invoke
type commands:

```php
mymodule_include_api();
module_invoke('myhook', $data);
```

If you need to pass references, this construct is standard:

```php
foreach (mymodule_include_api() as $module => $info) {
  $function = $module . '_hookname';
  // Just because they implement the API and include a file does not guarantee they implemented
  // a hook function!
  if (!function_exists($function)) {
    continue;
  }

  // Typically array_merge() is used below if data is returned.
  $result = $function($data1, $data2, $data3);
}
```

TODO: There needs to be a way to check API version without including anything,
as a module may simply provide normal plugins and versioning could still matter.
