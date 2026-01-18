<?php
	/**
	 * Checkout Form
	 *
	 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
	 *
	 * HOWEVER, on occasion WooCommerce will need to update template files and you
	 * (the theme developer) will need to copy the new files to your theme to
	 * maintain compatibility. We try to do this as little as possible, but it does
	 * happen. When this occurs the version of the template file will be bumped and
	 * the readme will list any important changes.
	 *
	 * @see https://docs.woocommerce.com/document/template-structure/
	 * @package WooCommerce/Templates
	 * @version 3.5.0
	 */

	if (!defined('ABSPATH')) {
		exit;
	}

	// If checkout registration is disabled and not logged in, the user cannot checkout.
	if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
		echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
		return;
	}

?>

<section class="clearfix mblp-row mblp-checkout-row">
    <div class="container <?php echo wpm_option_is('mblp.narrow_checkout', 'on') ? '' : 'mblp-wide-container'; ?>">
		<?php //do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
        <div class="row">
            <?php if(!is_user_logged_in()) { ?>
                <div class="col-xs-12">
                <div class="login-tabs bordered-tabs tabs-count-2">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs text-center" role="tablist">
                        <li role="presentation"
                            class="tab-1">
                            <a href="#checkout-wpm-login"
                               id="checkout-login-tab"
                               aria-controls="login-tab"
                               role="tab"
                               data-toggle="tab">
                                <span class="icon-sign-in iconmoon"></span>
						        <?php echo wpm_get_option('mbl_access.tab_login_text') ?>
                            </a>
                        </li>
				        <?php if (!wpm_is_users_overflow()) : ?>
                            <li role="presentation"
                                class="tab-2 active">
                                <a href="#checkout-wpm-register"
                                   id="checkout-register-tab"
                                   aria-controls="registration-tab"
                                   role="tab"
                                   data-toggle="tab">
                                    <span class="iconmoon icon-user"></span>
	                                <?php echo wpm_get_option('mbl_access.tab_register_text') ?>
                                </a>
                            </li>
				        <?php endif; ?>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane" id="checkout-wpm-login">
                            <div class="login-form">
						        <?php mbl_access_render_partial('login-form', 'public', array('full' => true, 'standalone' => true)); ?>
                            </div>
                        </div>
				        <?php if (!wpm_is_users_overflow()) : ?>
                            <div role="tabpanel" class="tab-pane active" id="checkout-wpm-register">
                                <div class="registration-form">
                                    <?php mbl_access_render_partial('register-form', 'public', array('full' => true, 'standalone' => true)); ?></div>
                            </div>
				        <?php endif; ?>
                    </div>

                </div>

            </div>
            <?php } else { ?>
                <div class="col-xs-12">
                <div class="login-tabs bordered-tabs tabs-count-1 checkout-tab">
                    <div class="tab-content">

                        <form name="checkout" method="post" class="checkout woocommerce-checkout"
                              action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

					        <?php if ($checkout->get_checkout_fields()) : ?>

						        <?php do_action('woocommerce_checkout_before_customer_details'); ?>


                                <div id="customer_details">
							        <?php do_action('woocommerce_checkout_billing'); ?>
                                </div>


						        <?php do_action('woocommerce_checkout_after_customer_details'); ?>

					        <?php endif; ?>

					        <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

					        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                            <div id="order_review" class="woocommerce-checkout-review-order">
						        <?php do_action('woocommerce_checkout_order_review'); ?>
                            </div>

					        <?php do_action('woocommerce_checkout_after_order_review'); ?>

                        </form>

				        <?php do_action('woocommerce_after_checkout_form', $checkout); ?>

                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
