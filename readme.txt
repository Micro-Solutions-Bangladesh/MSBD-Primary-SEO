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
* Basic Schema.org JSON-LD controls for WebSite, Organization, optional LocalBusiness, BreadcrumbList, BlogPosting, and WebPage.

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
* Subsites inherit network values unless a field is explicitly overridden.
* Capability checks before viewing or saving settings.
* Nonce verification before saving.
* Admin output escaped safely.
* Frontend output preserved for valid scripts and meta tags.
* Outputs social image meta tags using featured image, custom social image, and merged site/network defaults.
* Outputs lightweight Schema.org JSON-LD from safe structured data controls.

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
3. Site default social image when that site field is overridden.
4. Network default social image when the site field is not overridden.

The plugin outputs `og:image`, `twitter:image`, and image dimension meta tags when a fallback image is available.

Recommended image size: 1200 × 630 pixels.

= Basic Schema.org JSON-LD =

The plugin includes lightweight structured data controls for safe, general schema types:

* WebSite on the front page.
* Organization with name, logo, website URL, and SameAs social links.
* Optional LocalBusiness with name, URL, logo, telephone, price range, and plain-text address.
* BreadcrumbList for singular posts/pages and taxonomy archives.
* BlogPosting for standard posts.
* WebPage for standard pages.

Schema output is generated as JSON-LD in `wp_head`. On multisite network activation, network schema values provide defaults. Individual sites can override any schema field, including disabling a schema type that is enabled at network level.

== Multisite Behavior ==

When WordPress Multisite is enabled and the plugin is network activated:

* Network Admin gets a `Primary SEO` menu.
* Each site Admin also gets a `Primary SEO` menu.
* Each subsite inherits network-level settings by default.
* A site-level value is used only when the matching field is explicitly marked as an override on that subsite.

Effective value order:

1. Site override value, when override is enabled for that field.
2. Network value, when no site override is enabled.

This lets you apply global SEO defaults across the network while still allowing individual subsites to replace a specific field when required.

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



== Changelog ==

= 1.0.0 =
* Initial release.
* Added site, network, and per-post/page social image fields.
* Added frontend social image fallback meta output.
* Added Basic Schema.org JSON-LD controls and frontend structured data output.
