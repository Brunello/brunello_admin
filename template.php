<?php

/**
 * Override or insert variables into the maintenance page template.
 */
function brunello_admin_preprocess_maintenance_page(&$vars) {
  // While markup for normal pages is split into page.tpl.php and html.tpl.php,
  // the markup for the maintenance page is all in the single
  // maintenance-page.tpl.php template. So, to have what's done in
  // brunello_admin_preprocess_html() also happen on the maintenance page, it has to be
  // called here.
  brunello_admin_preprocess_html($vars);
}

/**
 * Override or insert variables into the html template.
 */
function brunello_admin_preprocess_html(&$vars) {
  // Add conditional CSS for IE8 and below.
  drupal_add_css(path_to_theme() . '/styles/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 8', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE7 and below.
  drupal_add_css(path_to_theme() . '/styles/ie7.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE6.
  drupal_add_css(path_to_theme() . '/styles/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 6', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add a unique class per content type that is shared on both node create and
  // node add pages.
  if ((arg(0) == 'node') && (arg(1) == 'add')) {
    $vars['classes_array'][] = 'node-add-edit-' . arg(2);
  }
  elseif ((arg(0) == 'node') && (arg(2) == 'edit')) {
    $node = node_load(arg(1));
    $vars['classes_array'][] = 'node-add-edit-' . $node->type;
  }
}

/**
 * Override or insert variables into the page template.
 */
function brunello_admin_preprocess_page(&$vars) {
  $vars['primary_local_tasks'] = $vars['tabs'];
  unset($vars['primary_local_tasks']['#secondary']);
  $vars['secondary_local_tasks'] = array(
    '#theme' => 'menu_local_tasks',
    '#secondary' => $vars['tabs']['#secondary'],
  );
}

/**
 * Display the list of available node types for node creation.
 */
function brunello_admin_node_add_list($variables) {
  $content = $variables['content'];
  $output = '';
  if ($content) {
    $output = '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="clearfix">';
      $output .= '<span class="label">' . l($item['title'], $item['href'], $item['localized_options']) . '</span>';
      $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  else {
    $output = '<p>' . t('You have not created any content types yet. Go to the <a href="@create-content">content type creation page</a> to add a new content type.', array('@create-content' => url('admin/structure/types/add'))) . '</p>';
  }
  return $output;
}

/**
 * Overrides theme_admin_block_content().
 *
 * Use unordered list markup in both compact and extended mode.
 */
function brunello_admin_admin_block_content($variables) {
  $content = $variables['content'];
  $output = '';
  if (!empty($content)) {
    $output = system_admin_compact_mode() ? '<ul class="admin-list compact">' : '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="leaf">';
      $output .= l($item['title'], $item['href'], $item['localized_options']);
      if (isset($item['description']) && !system_admin_compact_mode()) {
        $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      }
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  return $output;
}

/**
 * Override of theme_tablesort_indicator().
 *
 * Use our own image versions, so they show up as black and not gray on gray.
 */
function brunello_admin_tablesort_indicator($variables) {
  $style = $variables['style'];
  $theme_path = drupal_get_path('theme', 'brunello_admin');
  if ($style == 'asc') {
    return theme('image', array('path' => $theme_path . '/images/arrow-asc.png', 'alt' => t('sort ascending'), 'width' => 13, 'height' => 13, 'title' => t('sort ascending')));
  }
  else {
    return theme('image', array('path' => $theme_path . '/images/arrow-desc.png', 'alt' => t('sort descending'), 'width' => 13, 'height' => 13, 'title' => t('sort descending')));
  }
}

/**
 * Implements hook_css_alter().
 */
function brunello_admin_css_alter(&$css) {
  // Use Seven's vertical tabs style instead of the default one.
  if (isset($css['misc/vertical-tabs.css'])) {
    $css['misc/vertical-tabs.css']['data'] = drupal_get_path('theme', 'brunello_admin') . '/vertical-tabs.css';
  }
  if (isset($css['misc/vertical-tabs-rtl.css'])) {
    $css['misc/vertical-tabs-rtl.css']['data'] = drupal_get_path('theme', 'brunello_admin') . '/vertical-tabs-rtl.css';
  }
  // Use Seven's jQuery UI theme style instead of the default one.
  if (isset($css['misc/ui/jquery.ui.theme.css'])) {
    $css['misc/ui/jquery.ui.theme.css']['data'] = drupal_get_path('theme', 'brunello_admin') . '/jquery.ui.theme.css';
  }
}

/**
 * Implements theme_menu_local_action().
 *
 * See http://drupal.org/node/1167350
 */
function brunello_admin_menu_local_action($vars) {
  $link = $vars['element']['#link'];

  $link += array(
    'href' => '',
    'localized_options' => array(),
  );
  $link['localized_options']['attributes']['class'][] = 'button';
  $link['localized_options']['attributes']['class'][] = 'add';

  return '<li>' . l($link['title'], $link['href'], $link['localized_options']) . '</li>';
}

/**
 * Override of theme_pager().
 *
 * Implement "Showing 1-50 of 2345  Next 50 >" type of output.
 */
function brunello_admin_pager($vars) {
  $tags = $vars['tags'];
  $element = $vars['element'];
  $parameters = $vars['parameters'];
  $quantity = $vars['quantity'];
  global $pager_page_array, $pager_total, $pager_total_items, $pager_limits;

  $total_items = $pager_total_items[$element];

  if ($total_items == 0) {
    // No items to display.
    return;
  }

  $total_pages = $pager_total[$element];
  $limit = $pager_limits[$element];
  $showing_min = $pager_page_array[$element] * $limit + 1;
  $showing_max = min(($pager_page_array[$element] + 1) * $limit, $total_items);
  $pager_current = $pager_page_array[$element];

  $output = '<div class="short-pager">';
  if ($pager_current > 0) {
    // Add link to the first page.
    $vars = array(
      'text' => t('First'),
      'attributes' => array('title' => t('Go to the first page')),
      'element' => $element,
    );
    $output .= '<div class="short-pager-first">' . theme('pager_link', $vars) . '</div>';

    // Add link to prev page.
    $vars = array(
      'text' => t('Prev @limit', array('@limit' => $limit)),
      'page_new' => pager_load_array($pager_current - 1, $element, $pager_page_array),
      'element' => $element,
      'parameters' => $parameters,
      'attributes' => array('title' => t('Go to the previous page')),
    );
    $output .= '<div class="short-pager-prev">' . theme('pager_link', $vars) . '</div>';
  }

  $output .= '<div class="short-pager-main">' . t('Showing @range <span class="short-pager-total">of @total</span>', array('@range' => $showing_min . ' - ' . $showing_max, '@total' => $total_items)) . '</div>';

  if (($pager_current < ($total_pages - 1)) && ($total_pages > 1)) {
    // Add link to next page.
    $vars = array(
      'text' => t('Next @limit', array('@limit' => $limit)),
      'page_new' => pager_load_array($pager_current + 1, $element, $pager_page_array),
      'element' => $element,
      'parameters' => $parameters,
      'attributes' => array('title' => t('Go to the previous page')),
    );
    $output .= '<div class="short-pager-next">' . theme('pager_link', $vars) . '</div>';

    // Add link to last page.
    $vars = array(
      'text' => t('Last'),
      'attributes' => array('title' => t('Go to the last page')),
      'element' => $element,
      'page_new' => pager_load_array($total_pages - 1, $element, $pager_page_array),
    );
    $output .= '<div class="short-pager-last">' . theme('pager_link', $vars) . '</div>';
  }
  // Close tag for short-pager.
  $output .= '</div>';

  return $output;
}
