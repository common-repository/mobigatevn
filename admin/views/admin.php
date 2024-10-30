<div class="wrap">
	<?php
		if(isset($message)){
	?>
		<div id="message" class="updated"><?php echo $message; ?></div>
	<?php }	?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- @TODO: Provide markup for your options page here. -->
	<form method="post">
	<input type="hidden" name="update_settings" value="Y" />
    <?php settings_fields( 'mobigate-options' ); ?>
    <?php do_settings_sections( 'mobigate-options' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">API Key</th>
        <td><input type="text" name="api_key" value="<?php echo get_option('api_key'); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
