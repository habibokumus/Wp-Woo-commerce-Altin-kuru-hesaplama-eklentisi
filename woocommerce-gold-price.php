<?php
/**
 * Plugin Name: WooCommerce Altın Kuruna Göre Satış Yapma
 * Plugin URI: https://epis.io/
 * Description: 24k, 22k, 18k ve 14k altın ürünleri için Altın Fiyatı ekleyerek fiyatlarını güncellemeyi kolaylaştırır
 * Version: 1.0
 * Author: Habib Okumuş
 * Author URI: https://epist.io/
 *
 * PHP: 5.2
 *
 * Github Habibokumus
 * 
 *
 *
 */
 if ( ! defined( 'ABSPATH' ) ) exit; 

add_action( 'plugins_loaded', 'woocommence_altin_fiyati', 20 );




function woocommence_altin_fiyati() {

	if ( ! class_exists( 'woocommerce' ) ) {  
		return false;
	}

	
	add_filter( 'plugin_action_links', 'woocommence_altin_fiyati_action_links', 10, 2 );

	add_action( 'admin_init', 'woocommence_altin_fiyati_admin_init' );
	add_action( 'admin_menu', 'woocommence_altin_fiyati_admin_menu', 10 );


	load_plugin_textdomain( 'woocommence-altin-fiyati', false, '/woocommence-altin-fiyati/languages' );


	function woocommence_altin_fiyati_weight_description() {

		$weight_unit             = get_option( 'woocommerce_weight_unit' );
		$weight_unit_description = array(
											'kg'  => __( 'kg', 'woocommence-altin-fiyati' ),
											'g'   => __( 'g', 'woocommence-altin-fiyati' ),
											'lbs' => __( 'lbs', 'woocommence-altin-fiyati' ),
											'oz'  => __( 'oz', 'woocommence-altin-fiyati' ),
										);

		return $weight_unit_description[ $weight_unit ];
	}


	function woocommence_altin_fiyati_admin_init() {

	
		register_setting( 'woocommence_altin_fiyati_options',
			'woocommence_altin_fiyati_options',
			'woocommence_altin_fiyati_validate_options' );

		
		add_settings_section( 'woocommence_altin_fiyati_plugin_options_section',
			__( 'Altın kurları Düzenleme', 'woocommence-altin-fiyati' ),
			'woocommence_altin_fiyati_fields',
			'woocommence_altin_fiyati' );

	

		add_settings_field( 'woocommence_altin_fiyati_options',
			__( 'Altın kurları Düzenleme', 'woocommence-altin-fiyati' ),
			'woocommence_altin_fiyati_fields',
			'woocommence_altin_fiyati_plugin_options_section',
			'woocommence_altin_fiyati' );

		add_action( 'woocommerce_product_options_pricing',     'woocommence_altin_fiyati_product_settings' );
		add_action( 'woocommerce_process_product_meta_simple', 'woocommence_altin_fiyati_process_simple_settings' );  
	}


	function woocommence_altin_fiyati_admin_menu() {

		if ( current_user_can( 'manage_woocommerce' ) ) {

			

			add_submenu_page( 'woocommerce',
				 __( 'Gold Prices and Gold Products', 'woocommence-altin-fiyati' ),
				 __( 'Altın kurları Düzenleme ', 'woocommence-altin-fiyati' ) ,
				 'manage_woocommerce',
				 'woocommence_altin_fiyati',
				 'woocommence_altin_fiyati_page');

		}
	}


	function woocommence_altin_fiyati_page() {
		
		do_action( 'admin_enqueue_scripts' );

		$tab = 'config';

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'woocommence_altin_fiyati' ) {
			
			if ( isset( $_GET['tab'] ) ) {
			
				if ( in_array( $_GET['tab'], array( 

													'config',
													'log',

													 ) ) ) {

					$tab = esc_attr( $_GET['tab'] );

				}

			}
		}

		?>

		<div class="wrap woocommerce">
			<div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"></div>
			<h2 class="nav-tab-wrapper"> 

				<a href="<?php echo admin_url('admin.php?page=woocommence_altin_fiyati&tab=config'); ?>" class="nav-tab <?php if ( $tab == 'config' ) echo 'nav-tab-active'; ?>">
				<?php esc_html_e( 'Settings', 'woocommence-altin-fiyati' ); ?></a>

				<a href="<?php echo admin_url('admin.php?page=woocommence_altin_fiyati&tab=log'); ?>" class="nav-tab <?php if ( $tab == 'log' ) echo 'nav-tab-active'; ?>"><?php esc_html_e( 'Log', 'woocommence-altin-fiyati' ); ?></a> 

			</h2>
		<?php

			switch ( $tab ) {

				case 'config':
					woocommence_altin_fiyati_display_config_tab();
				break;

				case 'log':
					woocommence_altin_fiyati_display_log_tab();
				break;

			}

		?>
		</div>
		<?php
	}


	function woocommence_altin_fiyati_display_config_tab() {

		if ( ! isset( $_REQUEST['settings-updated'] ) ) {

			$_REQUEST['settings-updated'] = false;

		}

		if ( false !== $_REQUEST['settings-updated'] ) {

			?>
				<div id="message" class="updated notice">
					<p><strong><?php esc_html_e( 'Your settings have been saved.', 'woocommence-altin-fiyati' ) ?></strong></p>
				</div>
			<?php

		}
		?>

		<h1><?php esc_html_e( 'Altın Fiyatları Ayarlama', 'woocommence-altin-fiyati' )?></h1>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'woocommence_altin_fiyati_options' );
				do_settings_sections( 'woocommence_altin_fiyati' );
			?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_html_e( 'Kaydet Kur Güncelle', 'woocommence-altin-fiyati' ) ?>" />
			</p>
		</form>
		<hr />

		<h1><?php esc_html_e( 'Ürünlerin altın karşılığı', 'woocommence-altin-fiyati' )?></h1>

		<?php

		$karats = get_option( 'woocommence_altin_fiyati_options' );

		if ( ! $karats ) {

			$karats = array( '24k' => 0, '22k' => 0, '18k' => 0, '14k' => 0 );

		}

		foreach( $karats as $key => $value ) {

			$value     = floatval( str_replace( ',', '', $value ) );

			$the_query = new WP_Query( array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
				'meta_key'       => 'is_gold_price_product',
				'meta_value'     => 'yes',
				'meta_query'     => array(
        		    'key'     => 'gold_price_karats',
            		'value'   =>  $key,
		            'compare' => '=',
        			),
				) ); 

			?>

			<h2><?php echo $key ?></h2>

			<?php

			if ( 0 == $the_query->found_posts ) {

				echo '<p>' . __( 'Bu Alanda Ürün yok', 'woocommence-altin-fiyati' ) . '</p>';

			} else {

				echo '
					<ol>';

				
				while ( $the_query->have_posts() ) {

					$the_query->the_post();

					$the_product = wc_get_product( $the_query->post->ID );

					$edit_url    = admin_url( 'post.php?post=' . $the_product->get_id() . '&action=edit' );
					$message     = '';

					$spread      = get_post_meta( $the_product->get_id(), 'gold_price_product_spread', true );

					$fee         = get_post_meta( $the_product->get_id(), 'gold_price_product_fee', true );
					$fee         = floatval( str_replace( ',', '', $fee   ) );

					echo '
						<li><a href="' . $edit_url. '">' . get_the_title(). '</a>';

					if ( ! $the_product->has_weight() ) {
						
						$message = __( 'Product has zero weight, can\'t calculate price based on weight.', 'woocommence-altin-fiyati' );

					} else {

						if ( $the_product->is_on_sale() ) {

							$message = __( 'Product was on sale, can\'t calculate sale price.', 'woocommence-altin-fiyati' );

						}

						$weight_price = $the_product->get_weight() * $value;
						$spread_price = $weight_price * ( ( $spread / 100 ) + 1 );
						$gold_price   = $spread_price +  $fee;

						echo ': ' . $the_product->get_weight() . woocommence_altin_fiyati_weight_description()  . ' * ' .  wc_price( str_replace(',', '', $karats[ $key ] ) ) ;


						echo ' (' . wc_price( $weight_price )  . ') ';

						if ( $spread ) {

							echo ' + ' . wc_price( $spread_price - $weight_price  ) . ' (' . $spread . '%)' ;

						}

						if ( $fee ) {

							echo ' + ' . wc_price( $fee );

						}

						echo ' = <strong>' . wc_price( $gold_price ) . '</strong>';

						if ( false === $_REQUEST['settings-updated'] ) {

							if ( wc_price( $gold_price ) != wc_price( $the_product->get_regular_price() ) ) {
									//haib translate yap
							$message .= ' | ' . sprintf( __( 'Uyarı! Bu ürün fiyatı (%s) Altın Fiyatı bazında değildir, güncellemek için "Kaydet butonu" butonuna basınız.', 'woocommence-altin-fiyati' ), wc_price( $the_product->get_regular_price() ) );

							}

						} else {

							$the_product->set_price( $gold_price );
							$the_product->set_regular_price( $gold_price );
							$the_product->set_sale_price( '' );
							$the_product->set_date_on_sale_from( '' );
							$the_product->set_date_on_sale_to( '' );

							$the_product->save();

							update_post_meta( $the_product->get_id(), '_price',         $gold_price );
							update_post_meta( $the_product->get_id(), '_regular_price', $gold_price );
							update_post_meta( $the_product->get_id(), '_sale_price', '' );
							update_post_meta( $the_product->get_id(), '_sale_price_dates_from', '' );
							update_post_meta( $the_product->get_id(), '_sale_price_dates_to', '' );

							$log_message = sprintf( __( 'Updated price for %1$s', 'woocommence-altin-fiyati' ), $the_product->get_title() );

							woocommence_altin_fiyati_log( $log_message );


						}
					}

					echo ' ' . $message . '</li>';
				}

				echo '
					</ol>';
			}

			wp_reset_query();
			wp_reset_postdata();

		}
	}


	function woocommence_altin_fiyati_display_log_tab() {

		$class = '';

		if ( isset($_GET['clear_log'] )	&& 1 == $_GET['clear_log']  && check_admin_referer( 'clear_log' ) ) {

			woocommence_altin_fiyati_delete_log();

		}

		?>

			<div class="panel woocommerce_options_panel">
               
				<h3><?php _e('Logged events', 'woocommence-altin-fiyati');?> <a href="<?php echo wp_nonce_url( admin_url('admin.php?page=woocommence_altin_fiyati&tab=log&clear_log=1' ), 'clear_log' ); ?>" class="button-primary right">    <?php _e( 'Clear Log', 'woocommence-altin-fiyati') ?> </a></h3>

				<table class="widefat">
					<thead>
						<tr>
							<th style="width: 150px"><?php _e( 'Timestamp', 'woocommence-altin-fiyati') ?></th>
							<th><?php _e( 'Event', 'woocommence-altin-fiyati') ?></th>
							<th><?php _e( 'User', 'woocommence-altin-fiyati') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 

						foreach ( woocommence_altin_fiyati_get_log() as $event ) {

							if ( ! $event[2] ) {

								$display_name = '&#9889;';

							} else {

								$user_data = get_userdata( $event[2] ); 
								$display_name = $user_data->display_name;

							}

							?>

							<tr <?php echo $class ?>>
								<td><?php echo woocommence_altin_fiyati_nice_time( $event[0] ); ?></td>
								<td><?php echo $event[1]; ?></td>
								<td><?php echo $display_name; ?></td>
							</tr>

							<?php 

								if ( empty( $class ) )  {

									$class = ' class="alternate"';

								} else {

									$class = '';

								}
						}
						?>

					</tbody>
				</table>
			</div>
		
		<?php
	}


	function woocommence_altin_fiyati_better_human_time_diff( $from, $to = '', $limit = 3 ) {

	
		$units = apply_filters( 'time_units', array(
				31556926 => array( __('%s year'),  __('%s years') ),
				2629744  => array( __('%s month'), __('%s months') ),
				604800   => array( __('%s week'),  __('%s weeks') ),
				86400    => array( __('%s day'),   __('%s days') ),
				3600     => array( __('%s hour'),  __('%s hours') ),
				60       => array( __('%s min'),   __('%s mins') ),
				1        => array( __('%s sec'),   __('%s secs') ),
		) );

		if ( empty($to) ) {
			$to = time();
		}

		$from = (int) $from;
		$to   = (int) $to;
		
		$t_diff = $to - $from;
		
		$diff = (int) abs( $to - $from );

		$items = 0;
		$output = array();

		foreach ( $units as $unitsec => $unitnames ) {

				if ( $items >= $limit ) {
					break;
				}

				if ( $diff < $unitsec ) {
					continue;
				}

				$numthisunits = floor( $diff / $unitsec );
				$diff         = $diff - ( $numthisunits * $unitsec );
				$items++;

				if ( $numthisunits > 0 ) {
					$output[] = sprintf( _n( $unitnames[0], $unitnames[1], $numthisunits ), $numthisunits );
				}

		}


		$separator = _x( ', ', 'human_time_diff' );
		
		if ( ! empty( $output ) ) {

			$human_time = implode( $separator, $output );

		} else {

			$smallest   = array_pop( $units );
			$human_time = sprintf( $smallest[0], 1 );

		}

		if ( $t_diff < 0 ) {

			return sprintf( __( 'in %s' ), $human_time );

		} else {

			return '<strong>' . sprintf( __( 'is %s late' ), $human_time ) . '</strong>';
		}
	}


	function woocommence_altin_fiyati_fields() {

		$karats          = get_option( 'woocommence_altin_fiyati_options' );
		$currency_pos    = get_option( 'woocommerce_currency_pos' );
		$currency_symbol = get_woocommerce_currency_symbol();

		?>		<?php $habibokm = file_get_contents ("https://finans.truncgil.com/today.json");
				$habib = json_decode($habibokm, true);
                echo "<span>En Son Gelen Kurlar:</span> ". $habib["Update_Date"]; ?>
			<table class="form-table widefat">
			
				<thead>
					<tr valign="top">
						<th scope="col" style="padding-left: 1em;"><?php esc_html_e( 'Kanat', 'woocommence-altin-fiyati' )?></th>
						<th scope="col"><?php esc_html_e( 'Fiyat', 'woocommence-altin-fiyati' ) ?></td>
						<th scope="col"><?php esc_html_e( 'Ağırlık(Değiştirmek için woocommence ayarlardan değişebilirsin))', 'woocommence-altin-fiyati' ) ?></td>
					</tr>
				</thead>

				<tr valign="top">
					<th scope="row" style="padding-left: 1em;"><label for="woocommence_altin_fiyati_options_24">24k ( Gram Altın )</label></th>
					<td>
					
				<?php
				        $ayargram= $habib["gram-altin"]["Satış"];
						$ayargramayri = str_replace(',', '.', $ayargram);
						$ayar22= $habib["22-ayar-bilezik"]["Satış"];
						$ayarayri22 = str_replace(',', '.', $ayar22);
						$ayar18= $habib["18-ayar-altin"]["Satış"];
						$ayarayri18 = str_replace(',', '.', $ayar18);
						$ayar14= $habib["14-ayar-altin"]["Satış"];
						$ayarayri14 = str_replace(',', '.', $ayar14);
					
				

					$input = ' <input style="vertical-align: baseline; text-align: right;" id="woocommence_altin_fiyati_options_24" name="woocommence_altin_fiyati_options[24k]" size="10" type="text" value="' . $ayargramayri. '" /> ';

					switch ( $currency_pos ) {
						case 'left' :
							echo $currency_symbol . $input;
						break;
						case 'right' :
							echo $input . $currency_symbol;
						break;
						case 'left_space' :
							echo $currency_symbol . '&nbsp;' . $input;
						break;
						case 'right_space' :
							echo $input . '&nbsp;' . $currency_symbol;
						break;
					}

				?>
					</td>
					<td> / <?php echo woocommence_altin_fiyati_weight_description(); ?></td>
				</tr>
				<tr valign="top" class="alternate">
					<th scope="row" style="padding-left: 1em;"><label for="woocommence_altin_fiyati_options_22">22k ( 22 Ayar Bilezik )</label></th>
					<td>

				<?php
				
				$input = '<input style="vertical-align: baseline; text-align: right;" id="woocommence_altin_fiyati_options_22" name="woocommence_altin_fiyati_options[22k]" size="10" type="text" value="' . $ayarayri22 . '" />';

				switch ($currency_pos) {
					case 'left' :
						echo $currency_symbol . $input;
					break;
					case 'right' :
						echo $input . $currency_symbol;
					break;
					case 'left_space' :
						echo $currency_symbol . '&nbsp;' . $input;
					break;
					case 'right_space' :
						echo $input . '&nbsp;' . $currency_symbol;
					break;
				}

				?>
					</td>
					<td> / <?php echo woocommence_altin_fiyati_weight_description(); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row" style="padding-left: 1em;"><label for="woocommence_altin_fiyati_options_18">18k ( 18 Ayar Altin )</label></th>
					<td>
				<?php

				$input = '<input style="vertical-align: baseline; text-align: right;" id="woocommence_altin_fiyati_options_18" name="woocommence_altin_fiyati_options[18k]" size="10" type="text" value="' . $ayarayri18 . '" />';

				switch ($currency_pos) {
					case 'left' :
						echo $currency_symbol . $input;
					break;
					case 'right' :
						echo $input . $currency_symbol;
					break;
					case 'left_space' :
						echo $currency_symbol . '&nbsp;' . $input;
					break;
					case 'right_space' :
						echo $input . '&nbsp;' . $currency_symbol;
					break;
				}
				?>
					</td>
					<td> / <?php echo woocommence_altin_fiyati_weight_description(); ?></td>
				</tr>
				<tr valign="top" class="alternate">
					<th scope="row" style="padding-left: 1em;"><label for="woocommence_altin_fiyati_options_14">14k (14 Ayar Altin)</label></th>
					<td>
				<?php

				$input = '<input style="vertical-align: baseline; text-align: right;" id="woocommence_altin_fiyati_options_14" name="woocommence_altin_fiyati_options[14k]" size="10" type="text" value="' . $ayarayri14 . '" />';

				switch ($currency_pos) {
					case 'left' :
						echo $currency_symbol . $input;
					break;
					case 'right' :
						echo $input . $currency_symbol;
					break;
					case 'left_space' :
						echo $currency_symbol . '&nbsp;' . $input;
					break;
					case 'right_space' :
						echo $input . '&nbsp;' . $currency_symbol;
					break;
				}
				?>
					</td>
					<td> / <?php echo woocommence_altin_fiyati_weight_description(); ?></td>
				</tr>
			</table>
		<?php
	}


	function woocommence_altin_fiyati_validate_options( $input ) {
		foreach ( $input as $key =>$value ) {
			$input[ $key ] =  wp_filter_nohtml_kses( $value );
		}
		return $input;
	}


	function woocommence_altin_fiyati_product_settings() {

		global $thepostid;

		$is_gold_price_product = get_post_meta( $thepostid, 'is_gold_price_product', true );

		$karats  = get_post_meta( $thepostid, 'gold_price_karats', true );
		$spread  = get_post_meta( $thepostid, 'gold_price_product_spread', true );
		$fee     = get_post_meta( $thepostid, 'gold_price_product_fee', true );

		// easy access to weight
		$product        = wc_get_product( $thepostid );
		$product_weight = $product->get_weight();

		?>
		</div>
		<div class="options_group gold_price show_if_simple show_if_external hidden">
			<p class="form-field"     style="background-color: #c34b04;">
				<label style="color:#fff;"for="is_gold_price_product"><?php  esc_html_e( 'Bu ürün bir Altın ', 'woocommence-altin-fiyati' )?></label>
				<input type="checkbox" class="checkbox" id="is_gold_price_product" name="is_gold_price_product" <?php checked( $is_gold_price_product, 'yes' ); ?> />
			</p>

			<p class="form-field">
				<label for="karats">
				<?php  esc_html_e( 'Kaç Kanat ', 'woocommence-altin-fiyati' )?></label>
				<select name="karats" id='karats' style="float: none;">
					<option value="24k" <?php selected( '24k', $karats );?>><?php  esc_html_e( '24k', 'woocommence-altin-fiyati' )?></option>
					<option value="22k" <?php selected( '22k', $karats );?>><?php  esc_html_e( '22k', 'woocommence-altin-fiyati' )?></option>
					<option value="18k" <?php selected( '18k', $karats );?>><?php  esc_html_e( '18k', 'woocommence-altin-fiyati' )?></option>
					<option value="14k" <?php selected( '14k', $karats );?>><?php  esc_html_e( '14k', 'woocommence-altin-fiyati' )?></option>
				</select>

			</p>

			<p class="form-field">
				<label for="product_weight"><?php  esc_html_e( 'Ağırlık', 'woocommence-altin-fiyati' ); echo ' (' . get_option( 'woocommerce_weight_unit' ) . ')'?></label>
				<input type="text" class="short" id="product_weight" name="product_weight" value="<?php echo $product_weight; ?>"  />
				</br></br><a href="/wp-admin/admin.php?page=wc-settings">Ürün Ağırlık Birimi değiştirme</a>
			</p>
			
			
			

			<p class="form-field">
				<label for="fee"><?php  esc_html_e( 'İşcilik ', 'woocommence-altin-fiyati' ); echo ' (' . get_woocommerce_currency_symbol() . ')'; ?></label>
				<input type="text" class="short" id="fee" name="fee" value="<?php echo $fee; ?>"  />
			</p>
			<p>
			<a href="https://github.com/habibokumus">Bu ve bunun gibi içeriklere Erişmek için Github dan beni takip edebilirisiniz</a>
			</p>	
		<?php
	}


	function woocommence_altin_fiyati_process_simple_settings( $post_id ) {

		$message      = '';
		$gold_product = get_post_meta( $post_id, 'is_gold_price_product', true );

		$is_gold_price_product = isset( $_POST['is_gold_price_product'] ) ? 'yes' : 'no';
		$changed_gold_status   = update_post_meta( $post_id, 'is_gold_price_product', $is_gold_price_product );

		update_post_meta( $post_id, 'gold_price_karats', wc_clean( $_POST['karats'] ) );


		update_post_meta( $post_id, 'gold_price_product_spread', wc_clean( $_POST['spread'] ) );
		update_post_meta( $post_id, 'gold_price_product_fee',    wc_clean( $_POST['fee'] ) );

		$product = wc_get_product( $post_id );
		$product->set_weight( wc_clean( $_POST['product_weight'] ) );
		$product->save();

		if ( 'no' == $gold_product ) {

			if ( $changed_gold_status ) { 

				$message = sprintf( __( 'Checked <strong>%1$s</strong> | Purity: %2$s | Spread: %3$s%% | Fee: %4$s', 'woocommence-altin-fiyati'  ), $product->get_title(), $_POST['karats'], $_POST['spread'], wc_price( $_POST['fee']) );

			}

		} else {

			if ( $changed_gold_status  ) {

				$message = sprintf( __( 'Unchecked <strong>%1$s</strong>, no longer a gold product.', 'woocommence-altin-fiyati'  ), $product->get_title() );

			} else { 

				$message = sprintf( __( 'Updated <strong>%1$s</strong> | Purity: %2$s | Spread: %3$s%% | Fee: %4$s', 'woocommence-altin-fiyati'  ), $product->get_title(), $_POST['karats'], $_POST['spread'], wc_price( $_POST['fee']) );

			}

		}

		if ( $message ) {
			woocommence_altin_fiyati_log( $message );
		}

	}


	function woocommence_altin_fiyati_action_links( $links, $file ) {

		if ( plugin_basename( __FILE__ ) == $file ) {

			$woocommence_altin_fiyati_settings_link = '<a href="' . get_admin_url() . 'admin.php?page=woocommence_altin_fiyati&tab=config">' . __('Settings', 'woocommence-altin-fiyati' ) . '</a>';

			
			array_unshift( $links, $woocommence_altin_fiyati_settings_link );
		}

		return $links;
	}


	
	function woocommence_altin_fiyati_nice_time( $time, $args = false ) {

		$defaults = array( 'format' => 'date_and_time' );
		extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

		if ( ! $time)
			return false;

		if ( $format == 'date' )
			return date( get_option( 'date_format' ), $time );

		if ( $format == 'time' )
			return date( get_option( 'time_format' ), $time );

		if ( $format == 'date_and_time' ) //get_option( 'time_format' )
			return date( get_option( 'date_format' ), $time ) . " " . date( 'H:i:s', $time );

		return false;
	}


	function woocommence_altin_fiyati_log( $event ) {

		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;

		$log = get_option( 'woocommence_altin_fiyati_log' );

		$time_difference = get_option( 'gmt_offset' ) * 3600;
		$time            = time() + $time_difference;

		if ( ! is_array( $log ) ) {
			$log = array();
			array_push( $log, array( $time, __( 'Log Started.', 'woocommence-altin-fiyati' ), $current_user_id ) );
		}

		array_push( $log, array( $time, $event, $current_user_id ) );
		return update_option( 'woocommence_altin_fiyati_log', $log );
	}


	function woocommence_altin_fiyati_get_log() {

		$log = get_option( 'woocommence_altin_fiyati_log' );

		
		if ( ! is_array( $log ) ) {
			$current_user    = wp_get_current_user();
			$current_user_id = $current_user->ID;
			$log             = array();
			$time_difference = get_option( 'gmt_offset' ) * 3600;
			$time            = time() + $time_difference;
			array_push( $log, array( $time, __( 'Log Started.', 'woocommence-altin-fiyati' ), $current_user_id ) );
			update_option( 'woocommence_altin_fiyati_log', $log );
		}

		return array_reverse( get_option( 'woocommence_altin_fiyati_log' ) );
	}


	function woocommence_altin_fiyati_delete_log() {

		$current_user    = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$log             = array();
		$time_difference = get_option( 'gmt_offset' ) * 3600;
		$time            = time() + $time_difference;

		array_push( $log, array( $time, __( 'Log cleared.', 'woocommence-altin-fiyati' ), $current_user_id ) );
		
		update_option( 'woocommence_altin_fiyati_log', $log );
	}

}
