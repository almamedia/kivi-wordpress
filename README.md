# KIVI WP-plugin v2 REST
- Requires WordPress version at least: 5.0
- Tested WordPress version up to: 5.8.0
- License: GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)

- *PHP >= 7.1 required*, 7.4 tested
- no BasicAuth ( blocks async request )

- Kivi REST-API credentials with 200 hourly request and read capabilities to endpoints /realties/homepage, /realties/homepage/uiformat and /purchase-announcements/homepage (first run with large amount of properties might need more, about 210% of properties count)

## Description

A plugin for displaying KIVI real estate system properties on a WordPress site. With the plugin, simple index and single templates are provided for listing and displaying the properties.

Demo / POC available: https://kohdelista.wpengine.com/demoaineisto/kohde/ This is an out-of-the-box Wordpress-installation with Kivi-plugin and WP default theme activated.

KIVI Wordpress plugin imports KIVI data into Wordpress as a background process. The plugin is scheduled to read the modified items in the KIVI system four times in an hour. New items are added, deleted items are deleted and updated items are updated. In WordPress, the KIVI items are stored ad a custom post type named `kivi_item`. Property images are stored asurls to image files CDN, hosted on Kivi. Kivi_item posts are not supposed to be edited using the WordPress editor as the scheduled update from the KIVI system will overwrite any changes if the item is changed in KIVI.

For getting the correct export from KIVI system, a REST-API credentials should be opened first. For more information, contact the sales or the customer support kivi@almamedia.fi. 

In case of technical issues, contact technical support at kivi-tekniikka@almamedia.fi

## Installation

Just drop extract the package in the `plugins` directory of your WP installation. 'KIVI items' and 'KIVI' tabs will appear in the dashboard. 'KIVI' is the admin area, 'KIVI items' will list the imported items.

It could better to implement system cron instead of default WP-cron (interval 15 minutes or less). See https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/
This is not a requirement. With a low traffic website, there might be too much time between syncs and some data updates might be skipped.

## Settings

- REST: User name and password. These are for connecting to Kivi. For testing and developing, use `demo-kohdelista` `LZrz@MP*7t`

- Brand color. This will change some the colors of the plugin provided template page. For real customisations and theme like look and feel, a separate template for KIVI items should be implemented on the template directory.

- Slug. Define a slug for the kivi_item post type. This is basically a piece of the url structure for your KIVI-items. For example in item url http://www.example.com/kohde/5hks-tampere-jokupolku-4/ the 'kohde' would be the slug. Also the list of all the items can be accessed using the url `<site url>/<slug>`.

- Google maps api key. If set, a Google maps component is shown on the item page and the property is pinned on the map. The location is based on coordinates set in KIVI system or the address geocoded using Google's Geocoding API.

- Filter items on import: It is possible to set a key-value -pair that has to be true to an item to be imported. For example, only import items from a single town, use key `_municipality` and value `Tampere`.  

- Reset. This will remove all KIVI items. To disable schedules updates from happening, set invalid credentials.
- Save settings. This will save the settings.

## Adapting to a theme

The plugin has default templates for listing properties and displaying a single item. The templates might work with your theme just fine, or not. You can place your own templates in your theme's directory and these will override the ones defined by the plugin. The files sould be `kivi-index-template.php` (listing page template), `kivi-single-template.php`, `kivi-single-item-part.php` (single item in listing loop) and `kivi-purchase-announcement.php`. You can of course use the default templates as a starting point for your own templates, the files are located in `includes/partials`.

### Displying data for item

Some meta data is stored as WP post meta with a "_"-prefix in key. For example "municipality" becomes "_municipality" and it's value is shown in template file with code: `get_post_meta( get_the_ID(), '_municipality', true )`. You can see all possible data for an item in wp-admin edit kivi_item screen. 

## Shortcodes defined by the plugin

You can use shortcodes to display KIVI data in your own custom WordPress pages by adding a shortcode defined by the plugin in the content of the page.

### List items by any attribute
`[kivi]` Lists all items

There is a template file `/includes/partials/kivi-single-item-part.php` which is used to display
single item in listings, both kivi_item archive index and shortcode listing. You can create
your own custom template in (child) theme root with same file name.

A simple meta data field can be a filter attribute. See data fields and values from any kivi_item edit screen and choose attributes to use.

#### Examples
`[kivi realtytype="rivitalo"]`
Lists townhouses.

`[kivi ui_TRADE_TYPE="Vuokra" realtytype="rivitalo"]`
Lists townhouses for rent.

`[kivi itemgroup="vuokra-asunnot"]`
Lists apartments for rent.


## Updating plugin
Replace all plugin files by FTP.

If using WP admin, follow these steps:

1. Get new version from Github as zip file.
2. Add Kivi as new plugin by uploading the zip file.
3. Wordpress will ask about updating current plugin ( if using same branch )
4. Ready. Settings and items are kept in database.

If you are updating to different branch, disable old version first. Settings are kept and shared between plugin instances.

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

**2.0.0**
- Heavy recode: use Kivi REST-API instead of XML
- Use UI-formatted -data from API instead of raw data 
- Display images using CDN instead of downloading them to media library

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
