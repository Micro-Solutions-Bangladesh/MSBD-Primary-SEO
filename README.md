# MSBD Primary SEO

**MSBD Primary SEO** is a lightweight WordPress SEO utility plugin that allows trusted administrators to inject SEO-related scripts, meta tags, structured data, comments, and markup into key frontend locations.

It supports both:

- Single-site WordPress
- WordPress Multisite

## Plugin Details

| Item | Value |
|---|---|
| Plugin Name | MSBD Primary SEO |
| Plugin Slug | `msbd-primary-seo` |
| Text Domain | `msbd-primary-seo` |
| Prefix | `msbdpseo` |
| Version | `1.0.0` |

## File Structure

```text
msbd-primary-seo/
├── msbd-primary-seo.php
├── includes/
│   ├── class-msbdpseo-admin.php
│   ├── class-msbdpseo-helper.php
│   └── functions.php
├── readme.txt
└── README.md
```

## Features

- Clean repository-style structure.
- Object-oriented PHP.
- Lightweight implementation.
- No Composer.
- No build tools.
- Uses the native WordPress media uploader for image fields.
- Works on single-site WordPress.
- Supports WordPress Multisite.
- Adds a site-level admin settings page.
- Adds a network-level settings page on multisite.
- Outputs network-level code before site-level code.
- Uses nonce verification before saving.
- Uses capability checks before viewing or saving.
- Escapes admin output safely.
- Preserves valid frontend SEO scripts and markup.
- Adds a site default social image field.
- Adds a network default social image field on multisite.
- Adds a custom social image metabox for posts and pages.
- Outputs `og:image`, `twitter:image`, and image dimension meta tags.

## Settings Fields

The plugin includes three textarea fields and one image field.

### 1. SEO Code Before `</head>`

Outputs code before the closing `</head>` tag.

Hook:

```php
add_action( 'wp_head', ... , 99 );
```

Useful for:

- Search engine verification meta tags
- Analytics scripts
- Structured data JSON-LD
- SEO comments
- Other head-level snippets

### 2. SEO Code After `<body>`

Outputs code right after the opening `<body>` tag.

Hook:

```php
add_action( 'wp_body_open', ... , 5 );
```

Useful for:

- Tag manager body snippets
- Noscript tracking markup
- Body-start analytics snippets

Important: the active theme must call `wp_body_open()` after the opening `<body>` tag.

### 3. SEO Code Before `</body>`

Outputs code before the closing `</body>` tag.

Hook:

```php
add_action( 'wp_footer', ... , 99 );
```

Useful for:

- Footer scripts
- Tracking scripts
- SEO-related markup before closing body

## Stored Option Keys

The plugin stores values using these option keys:

```php
_msbdpseo_before_head_tag_end
_msbdpseo_body_tag_start
_msbdpseo_before_body_tag_end
_msbdpseo_default_social_image
```

The per-post/page social image attachment ID is stored in post meta:

```php
_msbdpseo_social_image_id
```

On multisite, the network settings use the same keys with WordPress network option functions.

## Single-Site Behavior

On single-site WordPress, the plugin adds this admin menu:

```text
Primary SEO
```

Location:

```text
WordPress Admin > Primary SEO
```

Required capability:

```php
manage_options
```

The site-level settings are saved as normal WordPress options using `get_option()` and `update_option()`.

## Multisite Behavior

On WordPress Multisite, the plugin adds:

### Network Admin Menu

```text
Network Admin > Primary SEO
```

Required capability:

```php
manage_network_options
```

Network-level settings are saved using:

```php
get_site_option()
update_site_option()
```

### Site Admin Menu

Each site also gets:

```text
Site Admin > Primary SEO
```

Required capability:

```php
manage_options
```

Site-level settings are saved using:

```php
get_option()
update_option()
```


## Social Image Fallback Order

The plugin outputs social image meta tags in the frontend head using this fallback order:

1. Post featured image.
2. Custom post/page social image.
3. Site default social image.
4. Network default social image.

Generated tags:

```html
<meta property="og:image" content="..." />
<meta name="twitter:image" content="..." />
<meta property="og:image:width" content="..." />
<meta property="og:image:height" content="..." />
```

The custom social image field appears in the post/page editor sidebar as **MSBD Social Image**. The default social image field appears on the site settings page and, on multisite, the network settings page. Recommended image size: 1200 × 630 pixels.

## Frontend Output Order

When both network-level and site-level values exist, the plugin outputs them in this order:

1. Network value
2. Site value

This lets you add global SEO scripts across the whole network while still allowing individual subsites to add their own code.

Example frontend output:

```html
<!-- MSBD Primary SEO: Before Head End - Network -->
...
<!-- /MSBD Primary SEO -->

<!-- MSBD Primary SEO: Before Head End - Site -->
...
<!-- /MSBD Primary SEO -->
```

## Frontend Context Restrictions

The plugin does not output SEO code in:

- WordPress admin
- AJAX requests
- REST API requests
- Cron requests
- XML-RPC requests

Frontend output only runs during normal frontend page rendering.

## Security Model

This plugin is designed for trusted administrators.

SEO integrations often require tags that normal sanitizers remove, such as:

- `<script>`
- `<meta>`
- `<noscript>`
- JSON-LD structured data
- Search engine verification tags

Because of that, the plugin uses a capability-gated sanitization approach:

- Users with `manage_network_options` can save unfiltered SEO code.
- Users with `unfiltered_html` can save unfiltered SEO code.
- Lower-trust users fall back to `wp_kses_post()`.

Frontend output is intentionally not escaped because escaping would break valid SEO scripts and markup.

Only trusted users should be allowed to manage these settings.

## Installation

### Single Site

1. Copy the `msbd-primary-seo` folder into:

```text
wp-content/plugins/
```

2. Go to:

```text
WordPress Admin > Plugins
```

3. Activate:

```text
MSBD Primary SEO
```

4. Go to:

```text
WordPress Admin > Primary SEO
```

5. Add your SEO code and save.

### Multisite

1. Copy the `msbd-primary-seo` folder into:

```text
wp-content/plugins/
```

2. Go to:

```text
Network Admin > Plugins
```

3. Network activate:

```text
MSBD Primary SEO
```

4. Configure global SEO code at:

```text
Network Admin > Primary SEO
```

5. Configure site-specific SEO code at:

```text
Site Admin > Primary SEO
```

## Developer Notes

Public helper functions are available in:

```php
includes/functions.php
```

Examples:

```php
msbdpseo_get_option_key( 'before_head_tag_end' );

msbdpseo_get_site_option( 'before_head_tag_end' );

msbdpseo_get_network_option( 'before_head_tag_end' );

msbdpseo_should_output_frontend();
```

## Changelog

### 1.0.0

Initial release.
