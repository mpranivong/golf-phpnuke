=== NextGEN Custom Fields ===
Contributors: shauno
Donate link: http://shauno.co.za/donate/
Tags: nextgen-gallery, nextgen, custom, fields, ngg-custom-fields, nextgen-gallery-custom-fields
Requires at least: 2.7.1
Tested up to: 3.8.1
Stable tag: 1.2.2

Creates the ability to quickly and easily add custom fields to NextGEN Galleries and Images.

== Description ==

This plugin was developed to add custom fields to the excellent and popular NextGEN Gallery plugin. Simply enter the name of your new field(s), select between "input", "textarea" or "dropdown", and the field(s) will be automatically added to the "Manage Gallery" screens in the NGG dashboard.

Please note, you do need to add a small tag to the NGG templates to get your custom fields showing in your theme, **so please do read the FAQ**.

== Installation ==

1. Unzip the plugin to your `wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Have a look at the FAQ to see how to work this thing

== Frequently Asked Questions ==

= Firstly, what exactly will this plugin do? =

Have you ever wanted to add just a little more information about a picture in your NextGEN Gallery?  Maybe you wanted to add the name of the photographer?  Or where the picture was taken?  Well, with this plugin you can add as many extra custom fields to the images as you need, and stop you trying to squeeze all the info into the description.

= How do I add custom fields =

After activation the plugin, there will be a new main menu item in the WordPress dashboard, labeled "NGG Custom Fields". If you open this menu item, you will be able to add "Gallery Custom Fields", or "Image Custom Fields".

"Gallery Custom Fields" are used to add information about the gallery as a whole, while Image "Custom Fields" are used for adding information about each individual image. When you add a custom field from this screen, you select which of your NGG galleries it is linked to.

= After adding custom fields, how do I set its value? =

* Click "Manage Gallery" from under the "Gallery" menu option on the left of WordPress
* Choose a gallery to manage
* All gallery custom fields linked to this gallery should show in the "Gallery settings" section of the page under a heading of "Custom Fields"
* All image custom fields linked to this gallery should show in columns to the right of each image.

= How do I show the fields in my galleries? =

So once you’ve created custom fields linked to your galleries, and inputted the values in NGG’s Manage Gallery screen, you’ll want to display these fields in your galleries on your site.

* For "Image" custom fields - You place the following tag in the gallery template: `<?php echo nggcf_get_field($image->pid, "Your Image Field Name"); ?>`
* For "Gallery" custom fields - You place the following tag in the gallery template: `<?php echo nggcf_get_gallery_field($gallery->ID, "Your Gallery Field Name"); ?>`

For NGG 1.x, you can find the gallery templates in the `/plugins/nextgen-gallery/view/` directory. For the default shortcode, `[nggallery id=x]`, you
add the tag to the `gallery.php` file. If you add the `template` attribute to your shortcode, you need to alter the appropriate template. eg:
If you use the `[nggallery id=x template="caption"]`, you need to add the tag to the `gallery-caption.php` template.

For NGG version 2.x, you need to add the tag to the `/nextgen-gallery/products/photocrati_nextgen/modules/nextgen_basic_gallery/templates/thumbnails/index.php`
template. This will only work for galleries inserted WITHOUT selecting a template from the gallery display options.
NGG v2.x templating system can be confusing for some users. Please see [this page](http://shauno.co.za/nextgen-gallery-v2-display-template-locations/) for more info on which templates are used under different settings.

Please be aware, for “Image Custom Fields", the tag needs to be placed inside the `foreach()` loop in the template, as that loop is outputting each image.
For “Gallery Custom Fields", be sure to place the tag outside of the foreach, or it will show for each image.

= Common Problems =

* Make sure you add the fields you want to the correct place.  Image fields added from the "Image Custom Fields" menu option and gallery fields from the "Gallery Custom Fields" option.  Sounds simple, but you can overlook it.

* NGG 2.x introduced template caching, so sometimes you need to clear the cache to get the custom fields showing in your gallery. Go to "Gallery->Other Options->Miscellaneous" and click the "Clear image cache" button, the refresh your gallery page.

* If you have unusual characters in your fields **names**, it can break the output.  Stick to upper and lower case letters, and numbers to avoid any issues.  As of version 0.5 there is some basic sanitation done to the names of fields and their values, but it is far from perfect.  It should allow characters such as apostrophes well enough though.

== Changelog ==

= 1.2.2 =
* Updated call to `add_menu_page()` and `add_submenu_page()` changing capability to 'manage_options' from deprecated user level 8 (thanks to ksemel for the report)

= 1.2.1 =
* Changed `field_value` column back to TEXT from VARCHAR(255), to allow more than 255 characters saved (thanks to ksemel for the find and bug report)

= 1.2 =
* Converted table to UTF8. This is much more compatible with internet safe languages.
* Fixed quotes in field names and values

= 1.1.3 =
* Made gallery fields not rely on having an image custom field and JavaScript
* Refactored some code to just clean it up a little

= 1.1.2 =
* I screwed up the backwards compatibility, sorry.  Use 1.0.2 for < NGG 1.7

= 1.1.1 =
* Made it backwards compatible with NGG 1.6.x and lower. Should have been done with the last update, but I was spaced on pain meds

= 1.1 =
* Made this plugin compatibile with NextGEN Galley 1.7.x and greater, which breaks compatibility with lower versions of NGG

= 1.0.2 =
* Fixed a bug that would break gallery custom fields (textareas) if you had new lines in them.  (thanks mygraphicfriend again for pointing it out)

= 1.0.1 =
* Fixed a bug that would delete all data for a custom field (all galleries or images in ngg), when it was unlinked from only a specific gallery (thanks to mygraphicfriend for pointing that out)

= 1.0 =
* Added the ability to link custom images when creating a new gallery. (Needs NextGEN 1.4.0 or later, thanks to maxx10 for the request)

= 0.6 =
* Added the ability to select which galleries to link fields to (thanks to vividlilac and goto10 for pushing me into doing that)
	
= 0.5 =
* Added a little sanitation to field names and values, to allow apostrophes and some other none alphanumeric characters
* Added the ability to edit a field's name
	
= 0.4 =
* Added gallery custom fields
* Fixed a bug that stopped the deletion of a custom field unless there was data saved for that field
	
= 0.3 =
* Added the ability to edit drop down options on existing fields
* Added the "Change Log" section to this file :)
	
= 0.2 =
* Minor code reformat
	
= 0.1 =
* Initial release!

== Upgrade Notice ==

= 1.2.1 =
Fixed field values to allow more than 255 characters.

= 1.2 =
Fixed quotes in field names and values. Also converted database tables to UTF8. This is much more compatible with internet safe languages. You may see extra backslashes in some values if you had quotes saved before. Just remove them and re-save.
