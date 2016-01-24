<?php global $cp_options; ?>

<div class="footer">

		<div class="footer_main">

				<div class="footer_main_res">

						<div class="dotted">

								<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_footer') ) : else : ?> <!-- no dynamic sidebar so don't do anything --> <?php endif; ?>

								<div class="clr"></div>

						</div><!-- /dotted -->

						<div class="footer_menu">

							<div class="footer_menu_res">

								<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'container' => false, 'menu_id' => 'footer-nav-menu', 'depth' => 1, 'fallback_cb' => false ) ); ?>

								<div class="clr"></div>

							</div><!-- /footer_menu_res -->

						</div><!-- /footer_menu -->

						<p>&copy; <?php echo date_i18n('Y'); ?><?php _e( ' The Restaurant Zone. All Rights Reserved', APP_TD ); ?></p>

						<div class="right">
								<p><a target="_blank" href="http://therestaurantzone.com/" title="The Restaurant Zone"><?php _e( 'The Restaurant Zone', APP_TD ); ?></a></p>
						</div>

						<?php cp_website_current_time(); ?>

						<div class="clr"></div>

				</div><!-- /footer_main_res -->


		</div><!-- /footer_main -->


</div><!-- /footer -->
