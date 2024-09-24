jQuery(document).ready(function() {

	/*---------------------------------------------------------*/
	/* Wordpress Sidebar Link                                  */
	/*---------------------------------------------------------*/

	jQuery('a[href="admin.php?page=mbt_upgrade_link"]').on('click', function() { jQuery(this).attr('target', '_blank'); });
});


