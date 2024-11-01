=== WP Aritic ===
Author: Aritic
Contributors: aritic,hideokamoto_,shulard_,escopecz_,arulraj
Tags: marketing, automation
Tested up to: 5.3.2
Requires at least: 4.6
Stable tag: 2.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

[Aritic](http://aritic.com) WordPress Plugin inserts Aritic tracking image and forms to the WP website. Your Aritic instance will be able to track information about your visitors that way.

## Key features
- You don't have to edit source code of your template to insert tracking code.
- Plugin adds additional information to tracking image URL so you get better results than using just plain HTML code of tracking image.
- You can use Aritic form embed with shortcode described below.
- You can choose where the script is injected (header / footer).
- Tracking image can be used as fallback when JavaScript is disabled.

## Configuration

Once installed, the plugin must appared in your plugin list :

1. Enable it.
2. Go to the settings page and set your Aritic instance URL.

And that's it !

## Usage

### Aritic Tracking Script

Tracking script works right after you finish the configuration steps. That means it will insert the patc.js` script from your Aritic instance. You can check HTML source code (CTRL + U) of your WP website to make sure the plugin works. You should be able to find something like this:

    <script>
      (function(w,d,t,u,n,a,m){w['AriticTrackingObject']=n;
        w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
        m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
      })(window,document,'script','http://yourariticsite.com/ma/atc.js','at');

    </script>
    <script src="https://yourAriticPinPointUrl/ma/patc.js"></script>
#### Custom attributes handling

If you need to send custom attributes within Aritic events, you can use the `wparitic_tracking_attributes` filter.

    add_filter('wparitic_tracking_attributes', function($attrs) {
      $attrs['preferred_locale'] = $customVar;
      return $attrs;
    });

The returned attributes will be added to Aritic payload.

### Aritic Forms

To load a Aritic Form to your WP post, insert this shortcode to the place you want the form to appear:

    [aritic type="form" id="1"]
    [ariticform id="1"]

Replace "1" with the form ID you want to load. To get the ID of the form, go to your Aritic, open the form detail and look at the URL. The ID is right there. For example in this URL: http://youraritic.com/s/forms/view/3 the ID is 3.

### Aritic Focus

To load a Aritic Focus to your post, insert this shortcode to the place you want the form to appear:

    [aritic type="focus" id="1"]
    [ariticfocus id="1"]

Replace "1" with the focus ID you want to load. To get the ID of the focus, go to your Aritic, open the focus detail and look at the URL. The ID is right there. For example in this URL: http://youraritic.com/s/focus/3.js the ID is 3.

### Aritic Dynamic Content

To load dynamic content into your WP content, insert this shortcode where you'd like it to appear:

    [aritic type="content" slot="slot_name"]Default content to display in case of error or unknown contact.[/aritic]
    [ariticcontent slot="slot_name"]Default content to display in case of error or unknown contact.[/ariticcontent]

Replace the "slot_name" with the slot name you'd like to load. This corresponds to the slot name you defined when building your campaign and adding the "Request Dynamic Content" contact decision.

### Aritic Gated Videos

Aritic supports gated videos with Youtube, Vimeo, and MP4 as sources.

To load gated videos into your WP content, insert this shortcode where you'd like it to appear:

    [aritic type="video" gate-time="#" form-id="#" src="URL"]
    [ariticvideo gate-time="#" form-id="#" src="URL"]
    [aritic type="video" src="URL"]
    [ariticvideo src="URL"]

Replace the # signs with the appropriate number. For gate-time, enter the time (in seconds) where you want to pause the video and show the aritic form. For form-id, enter the id of the aritic form that you'd like to display as the gate. Replace URL with the browser URL to view the video. In the case of Youtube or Vimeo, you can simply use the URL as it appears in your address bar when viewing the video normally on the providing website. For MP4 videos, enter the full http URL to the MP4 file on the server.

Since the Aritic v1.0.1 release, the form-id is not mandatory anymore, aritic video can be tracked.

### Aritic Tags

You can add or remove multiple lead tags on specific pages using commas. To remove an tag you have to use minus "-" signal before tag name:

    [aritic type="tags" values="mytag,anothertag,-removetag"]
    [aritictags values="mytag,anothertag,-removetag"]

== Installation ==

### Via WP administration

Aritic - WordPress plugin [is listed](https://wordpress.org/plugins/wp-aritic/) in the in the official WordPress plugin repository. That makes it very easy to install it directly form WP administration.

1. Go to *Plugins* / *Add New*.
2. Search for **WP Aritic** in the search box.
3. The "WP Aritic" plugin should appear. Click on Install.

### Via ZIP package

If the installation via official WP plugin repository doesn't work for you, follow these steps:

1. [Download ZIP package](https://github.com/aritic/aritic-wordpress/archive/master.zip).
2. At your WP administration go to *Plugins* / *Add New* / *Upload plugin*.
3. Select the ZIP package you've downloaded in step 1.

== Upgrade Notice ==
= v2.0.5 =
Fix a tracking code as per new option

= v2.0.4 =
Fix a bug introduced in the 2.0.2 version, you must upgrade asap because the async attribute on form generator script blocks `document.write`.

= v2.0.3 =
Fix a bug introduced in the 2.0.2 version, you must upgrade asap because there was a typo in the option page name which forbid option to be saved.

== Changelog ==

= v2.2.5 =

Release date : 2017-06-13

* Changed
  * Changed tracking code structure as new release having subfolder option available

= v2.2.4 =

Release date : 2017-09-07

* Changed
  * Changed for wrong folder structre. minor change

= v2.2.3 =

Release date : 2017-09-07

* Changed
  * Changed for wrong folder structre. minor change

= v2.2.2 =

Release date : 2017-09-07

* Changed
  * Changed for wrong folder structre. minor change

= v2.2.1 =

Release date : 2017-09-07

* Changed
  * Added changes in shordcode for fingerprint.

= v2.2.0 =

Release date : 2017-08-07

* Changed
  * Add compatibility with the video features.

= v2.1.1 =

Release date : 2017-07-19

* Changed
  * Update some labels which are not clear enough.

= v2.1.0 =

Release date : 2017-07-19

* Added
  * Call translation on all labels, plugin is translation ready !
  * Add a new function `wparitic_get_tracking_attributes` which defines attributes to be sent through JS and Image trackers.
  * Add a filter `wparitic_tracking_attributes` to allow developers injecting custom attributes in trackers.
  * Add the ability to track logged user (within an option)

* Changed
  * Add valid text domain and start official translation process.

= v2.0.4 =

Release date : 2017-06-03

* Changed
  * Hotfix release, the async attribute on form generator script blocks `document.write`.

= v2.0.3 =

Release date : 2017-06-02

* Changed
  * Hotfix release, the option group wasn't valid.

= v2.0.2 =

Release date : 2017-06-02

* Added
  * Make a solid test suite to check every plugin parts (settings, loading, injection)
  * Add a new setting to activate tracking image as a fallback when javascript is disabled

* Changed
  * Refactor shortcode handling and put everything in shortcodes.php.
  * Clean old code from the repository (wparitic_wp_title).

= v2.0.1 =

Release date : 2017-05-25

* Added
  * Add a new option in settings screen to choose where the script is injected.
  * Add new tests around script injection.

= v2.0.0 =

Release date : 2017-05-25

* Added
  * Composer development requirement (squizlabs/php_codesniffer).
  * Code sniffer configuration : phpcs.xml.
  * Update code using the sniff.
  * Add basic unit tests using PHPUnit.
  * Activate continuous integration using Travis-CI (check .travis.yml file).

* Changed
  * Use escape functions when printing data: esc_url.
