<?php
/**
 * Uninstall ns_digital_data_seller_plugin
 */

if( !defined('WP_UNINSTALL_PLUGIN') )
    exit();

// プラグインフォルダーパスを定義
if ( ! defined( 'NSDDSP_DIR' ) ) 
{
	define( 'NSDDSP_DIR', plugin_dir_path( __FILE__ ) );
}
require_once NSDDSP_DIR. 'includes/ns_digital_data_seller_plugin_define.php';

// カスタム投稿をすべて削除
$nsddsp_post_types = array( NSDDSP_POST_ID_DOWNFILE, NSDDSP_POST_ID_IPNDATA );
foreach ( $nsddsp_post_types as $post_type ) 
{
	$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1 ) );
	if ( $items ) 
	{
		foreach ( $items as $item ) 
		{
			wp_delete_post( $item, true);
		}
	}
}

//オプションの削除
delete_option(NSDDSP_OPTION_ID_TOKENWORD);
delete_option(NSDDSP_OPTION_ID_USE_SANDBOX);
delete_option(NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL);
delete_option(NSDDSP_OPTION_ID_RECORD_LOG);

delete_option(NSDDSP_OPTION_ID_EXPIRE_HOURS);
delete_option(NSDDSP_OPTION_ID_IS_COPY_DOWNFILE);

delete_option(NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL);
delete_option(NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT);
delete_option(NSDDSP_OPTION_ID_PAYER_MAIL_BODY);

delete_option(NSDDSP_OPTION_ID_SELLER_FROM_EMAIL);
delete_option(NSDDSP_OPTION_ID_SELLER_FROM_NAME);
delete_option(NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS);


delete_option(NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL);
delete_option(NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL);
delete_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT);
delete_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY);

delete_option(NSDDSP_OPTION_ID_COMPLETED_TXN_IDS);
