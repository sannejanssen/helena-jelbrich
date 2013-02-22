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

    /* Translate 'Order total' */
    $form['cart_contents']['cart_contents_view']['#markup'] = str_replace('Order total', 'Totaal', $form['cart_contents']['cart_contents_view']['#markup']);

    /* BILLING: Remove address 2, change address 1 to just 'address' */
    unset($form['customer_profile_billing']['commerce_customer_address']['und'][0]['street_block']['premise']);
    $form['customer_profile_billing']['commerce_customer_address']['und'][0]['street_block']['thoroughfare']['#title'] = 'Adres';

    /* SHIPPING: Remove address 2, change address 1 to just 'address' */
    unset($form['customer_profile_shipping']['commerce_customer_address']['und'][0]['street_block']['premise']);
    $form['customer_profile_shipping']['commerce_customer_address']['und'][0]['street_block']['thoroughfare']['#title'] = 'Adres';

    // In dutch because it's not translatable
    $form['buttons']['continue']['#value'] = 'Verder naar verzending';
    
    /* Remove &nbsp; before city input field */
    unset($form['customer_profile_billing']['commerce_customer_address']['und'][0]['locality_block']['locality']['#prefix']);
    unset($form['customer_profile_shipping']['commerce_customer_address']['und'][0]['locality_block']['locality']['#prefix']);

    /* Remove 'or' between cancel & continue */
    unset($form['buttons']['cancel']['#prefix']);
  }

  // Step 3 of the checkout process: shipping information
  if ($form_id == 'commerce_checkout_form_shipping') {

    // In dutch because it's not translatable
    $form['buttons']['continue']['#value'] = 'Verder naar overzicht';
    $form['buttons']['back']['#value'] = 'Terug naar bestelling';

    /* Remove 'or' between cancel & continue */
    unset($form['buttons']['back']['#prefix']);

    /* Make sure shipping 'verzending via post' is the default */
    $form['commerce_shipping']['shipping_service']['#default_value'] = 'post';
  }

  // Step 4 of the checkout process: place your order
  if ($form_id == 'commerce_checkout_form_review') {

    /* Translate 'Order total' */
    $form['checkout_review']['review']['#data']['cart_contents']['data'] = str_replace('Order total', 'Totaal', $form['checkout_review']['review']['#data']['cart_contents']['data']);

    // In dutch because it's not translatable
    $form['buttons']['continue']['#value'] = 'Plaats uw bestelling';
    $form['buttons']['back']['#value'] = 'Terug naar verzending';

    /* Remove 'or' between cancel & continue */
    unset($form['buttons']['back']['#prefix']);
  }

  // Step 5 of the checkout process: confirmation message
  if ($form_id == 'commerce_checkout_form_complete') {

    /* TODO check code*/
    // dpm($form);


    

    // $order_url = $form['checkout_completion_message']['message']['#markup'];
    // $message = "<p>Uw bestelling is geplaatst. <a href='$order_url'>U kan deze hier bekijken</a></p>";

    // global $base_root;
    // $message .= "<p><a href='$base_root'>Klik hier om terug te keren naar de startpagina</a></p>";
    // $form['checkout_completion_message']['message']['#markup'] = $message;

    // dpm($form);
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

/**
 * Alter EUR currency placement
 */
function james_commerce_currency_info_alter(&$currencies, $langcode) {
  $currencies['EUR']['symbol_placement'] = 'before';
  $currencies['EUR']['symbol'] = '€ ';
}