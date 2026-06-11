=== MSBD Primary SEO ===
Contributors: shahalom, microsolutions
Tags: seo, header scripts, footer scripts, meta tags, multisite, structured data
Requires at least: 5.3
Requires PHP: 7.4
Tested up to: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight SEO utility plugin for injecting trusted SEO scripts, meta tags, structured data, comments, and markup into key frontend locations.

== Description ==

MSBD Primary SEO is a lightweight WordPress plugin for administrators who need to add SEO-related code to frontend pages.

It supports:

* Single-site WordPress.
* WordPress Multisite.
* Network-level SEO code.
* Site-level SEO code.
* Output before the closing head tag.
* Output right after the opening body tag.
* Output before the closing body tag.
* Site default social image.
* Network default social image on multisite.
* Custom post/page social image fallback.

The plugin is useful for adding:

* Search engine verification meta tags.
* Analytics scripts.
* Structured data JSON-LD.
* NoScript tracking markup.
* SEO-related comments.
* Global network SEO snippets.
* Site-specific SEO snippets.

== Features ==

* Clean repository-style structure.
* Object-oriented PHP.
* No Composer dependency.
* No build tools.
* Uses the native WordPress media uploader for image fields.
* Supports multisite network admin settings.
* Supports individual site settings.
* Network values output before site values.
* Capability checks before viewing or saving settings.
* Nonce verification before saving.
* Admin output escaped safely.
* Frontend output preserved for valid scripts and meta tags.
* Outputs social image meta tags with the requested fallback order.

== Installation ==

1. Upload the `msbd-primary-seo` folder to `/wp-content/plugins/`.
2. In WordPress Admin, go to Plugins.
3. Activate `MSBD Primary SEO`.

For WordPress Multisite:

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Go to Network Admin > Plugins.
3. Network Activate `MSBD Primary SEO`.
4. Go to Network Admin > Primary SEO to configure global network-level code.
5. Go to each site Admin > Primary SEO to configure site-level code.

== Settings ==

The plugin provides three textarea fields:

= SEO Code Before `</head>` =

Outputs code using:

`wp_head` priority `99`.

Useful for:

* Verification meta tags.
* Structured data.
* Analytics scripts.
* SEO comments.
* Other head-level snippets.

= SEO Code After `<body>` =

Outputs code using:

`wp_body_open` priority `5`.

Useful for:

* Tracking noscript markup.
* Body-start tracking snippets.
* Tag manager body snippets.

Important: the active theme must call `wp_body_open()` after the opening body tag.

= SEO Code Before `</body>` =

Outputs code using:

`wp_footer` priority `99`.

Useful for:

* Footer scripts.
* Tracking scripts.
* SEO-related markup before closing body.


= Social Image Fields =

The plugin includes a default social image field on the site settings page. On multisite, the network settings page also includes a network default social image field. Posts and pages include a custom social image metabox.

Frontend fallback order:

1. Post featured image.
2. Custom post/page social image.
3. Site default social image.
4. Network default social image.

The plugin outputs `og:image`, `twitter:image`, and image dimension meta tags when a fallback image is available.

Recommended image size: 1200 × 630 pixels.

== Multisite Behavior ==

When WordPress Multisite is enabled and the plugin is network activated:

* Network Admin gets a `Primary SEO` menu.
* Each site Admin also gets a `Primary SEO` menu.
* Network-level values are output first.
* Site-level values are output after network-level values.

Output order:

1. Network value.
2. Site value.

This allows global SEO code to apply across the whole network while still allowing individual subsites to add their own SEO code.

== Security Notes ==

This plugin intentionally allows trusted administrators to save SEO scripts, meta tags, and markup.

The plugin does not escape frontend output because escaping would break valid SEO scripts, verification tags, JSON-LD, noscript markup, and meta tags.

Sanitization behavior:

* Network administrators can save unfiltered SEO code.
* Users with `unfiltered_html` can save unfiltered SEO code.
* Lower-trust users fall back to `wp_kses_post()`, which may strip scripts and meta tags.

Only trusted users should be given access to this settings page.

== Frequently Asked Questions ==

= Why not use wp_kses_post() for everyone? =

`wp_kses_post()` removes many tags needed for SEO integrations, including scripts and meta tags. This plugin is intended for trusted administrators who need to paste third-party SEO, analytics, verification, and structured data snippets.

= Does the plugin output code in admin, REST, AJAX, or cron requests? =

No. Frontend output is skipped in admin, REST, AJAX, cron, and XML-RPC contexts.

= Does the body-start field always work? =

It works when the active theme calls `wp_body_open()` immediately after the opening body tag.

= Can I use it on single-site WordPress? =

Yes. The site-level settings page works normally on single-site WordPress.

= Can I use it on multisite? =

Yes. Network activate the plugin to use both network-level and site-level settings.


== Screenshots ==
1. Settings page


== Changelog ==

= 1.0.0 =
* Initial release.
* Added site, network, and per-post/page social image fields.
* Added frontend social image fallback meta output.
