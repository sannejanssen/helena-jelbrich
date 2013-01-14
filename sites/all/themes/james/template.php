<?php


function james_preprocess_page(&$variables) {

  // Add specific stylesheet for IE
  drupal_add_css(
    path_to_theme() . '/stylesheets/ie.css',
    array(
      'group' => CSS_THEME,
      'weight' => 115,
      'browsers' => array('IE' => 'lte IE 9', '!IE' => FALSE),
      'preprocess' => FALSE
    )
 );
}

function james_preprocess_commerce_line_item_summary(&$variables) {
  $variables['cart_url'] = base_path() . 'cart';
}


function james_form_alter(&$form, &$form_state, $form_id) {

  // Ask a question about a project (on webshop project page)
  if ($form_id == 'webform_client_form_261') {
    $webshop_item_title = drupal_get_title();

    global $base_root;
    $webshop_item_url = $base_root . request_uri();

    $form['submitted']['product']['#value'] = $webshop_item_title;
    $form['submitted']['url']['#value'] = $webshop_item_url;
  }

  // Step 2 of the checkout process: personal information
  if ($form_id == 'commerce_checkout_form_checkout') {

    // In dutch because it's not translatable
    $form['buttons']['continue']['#value'] = 'Verder naar verzending';
    
    /* Remove &nbsp; before city input field */
    unset($form['customer_profile_billing']['commerce_customer_address']['und'][0]['locality_block']['locality']['#prefix']);
    unset($form['customer_profile_shipping']['commerce_customer_address']['und'][0]['locality_block']['locality']['#prefix']);

    /* Remove 'or' between cancel & continue */
    unset($form['buttons']['cancel']['#prefix']);

    // dpm();
  }

  // Step 3 of the checkout process: shipping information
  if ($form_id == 'commerce_checkout_form_shipping') {

    // In dutch because it's not translatable
    $form['buttons']['continue']['#value'] = 'Verder naar overzicht';
    $form['buttons']['back']['#value'] = 'Terug naar bestelling';

    /* Remove 'or' between cancel & continue */
    unset($form['buttons']['back']['#prefix']);
    // dpm($form['buttons']);
  }

  // Step 4 of the checkout process: place your order
  if ($form_id == 'commerce_checkout_form_review') {

    // In dutch because it's not translatable
    $form['buttons']['continue']['#value'] = 'Plaats uw bestelling';
    $form['buttons']['back']['#value'] = 'Terug naar verzending';
  }

  // dpm($form_id);
}

