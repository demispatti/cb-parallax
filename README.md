# cbParallax

Responsive website background images with a parallax effect and a user friendly interface.
Your theme must support [Custom Backgrounds](https://codex.wordpress.org/Custom_Backgrounds), which it most likely does
already.

## Description

Responsive website background image(s) with parallax effect.

## Features

- Define one image for all pages or individual images and effects on a per page and post basis with fllback to the main
  background image
- Compatible with all custom post types
- Works vertically and, for fun, horizontally
- Various overlays to choose from
- Optional boxed layout

## Requirements

* Your theme must support the core WordPress implementation of
  the [Custom Backgrounds](https://codex.wordpress.org/Custom_Backgrounds) theme feature.
* Minimum image resolution of 1080px * 1080px
* PHP version 8.0 or above.

* ## Installation

1. Upload the `cb-parallax` folder to your `/wp-content/plugins/` directory.
2. Activate the "cbParallax" plugin through the "Plugins" menu in WordPress.
3. Edit a post to add a custom background.

## Frequently Asked Questions

= Where do I interact with this plugin and how does it work? =

Go to "Settings" -> "cbParallax".

= Why doesn't it work with my theme? =

Most likely, this is because your theme doesn't support the WordPress `custom-background` theme feature.
This plugin requires that your theme utilize this theme feature to work properly.
Unfortunately, there's just no reliable way for the plugin to overwrite the background if the theme doesn't support this
feature.
You'll need to check with your theme author to see if they'll add support or switch to a different theme.

= My theme supports 'custom-background' but it doesn't work! =

That's unlikely.  
Just to make sure, check with your theme author and make sure that they support the WordPress `custom-background` theme
feature.  
Also, make sure that no container element is covering the element that holds the background image.

= How do I add support for this in a theme? =

Your theme must support the [Custom Backgrounds](https://codex.wordpress.org/Custom_Backgrounds) feature for this
plugin to work.  
If you're a theme author, consider adding support for this feature if you can make it fit in with your design. The
following is the basic code, but check out the above link.  
add_theme_support( 'custom-background' );

= Are there any known limitations? =

* For the background image to show up, you need to (try) to use the plugins boxed layout feature and may define a
  background color for the content container the plugin creates. Or you do it on your own ;)

= Can you help me? =

Yes. I have a look at the plugin's support page two or three times a month and I provide some basic support there.

= Are there any known issues? =

* No.

## Screenshots

1. Multiple background views of a single post.
2. Settings Page.
3. Custom background meta box on the edit post screen.