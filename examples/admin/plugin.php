<?php

/**
 * Example \Kinglet\Admin\PageBase implementations
 */
add_action('admin_menu', function() {
	require_once 'admin/parent-page.php';
	require_once 'admin/child-page.php';

	$parentPage = new MyParentPage();
	$parentPage->addToMenu();

	$childPage = new MyChildPage();
	$childPage->addToSubMenu($parentPage);
});
