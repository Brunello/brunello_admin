##Administration theme for Drupal 7

Based on the core theme 'Seven', this is a two-column admin theme for Drupal 7

###Improvements

* Fixed width (1000px)
* Added sidebar left region to hold navigation menu
* Added table-row hover style
* Generic status colors (0, 1, 2 -> red, yellow, green)
* Placeholder "custom" css file
* Improved file structure
* Unique body class per content type shared on both node add and node edit
  pages
* "Showing 1-50 of 2345  Next 50 >" type pagers. See http://drupal.org/node/538788
  (which was neven implemented in the core Seven theme)
* Updated action button styles. See: http://drupal.org/node/1167350. (Will be
  implemented in Drupal 8)

###NOTE:
You must disable Seven and select this theme as the "Administration theme" on
on the Appearance page in order for this theme to work properly. If you have 
enabled this theme but still don't see the sidebar region rendered, ensure that
you have selected "brunello_admin" from the "Administration theme" select list.

Dev snapshot available on GitHub:
https://github.com/balsama/brunello_admin
