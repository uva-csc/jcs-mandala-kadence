# Mandala Kadence
A child theme of Kadence for Mandala-based sites and their subsites

## Styles
CSS styles are written in SCSS locally using the WP-SCSS plugin which compiles and saves the css, which will also 
be included in the git repo to be used by production sites.

The media-queries.scss file is taken from https://github.com/Necromancerx/media-queries-scss-mixins and the use of 
those media queries is documented in that repo.

## Subsites
This theme also allows for subsites. This requires the use of custom metadata for pages. This is best implemented
through the plugin Advanced Custom Fields. The necessary fields are:

* subsite_menu: The menu code for the subsite’s custom menu
* subsite_title: The Title for the subsite to display in the header in place of the blogname
* subsite_class: Specific body class for subsite (if not defined, adds ‘subsite-pageid’ as the body class
* subsite_logo: an image field to choose the logo image
* subsite_footer_menu: the  id for the menu to use in the subsite’s footer
* subsite_css: a text area for the custom CSS for that subsite, all selectors are automatically prefixed with 
the body class for the subsite as defined above

Any page *or children of a page* that has these any of these fields set will display the specific menu and title
in the header and attach the subsite class to the body along with the generic class "subsite".


**Author:** Than Grove \
**Organization:** Contemplative Sciences Center & Tibetan and Himalayan Digital Library, University of Virginia \
**Date:** March 29, 2022 (July 13, 2022)
