<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 */
?>
<div class="page-wrapper">
  <?php if($page['admin']): ?>
    <div class="admin-wrapper">
      <?php print render($page['admin']); ?>
    </div>
  <?php endif; ?>
  <header>
    <div class="logo">
      <?php if (!$is_front): ?>
        <a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home">
          <img src="<?php print $logo; ?>" alt="<?php print $site_name; ?>" />
        </a>
      <?php endif; ?>
      <?php if ($is_front): ?>
        <img src="<?php print $logo; ?>" alt="<?php print $site_name; ?>" />
      <?php endif; ?>
    </div><!-- /#logo -->
    <nav>
      <?php if($page['navigation']): ?>
        <?php print render($page['navigation']); ?>
      <?php endif; ?>
    </nav>
  </header>
  <?php if ($page['slider']): ?>
    <div class="slider-wrapper">
      <div class="slider"><?php print render($page['slider']); ?></div>
    </div>
  <?php endif; ?>
  <?php if($messages): ?>
    <div class="message-wrapper clearfix">
      <?php print $messages; ?>
    </div>
  <?php endif; ?>
  <div class="content-wrapper<?php if($page['sidebar']){print ' with-sidebar';} ?>">
    <div class="center">
      <?php $tabs_rendered = render($tabs); ?>
      <?php if($tabs_rendered): ?>
        <div class="tabs"><?php print $tabs_rendered; ?></div>
      <?php endif; ?>
      <a id="main-content"></a>
      <?php if ($title): ?><h1 class="title"><?php print $title; ?></h1><?php endif; ?>
      <?php print render($page['content']); ?>
    </div>
    <?php if ($page['sidebar']): ?>
      <aside class="sidebar">
          <?php print render($page['sidebar']); ?>
      </aside>
    <?php endif; ?>
  </div>
  <?php if( $page['footer_first'] || $page['footer_second'] || $page['footer_third'] || $page['footer_fourth']): ?>
    <footer>
      <?php if($page['footer_first']): ?>
        <div class="footer-first">
          <?php print render($page['footer_first']); ?>  
        </div>
      <?php endif; ?>
      <?php if($page['footer_second']): ?>
        <div class="footer-second">
          <?php print render($page['footer_second']); ?>  
        </div>
      <?php endif; ?>
      <?php if($page['footer_third']): ?>
        <div class="footer-third">
          <?php print render($page['footer_third']); ?>  
        </div>
      <?php endif; ?>
      <?php if($page['footer_fourth']): ?>
        <div class="footer-fourth">
          <?php print render($page['footer_fourth']); ?>  
        </div>
      <?php endif; ?>
      <?php if($page['footer_fifth']): ?>
        <div class="footer-fifth">
          <?php print render($page['footer_fifth']); ?>  
        </div>
      <?php endif; ?>
    </footer>
  <?php endif; ?>
</div>


















