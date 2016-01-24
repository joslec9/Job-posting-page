<?php
/**
 * Order Checkout template.
 *
 * @package ClassiPress\Templates
 * @author  AppThemes
 * @since   ClassiPress 3.3
 */
?>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<div id="breadcrumb"><?php cp_breadcrumb(); ?></div>

			<div class="shadowblock_out">

				<div class="shadowblock">

					<div class="post">

						<div class="order-gateway">

							<?php
								process_the_order();
								// Retrieve updated order object
								$order = get_order();
								if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ) {
									$redirect_to = get_post_meta( $order->get_id(), 'complete_url', true );
									echo html( 'a', array( 'href' => $redirect_to ), __( 'Continue', APP_TD ) );
									echo html( 'script', 'location.href="' . $redirect_to . '"' );
								}
							?>

						</div>

					</div><!--/post-->

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
