<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-base-template"><br></div>
    <h2><?php _e( 'nonprofitCMS Membership', 'npcms_membership' ); ?></h2>
	<ul>
		<li class="clear"><h3>Getting Started</h3>
			<p><strong>1. </strong> Visit our <a href="http://dev.nonprofitcms.org/wordpress-membership-plugin/" target="_blank">documentation</a></p>
			<p><strong>2. </strong> Setup either iMIS or memberCMS before returning here</p>
			<p><strong>3. </strong> Use the short code [member-login] to render the login widget on a page</p>
			<p><strong>4. </strong> Enter your iMIS or memberCMS credentials in the settings below</p>			
		</li>
	</ul>
	<p></p>	
    <form id="npcms_membership-form" action="options.php" method="POST">

        <?php settings_fields( 'npcms_membership_setting' ) ?>
        <?php do_settings_sections( 'npcms_membership_setting' ) ?>

        <input type="submit" value="<?php _e( 'Save', 'npcms_membership_settings' ); ?>" />
    </form>
</div>