function james_form_element($variables) {
  // dpm($variables);
  $element = &$variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Remove useless Drupal core or module css.
 */
function james_css_alter(&$css) {
  unset($css['modules/system/system.menus.css']);
  unset($css['modules/node/node.css']);
  unset($css['modules/user/user.css']);
  unset($css['modules/field/theme/field.css']);
}

/**
 * Theme main menu
 */
function james_menu_link(array $variables) {

  // Capture the menu link element
  $element = $variables['element'];

  // If frontpage, make the home logo an H1 element
  if(drupal_is_front_page()) {
    if($element['#href'] == '<front>') {
      $element['#attributes']['class'][] = 'home';
      $element['#prefix'] = '<h1>';
      $element['#suffix'] = '</h1>';
      $element['#title'] = "Helena-Jelbrich";
    }
  }
  else
  {
    if($element['#href'] == '<front>') {
      $element['#attributes']['class'][] = 'home';
      // $element['#prefix'] = '<h1>';
      // $element['#suffix'] = '</h1>';
      $element['#title'] = "Helena-Jelbrich";
    }
  }


  $sub_menu = '';

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);

  if( isset($element['#prefix']) && isset($element['#suffix'])) {
    $output = $element['#prefix'] . $output . $element['#suffix'];
  }

  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

/*
 * Remove icon from downloads
 */
function james_file_link($variables) {

  $file = $variables['file'];
  $icon_directory = $variables['icon_directory'];

  $url = file_create_url($file->uri);
  $icon = theme('file_icon', array('file' => $file, 'icon_directory' => $icon_directory));

  // Set options as per anchor format described at
  // http://microformats.org/wiki/file-format-examples
  $options = array(
    'attributes' => array(
      'type' => $file->filemime . '; length=' . $file->filesize,
    ),
  );

  // Use the description as the link text if available.
  if (empty($file->description)) {
    $link_text = $file->filename;
  }
  else {
    $link_text = $file->description;
    $options['attributes']['title'] = check_plain($file->filename);
  }

  // return '<span class="file">' . $icon . ' ' . l($link_text, $url, $options) . '</span>';
  return '<span class="file">' . l($link_text, $url, $options) . '</span>';
}


function james_preprocess_views_view(&$vars){

  

  /*
  $view = $vars['view'];
  if ($view -> name = 'frontpage_view'){
    drupal_add_js(drupal_get_path('theme', 'tbase') . '/js/jquery.cycle.all.js');
    drupal_add_js(drupal_get_path('theme', 'tbase') . '/js/slideshow.js');
  }
  */
}






/*
 * Add body classes if certain regions have content.



function bartik_preprocess_html(&$variables) {
  if (!empty($variables['page']['featured'])) {
    $variables['classes_array'][] = 'featured';
  }

  if (!empty($variables['page']['triptych_first'])
    || !empty($variables['page']['triptych_middle'])
    || !empty($variables['page']['triptych_last'])) {
    $variables['classes_array'][] = 'triptych';
  }

  if (!empty($variables['page']['footer_firstcolumn'])
    || !empty($variables['page']['footer_secondcolumn'])
    || !empty($variables['page']['footer_thirdcolumn'])
    || !empty($variables['page']['footer_fourthcolumn'])) {
    $variables['classes_array'][] = 'footer-columns';
  }

  // Add conditional stylesheets for IE
  drupal_add_css(path_to_theme() . '/css/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'preprocess' => FALSE));
  drupal_add_css(path_to_theme() . '/css/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 6', '!IE' => FALSE), 'preprocess' => FALSE));
}

/**
 * Override or insert variables into the page template for HTML output.

function bartik_process_html(&$variables) {
  // Hook into color.module.
  if (module_exists('color')) {
    _color_html_alter($variables);
  }
}

/**
 * Override or insert variables into the page template.

function bartik_process_page(&$variables) {
  // Hook into color.module.
  if (module_exists('color')) {
    _color_page_alter($variables);
  }
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name']   = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
}

/**
 * Implements hook_preprocess_maintenance_page().

function bartik_preprocess_maintenance_page(&$variables) {
  // By default, site_name is set to Drupal if no db connection is available
  // or during site installation. Setting site_name to an empty string makes
  // the site and update pages look cleaner.
  // @see template_preprocess_maintenance_page
  if (!$variables['db_is_active']) {
    $variables['site_name'] = '';
  }
  drupal_add_css(drupal_get_path('theme', 'bartik') . '/css/maintenance-page.css');
}

/**
 * Override or insert variables into the maintenance page template.

function bartik_process_maintenance_page(&$variables) {
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name']   = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
}

/**
 * Override or insert variables into the node template.

function bartik_preprocess_node(&$variables) {
  if ($variables['view_mode'] == 'full' && node_is_page($variables['node'])) {
    $variables['classes_array'][] = 'node-full';
  }
}

/**
 * Override or insert variables into the block template.

function bartik_preprocess_block(&$variables) {
  // In the header region visually hide block titles.
  if ($variables['block']->region == 'header') {
    $variables['title_attributes_array']['class'][] = 'element-invisible';
  }
}

/**
 * Implements theme_menu_tree().

function bartik_menu_tree($variables) {
  return '<ul class="menu clearfix">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_field__field_type().

function bartik_field__taxonomy_term_reference($variables) {
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<h3 class="field-label">' . $variables['label'] . ': </h3>';
  }

  // Render the items.
  $output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
  foreach ($variables['items'] as $delta => $item) {
    $output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $variables['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
  }
  $output .= '</ul>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . (!in_array('clearfix', $variables['classes_array']) ? ' clearfix' : '') . '"' . $variables['attributes'] .'>' . $output . '</div>';

  return $output;
}
*/