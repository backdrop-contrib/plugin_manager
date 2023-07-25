# Plugin Manager

Provides API to make it easy for modules to implement plugins.

The plugins tool allows a module to allow *other* modules (and themes!) to
provide plugins which provide some kind of functionality or some kind of task.
For example, in Encrypt there are several types of plugins: encryption methods
and key providers. Each plugin is represented by a .inc file, and the
functionality they offer can differ wildly.

A module which uses plugins can implement a hook describing the plugin (which is
not necessary, as defaults will be filled in) and then calls a function
which loads either all the known plugins (used for listing/choosing) or a
specific plugin (used when it's known which plugin is needed). From the
perspective of the plugin system, a plugin is a packet of data, usually some
printable info and a list of callbacks. It is up to the module implementing
plugins to determine what that info means and what the callbacks do.

A module which implements plugins must first implement the
`hook_plugin_directory` function, which simply tells the system
which plugins are supported and what directory to look in. Each plugin will then
be in a separate .inc file in the directory supplied. The .inc file will contain
a specially named hook which returns the data necessary to implement the plugin.

Read more under `/help/` in this project's files.

## Installation

Install this module using the official Backdrop CMS instructions at
<https://backdropcms.org/guide/modules>.

## License

This project is GPL v2 software. See the LICENSE.txt file in this directory for
complete text.

## Maintainers

* [herbdool](https://github.com/herbdool)
* Seeking co-maintainers.

## Credits

Ported to Backdrop by [herbdool](https://github.com/herbdool).

This module is based on a part of the Chaos Tool Suite module for Drupal,
originally written and maintained by a large number of contributors, including:

* [merlinofchaos](https://www.drupal.org/u/merlinofchaos)
