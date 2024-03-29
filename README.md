For new installation, you should use https://github.com/almamedia/kivi-wordpress/tree/REST-API-instead-XML (v2 REST)

# KIVI WP-plugin v1 XML
- Requires at least: 4.0
- Tested up to: 5.6.0
- Stable tag: 1.1.2
- License: GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)

- *PHP >= 5.6.0 required*, 7.4 tested
- php GD support required
- php DOM required (http://php.net/manual/en/book.dom.php)
- php settings:
	-	”allow_url_fopen” On
	-	”memory limit” at least 128M
	-	”max_execution_time” at least 30
- enough memory on server to create image thumbnails (2GB+)
- no BasicAuth ( blocks async request )


## Description

A plugin for displaying KIVI real estate system properties on a WordPress site. The plugin is not meant to be out-of-the box solution
for the job, but instead a starting point for development. With the plugin, simple index and single templates are provided for listing and displaying the properties.

KIVI Wordpress plugin imports KIVI data into Wordpress as a background process. The plugin is scheduled to read the active items in the KIVI system every hour. New items are added, deleted items are deleted and updated items are updated. In WordPress, the KIVI items are stored ad a custom post type named `kivi_item`. Item images are stored in the WordPress media gallery and referenced in the kivi_item posts. Kivi_item posts are not supposed to be edited using the WordPress editor as the scheduled update from the KIVI system will overwrite any changes if the item is changed in KIVI.

For getting the correct export from KIVI system, a specific WordPress transfer needs to be opened first. For more information, contact the sales or the customer support kivi@almamedia.fi. 

In case of technical issues, contact technical support at kivi-tekniikka@almamedia.fi

## Installation

Just drop extract the package in the `plugins` directory of your WP installation. 'KIVI items' and 'KIVI' tabs will appear in the dashboard. 'KIVI' is the admin area, 'KIVI items' will list the imported items.

## Settings

- XML address for importing. This you will get from the KIVI customer service. This is customer specific and will include all the items for a specific customer.

- Brand color. This will change some the colors of the plugin provided template page. For real customisations and theme like look and feel, a separate template for KIVI items should be implemented on the template directory.

- Slug. Define a slug for the kivi_item post type. This is basically a piece of the url structure for your KIVI-items. For example in item url http://www.example.com/kohde/5hks-tampere-jokupolku-4/ the 'kohde' would be the slug. Also the list of all the items can be accessed using the url `<site url>/<slug>`.

- 'Use www sized images in transfer (default: original)' Use www-size images when transferring the items to WP. Www-sized are smaller than originals. This is useful for in development phase as the www images download quicker. Using originals in production is a good idea.

- Clean values. Alters property data to look better. Replaces "m2" with "m²", addds euro sign to prices and formats numbers. For additional formatting, use the filter "kivi_viewable_value" ( with up to three attributes: value, label, properties).

- Google maps api key. If set, a Google maps component is shown on the item page and the property is pinned on the map. The location is based on coordinates set in KIVI system or the address geocoded using Google's Geocoding API.

- Reset. This will reset the settings and remove all KIVI items.
- Save settings. This will (obviously) save the settings.

## Adapting to a theme

The plugin has default templates for listing properties and displaying a single item. The templates might work with your theme just fine, or not. You can place your own templates in your theme's directory and these will override the ones defined by the plugin. The files sould be `kivi-index-template.php` and `kivi-single-template.php` for index page and single item page, respectively. You can of course use the default templates as a starting point for your own templates, the files are  `includes/partials/kivi-index-template.php` and `includes/partials/kivi-single-template.php`.

### Displying data for item

All data is stored as WP post meta with a "_"-prefix in key. For example "constructionright" becomes "_constructionright" and it's value is shown in template file with code: `get_post_meta( get_the_ID(), '_constructionright', true )`

## Shortcodes defined by the plugin

You can use shortcodes to display KIVI data in your own custom WordPress pages by adding a shortcode defined by the plugin in the content of the page.

### List items by any attribute
`[kivi]` Lists all items

There is a template file `/includes/partials/kivi-single-item-part.php` which is used to display
single item in listings, both kivi_item archive index and shortcode listing. You can create
your own custom template in (child) theme root with same file name.

Any meta data field can be a filter attribute. See data fields and values from any kivi_item edit screen and choose attributes to use.

#### Examples
`[kivi assignment_type="myynti" realtytype_id="rivitalo"]`
Lists townhouses for sale.

`[kivi assignment_type="vuokra" realtytype_id="rivitalo"]`
Lists townhouses for rent.

`[kivi itemgroup_id="vuokra-asunnot"]`
Lists apartments for rent.


### List items by town

`[kunta nimi='<city>']`

for example:
`[kunta nimi='Tampere']`

### List items by housing company
`[taloyhtio nimi='<housing company name>']`

for example:
`[taloyhtio nimi='Asunto Oy Tampereen Pohtola']`

### List items by item type
`[tyyppi nimi=<item type name>]`

for example:
`[tyyppi nimi="omakotitalo"]`
or
`[tyyppi nimi="omakotitalo" toimeksianto="vuokra"]`


### List items by item group
`[tuoteryhma nimi=<item group name>]`

for example:
`[tuoteryhma nimi="toimitilat"]`

Possible item groups are: asunnot,loma-asunnot,toimitilat,tontit,vuokra-asunnot,loma-asunnot,uudiskohteet, "maa- ja metsätilat", "autotallit ja muut"

### List items by assignment type
`[toimeksianto tyyppi=<assignment type>]`

for example:
`[toimeksianto tyyppi="vuokra"]`
or
`[toimeksianto tyyppi="myynti"]`

### Changes to property info

In template files, there are functions like view_<basic/cost/additional etc.>_info( $id ), that create info tables about property.

If you need to change the table data, you can use filters: `kivi_viewable_label` and `kivi_viewable_value`, for example:

```php
// change label
add_filter( 'kivi_viewable_label', function( $label ) {
	if($label == "Myyntihinta") {
		$label = "Hinta ilman velkaosuutta";
	}
	return $label;
});
```

```php
// recreate contact info to use mailto- and tel- links.
add_filter( 'kivi_viewable_value', function( $ret, $label, $properties ){
	if( 'Yhteystiedot' == $label){
		$data = array();
		$data['name'] 	= 	get_post_meta( get_the_id(), '_iv_person_name', true);
		$data['supplier'] = get_post_meta( get_the_id(), '_iv_person_suppliername', true);
		$data['email'] 	= 	get_post_meta( get_the_id(), '_iv_person_email', true);
		$data['phone'] 	= 	get_post_meta( get_the_id(), '_iv_person_mobilephone', true);
		$data = array_map( 'esc_html', $data );

		$ret_str = "";
		$ret_str .= "<ul class='custom-kivi-contact-info'>";
		$ret_str .= "<li class='custom-kivi-contact-info-name'>$data[name]</li>";
		$ret_str .= "<li class='custom-kivi-contact-info-supplier'>$data[supplier]</li>";
		$ret_str .= "<li class='custom-kivi-contact-info-email'><a href='mailto:$data[email]'>$data[email]</a></li>";
		$ret_str .= "<li class='custom-kivi-contact-info-phone'><a href='tel:$data[phone]'>$data[phone]</a></li>";
		$ret_str .= "</ul>";
		
		return $ret_str;
	}
    return $ret;
}, 10, 3);
```

Add these to the current theme functions.php or as new plugin. 


## Updating plugin
Replace all plugin files by FTP.

If using WP admin, follow these steps:

1. Get new version from Github as zip file.
2. Add Kivi as new plugin by uploading the zip file.
3. Wordpress will ask about updating current plugin ( if using same branch )
4. Ready. Settings and items are kept in database.

If you are updating to different branch, disable old version first. Settings are kept and shared between plugin instances.

## Wordpress Core updates
1. Disable Kivi plugin.
2. Update Wordpress Core.
3. Activate Kivi plugin.
4. Ready. Settings and items are kept in database.

If property data stops updating from Kivi (this might happen after WP Core update), disable and then activate plugin once. Settings and items are kept in database.

## Recommended plugins
### DX Delete Attached Media
Helps to keep media library clean. With this plugin, media attached to items will be removed when item is deleted manually from WP admin.
If the images dowloaded by Kivi plugin are used in posts or pages, the images will stop working after the item is deleted.

### WordPress Share Buttons Plugin – AddThis
An easy way to include share buttons for Kivi item pages. Just install and configure settings.

### Some contact form plugin?
There is a need for contact form plugin, that could embed contact form to single item page and on form submit send the message to the correct email address.
The email is stored as meta data for the item ( metadata "_sc_itempage_email" ).

## Setting archive title
Plugin works with Yoast SEO and you should set better archive title in the Yoast SEO settings. ("Sisältötyypit" -> "KIVI items" -> last "SEO-title" )
or, you can use filter like this:

```php
add_filter( 'pre_get_document_title', function($title){
    if ( is_post_type_archive('kivi_item') ){
        $title = 'Kohteiden listailu - ' . get_bloginfo('name');
    }
    return $title;
}, 99 );
```

## Feature Requests and Contributing

We are not too actively developing individual features for the plugin as it's meant to be a starting point for development anyways. However, we do fix bugs and for example add support for new data if such data appears in the source system. If there are specific needs you can of course contact our sales.

We are also happy to accept any pull requests if they are generic enough and seem fit for our users.

## Changelog

**1.1.2**
- Fix image ordering on item data update
- Fix euro sign
- Use wp_remote_get instead of file_get_contents

**1.1.1**
- Refactor and fix warnings on background process

**1.1.0**
- Introduce new cleaner layout and lots of improvements for developers

**1.0.10**
- Add support for featured image change among images already uploaded to Kivi and WP

**1.0.9**
- Minor refactor

**1.0.8**
- Make unique id selections more strict and fix code to work more as expected
- Add missing property information
- Fix pagination on default index template

**1.0.7**
- Added video/3d links
- Better dublicate handling
- show next sync time or sync-off notice in admin view

**1.0.6**
- Better handling for a situation when there are no items to display.

**1.0.5**
- Changed default item order in listing. (merged branch list-order)
- Fixed data update to work like it is specified in readme. All item data, including post_content and post_title, will now update from Kivi.

**1.0.4**
- Added possibility for prefiltering items while importing

**1.0.3**
- Missing assets of slick carousel (Issue #3)
- More specific css selectors for admin css (Issue #2)
- Shortcode for listing based on assignment type (PR #1)

**1.0.2**
- Added shortcode for listing items based on item type. Patch submitted by @karikauppinen

**1.0.1**
- Fix scheduling initalialization (the scheduling did not work at all)
- Change scheduling interval to 15 minutes

**1.0.0**
- Initial version
