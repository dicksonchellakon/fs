<?php
/**
 * WooCommerce Jetpack Checkout Files Upload
 *
 * The WooCommerce Jetpack Checkout Files Upload class.
 *
 * @version 2.4.6
 * @since   2.4.5
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Checkout_Files_Upload' ) ) :

class WCJ_Checkout_Files_Upload extends WCJ_Module {

	/**
	 * Constructor.
	 */
	function __construct() {

		$this->id         = 'checkout_files_upload';
		$this->short_desc = __( 'Checkout Files Upload', 'woocommerce-jetpack' );
		$this->desc       = __( 'Let customers upload files on WooCommerce checkout.', 'woocommerce-jetpack' );
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_action( 'add_meta_boxes', array( $this, 'add_file_admin_order_meta_box' ) );
			add_action( 'init', array( $this, 'process_checkout_files_upload' ) );
			/* add_action(
				get_option( 'wcj_checkout_files_upload_hook', 'woocommerce_before_checkout_form' ),
				array( $this, 'add_files_upload_form_to_checkout_frontend' )
			); */
			$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
			for ( $i = 1; $i <= $total_number; $i++ ) {
				add_action(
					get_option( 'wcj_checkout_files_upload_hook_' . $i, 'woocommerce_before_checkout_form' ),
					array( $this, 'add_files_upload_form_to_checkout_frontend' ),
					get_option( 'wcj_checkout_files_upload_hook_priority_' . $i, 10 )
				);
			}
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'add_files_to_order' ), PHP_INT_MAX, 2 );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate' ) );
		}
	}

	/**
	 * validate.
	 */
	function validate( $posted ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( 'yes' === get_option( 'wcj_checkout_files_upload_required_' . $i, 'no' ) && ! isset( $_SESSION[ 'wcj_checkout_files_upload_' . $i ] ) ) {
				// Is required
				wc_add_notice( get_option( 'wcj_checkout_files_upload_notice_required_' . $i, __( 'File is required!', 'woocommerce-jetpack' ) ), 'error' );
			}
			if ( '' != ( $file_accept = get_option( 'wcj_checkout_files_upload_file_accept_' . $i, '' ) ) && isset( $_SESSION[ 'wcj_checkout_files_upload_' . $i ] ) ) {
				// Validate file type
				$file_accept = explode( ',', $file_accept );
				if ( is_array( $file_accept ) && ! empty( $file_accept ) ) {
					$file_name = $_SESSION[ 'wcj_checkout_files_upload_' . $i ]['name'];
					$file_type = '.' . pathinfo( $file_name, PATHINFO_EXTENSION );
					if ( ! in_array( $file_type, $file_accept ) ) {
						wc_add_notice( sprintf( get_option( 'wcj_checkout_files_upload_notice_wrong_file_type_' . $i,
							__( 'Wrong file type: "%s"!', 'woocommerce-jetpack' ) ), $file_name ), 'error' );
					}
				}
			}
		}
	}

	/**
	 * add_file_admin_order_meta_box.
	 */
	function add_file_admin_order_meta_box() {
		$screen   = 'shop_order';
		$context  = 'side';
		$priority = 'high';
		add_meta_box(
			'wc-jetpack-' . $this->id,
			__( 'Booster', 'woocommerce-jetpack' ) . ': ' . __( 'Uploaded Files', 'woocommerce-jetpack' ),
			array( $this, 'create_file_admin_order_meta_box' ),
			$screen,
			$context,
			$priority
		);
	}

	/**
	 * create_file_admin_order_meta_box.
	 */
	function create_file_admin_order_meta_box() {
		$order_id = get_the_ID();
		$html = '';
		$total_files = get_post_meta( $order_id, '_' . 'wcj_checkout_files_total_files', true );
		for ( $i = 1; $i <= $total_files; $i++ ) {
			$order_file_name = get_post_meta( $order_id, '_' . 'wcj_checkout_files_upload_'           . $i, true );
			$real_file_name  = get_post_meta( $order_id, '_' . 'wcj_checkout_files_upload_real_name_' . $i, true );
			if ( '' != $order_file_name ) {
				$html .= '<p><a href="' . add_query_arg(
					array(
						'wcj_download_checkout_file_admin' => $order_file_name,
						'wcj_checkout_file_number'         => $i,
					) ) . '">' . $real_file_name . '</a></p>';
			}
		}
		echo $html;
	}

	/**
	 * add_files_to_order.
	 */
	function add_files_to_order( $order_id, $posted ) {
		$upload_dir = wcj_get_wcj_uploads_dir( 'checkout_files_upload' );
		if ( ! file_exists( $upload_dir ) ) {
			mkdir( $upload_dir, 0755, true );
		}
		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( isset( $_SESSION[ 'wcj_checkout_files_upload_' . $i ] ) ) {
				$file_name          = $_SESSION[ 'wcj_checkout_files_upload_' . $i ]['name'];
				$ext                = pathinfo( $file_name, PATHINFO_EXTENSION );
				$download_file_name = $order_id . '_' . $i . '.' . $ext;
				$file_path          = $upload_dir . '/' . $download_file_name;
				$tmp_file_name      = $_SESSION[ 'wcj_checkout_files_upload_' . $i ]['tmp_name'];
				$file_data          = file_get_contents( $tmp_file_name );
				file_put_contents( $file_path, $file_data );
				unlink( $tmp_file_name );
				unset( $_SESSION[ 'wcj_checkout_files_upload_' . $i ] );
				update_post_meta( $order_id, '_' . 'wcj_checkout_files_upload_' . $i, $download_file_name );
				update_post_meta( $order_id, '_' . 'wcj_checkout_files_upload_real_name_' . $i, $file_name );
			}
		}
		update_post_meta( $order_id, '_' . 'wcj_checkout_files_total_files', $total_number );
	}

	/**
	 * process_checkout_files_upload.
	 */
	function process_checkout_files_upload() {
		if ( ! session_id() ) {
			session_start();
		}
		// Remove file
		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( isset( $_POST[ 'wcj_remove_checkout_file_' . $i ] ) ) {
				$file_name = 'wcj_checkout_files_upload_' . $i;
				unlink( $_SESSION[ $file_name ]['tmp_name'] );
				wc_add_notice( sprintf( get_option( 'wcj_checkout_files_upload_notice_success_remove_' . $i,
					__( 'File "%s" was successfully removed.', 'woocommerce-jetpack' ) ), $_SESSION[ $file_name ]['name'] ) );
				unset( $_SESSION[ $file_name ] );
			}
		}
		// Upload file
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( isset( $_POST[ 'wcj_upload_checkout_file_' . $i ] ) ) {
				$file_name = 'wcj_checkout_files_upload_' . $i;
				if ( isset( $_FILES[ $file_name ] ) && '' != $_FILES[ $file_name ]['tmp_name'] ) {
					$_SESSION[ $file_name ] = $_FILES[ $file_name ];
					$tmp_dest_file = tempnam( sys_get_temp_dir(), 'wcj' );
					move_uploaded_file( $_SESSION[ $file_name ]['tmp_name'], $tmp_dest_file );
					$_SESSION[ $file_name ]['tmp_name'] = $tmp_dest_file;
					wc_add_notice( sprintf( get_option( 'wcj_checkout_files_upload_notice_success_upload_' . $i,
						__( 'File "%s" was successfully uploaded.', 'woocommerce-jetpack' ) ), $_SESSION[ $file_name ]['name'] ) );
				} else {
					wc_add_notice( get_option( 'wcj_checkout_files_upload_notice_upload_no_file_' . $i,
						__( 'Please select file to upload!', 'woocommerce-jetpack' ) ), 'notice' );
				}
			}
		}
		// Admin file download
		if ( isset( $_GET['wcj_download_checkout_file_admin'] ) ) {
			$tmp_file_name = wcj_get_wcj_uploads_dir( 'checkout_files_upload' ) . '/' . $_GET['wcj_download_checkout_file_admin'];
			$file_name     = get_post_meta( $_GET['post'], '_' . 'wcj_checkout_files_upload_real_name_' . $_GET['wcj_checkout_file_number'], true );
			if ( is_super_admin() || is_shop_manager() ) {
				header( "Expires: 0" );
				header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
				header( "Cache-Control: private", false );
				header( 'Content-disposition: attachment; filename=' . $file_name );
				header( "Content-Transfer-Encoding: binary" );
				header( "Content-Length: ". filesize( $tmp_file_name ) );
				readfile( $tmp_file_name );
				exit();
			}
		}
		// User file download
		if ( isset( $_GET['wcj_download_checkout_file'] ) ) {
			$tmp_file_name = $_SESSION[ $_GET['wcj_download_checkout_file'] ]['tmp_name'];
			$file_name     = $_SESSION[ $_GET['wcj_download_checkout_file'] ]['name'];
			header( "Expires: 0" );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( "Cache-Control: private", false );
			header( 'Content-disposition: attachment; filename=' . $file_name );
			header( "Content-Transfer-Encoding: binary" );
			header( "Content-Length: ". filesize( $tmp_file_name ) );
			readfile( $tmp_file_name );
			exit();
		}
		/* // Upload all files
		if ( isset( $_POST['wcj_checkout_files_upload_submit'] ) ) {
			$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
			for ( $i = 1; $i <= $total_number; $i++ ) {
				$file_name = 'wcj_checkout_files_upload_' . $i;
				if ( isset( $_FILES[ $file_name ] ) && '' != $_FILES[ $file_name ]['tmp_name'] ) {
					$_SESSION[ $file_name ] = $_FILES[ $file_name ];
					$tmp_dest_file = tempnam( sys_get_temp_dir(), 'wcj' );
					move_uploaded_file( $_SESSION[ $file_name ]['tmp_name'], $tmp_dest_file );
					$_SESSION[ $file_name ]['tmp_name'] = $tmp_dest_file;
					wc_add_notice( sprintf( get_option( 'wcj_checkout_files_upload_notice_success_upload_' . $i,
						__( 'File "%s" was successfully uploaded.', 'woocommerce-jetpack' ) ), $_SESSION[ $file_name ]['name'] ) );
				}
			}
		} */
	}

	/**
	 * add_files_upload_form_to_checkout_frontend.
	 *
	 * @version 2.4.6
	 */
	function add_files_upload_form_to_checkout_frontend() {
		$html = '';
//		$html .= '<form enctype="multipart/form-data" action="" method="POST">';
		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
//		$html .= '<table>';
		$current_filter = current_filter();
		$current_filter_priority = wcj_current_filter_priority();
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if (
				'yes' === get_option( 'wcj_checkout_files_upload_enabled_' . $i, 'yes' ) &&
				$current_filter === get_option( 'wcj_checkout_files_upload_hook_' . $i, 'woocommerce_before_checkout_form' ) &&
				$current_filter_priority == get_option( 'wcj_checkout_files_upload_hook_priority_' . $i, 10 )
			) {
				$html .= '<form enctype="multipart/form-data" action="" method="POST">';
				$html .= '<table>';
				if ( '' != ( $the_label = get_option( 'wcj_checkout_files_upload_label_' . $i, '' ) ) ) {
					$html .= '<tr>';
					$html .= '<td colspan="2">';
					$html .= '<label for="wcj_checkout_files_upload_' . $i . '">' . $the_label . '</label>';
					if ( 'yes' === get_option( 'wcj_checkout_files_upload_required_' . $i, 'no' ) ) {
						$html .= '&nbsp;<abbr class="required" title="required">*</abbr>';
					}
					$html .= '</td>';
					$html .= '</tr>';
				}
				if ( ! isset( $_SESSION[ 'wcj_checkout_files_upload_' . $i ] ) ) {
					$html .= '<tr>';
					$html .= '<td style="width:50%;">';
					$html .= '<input type="file" name="wcj_checkout_files_upload_' . $i . '" id="wcj_checkout_files_upload_' . $i .
						'" accept="' . get_option( 'wcj_checkout_files_upload_file_accept_' . $i, '' ) . '">';
					$html .= '</td>';
					$html .= '<td style="width:50%;">';
					$html .= '<input type="submit"' .
						' class="button alt"' .
						' style="width:100%;"' .
						' name="wcj_upload_checkout_file_' . $i . '"' .
						' id="wcj_upload_checkout_file_' . $i . '"' .
						' value="'      . get_option( 'wcj_checkout_files_upload_label_upload_button_' . $i, __( 'Upload', 'woocommerce-jetpack' ) ) . '"' .
						' data-value="' . get_option( 'wcj_checkout_files_upload_label_upload_button_' . $i, __( 'Upload', 'woocommerce-jetpack' ) ) . '">';
					$html .= '</td>';
					$html .= '</tr>';
				} else {
					$html .= '<tr>';
					$html .= '<td style="width:50%;">';
					$html .= '<a href="' . add_query_arg( 'wcj_download_checkout_file', 'wcj_checkout_files_upload_' . $i ) . '">' .
						$_SESSION[ 'wcj_checkout_files_upload_' . $i ]['name'] . '</a>';
					$html .= '</td>';
					$html .= '<td style="width:50%;">';
					$html .= '<input type="submit"' .
						' class="button"' .
						' style="width:100%;"' .
						' name="wcj_remove_checkout_file_' . $i . '"' .
						' id="wcj_remove_checkout_file_' . $i . '"' .
						' value="'      . get_option( 'wcj_checkout_files_upload_label_remove_button_' . $i, __( 'Remove', 'woocommerce-jetpack' ) ) . '"' .
						' data-value="' . get_option( 'wcj_checkout_files_upload_label_remove_button_' . $i, __( 'Remove', 'woocommerce-jetpack' ) ) . '">';
					$html .= '</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
				$html .= '</form>';
			}
		}
		/* if ( $total_number > 1 ) {
			$html .= '<tr>';
			$html .= '<td colspan="2">';
			$html .= '<input type="submit"' .
				' class="button alt"' .
				' name="wcj_checkout_files_upload_submit"' .
				' id="wcj_checkout_files_upload_submit"' .
				' value="' . __( 'Upload All', 'woocommerce-jetpack' ) . '"' .
				' data-value="' . __( 'Upload All', 'woocommerce-jetpack' ) . '"></p>';
			$html .= '</td>';
			$html .= '</tr>';
		} */
//		$html .= '</table>';
//		$html .= '</form>';
		echo $html;
	}

	/**
	 * get_settings.
	 *
	 * @todo styling options; options to place form on: cart, order review and my account pages.
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Options', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'id'       => 'wcj_checkout_files_upload_options',
			),
			/* array(
				'title'    => __( 'Position', 'woocommerce-jetpack' ),
				'id'       => 'wcj_checkout_files_upload_hook',
				'default'  => 'woocommerce_before_checkout_form',
				'type'     => 'select',
				'options'  => array(
					'woocommerce_before_checkout_form' => __( 'Before checkout form', 'woocommerce-jetpack' ),
					'woocommerce_after_checkout_form'  => __( 'After checkout form', 'woocommerce-jetpack' ),
				),
			), */
			array(
				'title'    => __( 'Total Files', 'woocommerce-jetpack' ),
				'id'       => 'wcj_checkout_files_upload_total_number',
				'default'  => 1,
				'type'     => 'custom_number',
				'desc'     => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes' => array_merge(
					is_array( apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) ) ?
						apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) : array(),
					array( 'step' => '1', 'min'  => '1', )
				),
			),
		);
		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'File', 'woocommerce-jetpack' ) . ' #' . $i,
					'id'       => 'wcj_checkout_files_upload_enabled_' . $i,
					'desc'     => __( 'Enabled', 'woocommerce-jetpack' ),
					'type'     => 'checkbox',
					'default'  => 'yes',
				),
				array(
					'id'       => 'wcj_checkout_files_upload_required_' . $i,
					'desc'     => __( 'Required', 'woocommerce-jetpack' ),
					'type'     => 'checkbox',
					'default'  => 'no',
				),
				array(
					'id'       => 'wcj_checkout_files_upload_hook_' . $i,
					'desc'     => __( 'Position', 'woocommerce-jetpack' ),
					'default'  => 'woocommerce_before_checkout_form',
					'type'     => 'select',
					'options'  => array(
						'woocommerce_before_checkout_form'              => __( 'Before checkout form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_form'               => __( 'After checkout form', 'woocommerce-jetpack' ),

						/* 'woocommerce_before_checkout_billing_form'      => __( 'Before checkout billing form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_billing_form'       => __( 'After checkout billing form', 'woocommerce-jetpack' ),
						'woocommerce_before_checkout_registration_form' => __( 'Before checkout registration form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_registration_form'  => __( 'After checkout registration form', 'woocommerce-jetpack' ), */

						/* 'woocommerce_before_checkout_shipping_form'     => __( 'Before checkout shipping form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_shipping_form'      => __( 'After checkout shipping form', 'woocommerce-jetpack' ),
						'woocommerce_before_order_notes'                => __( 'Before order notes', 'woocommerce-jetpack' ),
						'woocommerce_after_order_notes'                 => __( 'After order notes', 'woocommerce-jetpack' ), */

						/* 'woocommerce_checkout_before_customer_details'  => __( 'Before checkout customer details', 'woocommerce-jetpack' ),
						'woocommerce_checkout_billing'                  => __( 'Inside checkout billing', 'woocommerce-jetpack' ),
						'woocommerce_checkout_shipping'                 => __( 'Inside checkout shipping', 'woocommerce-jetpack' ),
						'woocommerce_checkout_after_customer_details'   => __( 'After checkout customer details', 'woocommerce-jetpack' ),
						'woocommerce_checkout_before_order_review'      => __( 'Before checkout order review', 'woocommerce-jetpack' ),
						'woocommerce_checkout_order_review'             => __( 'Inside checkout order review', 'woocommerce-jetpack' ),
						'woocommerce_checkout_after_order_review'       => __( 'After checkout order review', 'woocommerce-jetpack' ), */
					),
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Position order', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_hook_priority_' . $i,
					'default'  => 20,
					'type'     => 'number',
					'custom_attributes' => array( 'min' => '0' ),
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Label', 'woocommerce-jetpack' ),
					'desc_tip' => __( 'Leave blank to disable label', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_label_' . $i,
					'default'  => __( 'Please select file to upload', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Accepted file types', 'woocommerce-jetpack' ),
					'desc_tip' => __( 'Accepted file types. E.g.: ".jpg,.jpeg,.png". Leave blank to accept all files', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_file_accept_' . $i,
					'default'  => '.jpg,.jpeg,.png',
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Label: Upload button', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_label_upload_button_' . $i,
					'default'  =>  __( 'Upload', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Label: Remove button', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_label_remove_button_' . $i,
					'default'  =>  __( 'Remove', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Notice: Wrong file type', 'woocommerce-jetpack' ),
					'desc_tip' => __( '%s will be replaced with file name', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_notice_wrong_file_type_' . $i,
					'default'  =>  __( 'Wrong file type: "%s"!', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Notice: File is required', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_notice_required_' . $i,
					'default'  =>  __( 'File is required!', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Notice: File was successfully uploaded', 'woocommerce-jetpack' ),
					'desc_tip' => __( '%s will be replaced with file name', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_notice_success_upload_' . $i,
					'default'  =>  __( 'File "%s" was successfully uploaded.', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Notice: No file selected', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_notice_upload_no_file_' . $i,
					'default'  =>  __( 'Please select file to upload!', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
				array(
					'desc'     => __( 'Notice: File was successfully removed', 'woocommerce-jetpack' ),
					'desc_tip' => __( '%s will be replaced with file name', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_files_upload_notice_success_remove_' . $i,
					'default'  =>  __( 'File "%s" was successfully removed.', 'woocommerce-jetpack' ),
					'type'     => 'text',
					'css'      => 'width:250px;',
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_checkout_files_upload_options',
			),
		) );
		return $this->add_standard_settings( $settings );
	}
}

endif;

return new WCJ_Checkout_Files_Upload();
