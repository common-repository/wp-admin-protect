<div id="mrwrapper">
	<div class="container">
		<div class="row wpap-header">
			<div class="col-6 col-12-sm">
				<h1>WP Admin Protect</h1>
			</div>
			<div class="col-6 col-12-sm right">
				<ul class="social-links">
					<li>
						<a href="https://www.youtube.com/channel/UCVFVYTQ8f3vMiVhOAIc7cFA" target="_Blank"><i class="fa fa-youtube"></i></a>
					</li>
					<li>
						<a href="http://www.facebook.com/brainythemes"><i class="fa fa-facebook" target="_Blank"></i></a>
					</li>
				</ul>
			</div>
		</div>					
		<div class="row wpap-body">
			<div class="col-8 col-12-sm">
				<?php settings_errors(); ?>	
				<form method="post" action="options.php">
				    <?php
				    	settings_fields( 'wpap-group' );
				        do_settings_sections("wpap");
				        submit_button(); 
				    ?>          
				</form>
				<?php if ( get_option( 'wpap-activated' ) == 1 ): ?>
					<div class="col-12 col-12-sm center">
						<p><?php echo __( 'Now your WP Admin URL is:', 'wp-admin-protect' ); ?></p>
					</div>
					<div class="col-1 hidden-sm"></div>
					<div class="col-10 col-12-sm center url-panel">
						<?php echo esc_url( home_url( '/' ) ) . 'wp-login.php?' . get_option( 'wpap-term' ); ?>
					</div>
					<div class="col-1 hidden-sm"></div>
				<?php endif; ?>
			</div>			
			<div class="col-4 about">
				<a href="https://brainythemes.com/member-club" target="_Blank">
					<img src="<?php echo plugins_url() . '/wp-admin-protect/assets/images/member-club.jpg'; ?>" alt="">
				</a>
			</div>
		</div>
		<div class="row wpap-footer">
			<div class="col-12">
				<p class="text-center">
					<a href="https://brainythemes.com" target="_Blank">
						<img src="<?php echo plugins_url() . '/wp-admin-protect/assets/images/logo.jpg'; ?>" alt="">
					</a>
				</p>
			</div>
		</div>
	</div>
</div>