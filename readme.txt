=== WordPress Hierarchy Navigation ===
Contributors: Joe Tercero
Tags: navigation, section, cms, pages, top level, hierarchy
Requires at least: 4.3
Tested up to: 4.7.4
Stable tag: 4.3

Adds a widget and shortcode for page hierarchy based navigation. Includes simple function for template developers.

== Description ==

Adds a widget and shortcode to your theme for hierarchy based navigation.

Shows all page siblings (except on the top level page if disabled), all parents and grandparents (and higher), the siblings of all parents and grandparents (up to top level page), and any immediate children of the current page. Can also be called by a function inside template files.

It includes a simple widget configuration panel. From this panel you can:

1. Override standard behavior and have the widget show all pages in the current section
2. Determine whether the widget should show the top level page
3. Provide a list of pages to exclude from the output
4. Determine whether the widget should show all top level pages
5. Use a specific widget title (i.e. In This Section), or just use the top level page title
6. Include page thumbnails in the menu
7. Determine page sort order (defaults to menu order)

The widget uses standard WordPress navigation classes, in addition to a unique class around the widget, for easy styling.

== Installation ==

1. Install easily with the WordPress plugin control panel or manually download the plugin and upload the extracted
folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Widget users can add it to the sidebar by going to the "Widgets" menu under "Appearance" and adding the "WPHN Page Hierarchy" widget. Open the widget to configure.
1. Template authors can call the navigation by using the `the_wphn_page_heirarchy` function. The function accepts a single
argument in the form of a classical WordPress set of parameters.

= Shortcode Parameters =

* `title` - Provide a specific widget title; (default: '')
* `show_all` - Always show all pages in current section (default: false)
* `exclude` - Page IDs, separated by commas, of pages to exclude (default: '')
* `sort_by` - Page sort order; can use any WordPress page list sort value (default: menu_order)
* `show_top_page` - Show top level page in navigation (default: false)
* `show_top_level` - Show all top level pages in navigation (default: false)
* `top_page_as_title` - Replace the Widget Title with the Top Page Title (default: '')
* `featured` - Show page thumbnail (default: none)
* `before_shortcode` - HTML before shortcode (default: `<nav>`)
* `after_shortcode` - HTML after shortcode (default: `</nav>`)
* `before_title` - HTML before shortcode (default: `<h2 class="shortcode-title">`)
* `after_title` - HTML after shortcode (default: `</h2>`)

= Template Function Parameters =

* `title` - Provide a specific widget title; (default: '')
* `show_all` - Always show all pages in current section (default: false)
* `exclude` - Page IDs, separated by commas, of pages to exclude (default: '')
* `sort_by` - Page sort order; can use any WordPress page list sort value (default: menu_order)
* `show_top_page` - Show top level page in navigation (default: false)
* `show_top_level` - Show all top level pages in navigation (default: false)
* `top_page_as_title` - Replace the Widget Title with the Top Page Title (default: '')
* `featured` - Show page thumbnail (default: none)
* `before_widget` - HTML before shortcode (default: `<nav>`)
* `after_widget` - HTML after shortcode (default: `</nav>`)
* `before_title` - HTML before shortcode (default: `<h2 class="widget-title">`)
* `after_title` - HTML after shortcode (default: `</h2>`)

= Example =

`the_wphn_page_heirarchy( array(
	'before_widget' => '<li>',
	'after_widget' => '</li>',
	'exclude' => 2,
	'show_top_page' => 1)
);`

Will wrap the widget in LI tags, exclude page ID 2, and will output on home page.


== Screenshots ==



== Changelog ==

= 1.1 =
* Update - Re-Factored core function class and widget class
* Update - Updated Walker Class with new parameters
* Add - Shortcode, [wphn_page_menu]
* Add - Nav output now displays custom post hierarchies in addition to default pages

= 1.0 =
* Initial Release

= Future features =
* Lists private pages if user has permission to see them
