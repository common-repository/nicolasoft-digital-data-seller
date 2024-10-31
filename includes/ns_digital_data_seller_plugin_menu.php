<?php

//メールの再送
function nsddsp_resend_email( $post_id )
{   
    $ret = false;
	$post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_IPN_DATA, true);	
    if ($post_meta) 
    {
        $args = array();
        $args['first_name']     = $post_meta['ipn_first_name'];
        $args['last_name']      = $post_meta['ipn_last_name'];	
        $args['payer_email']    = $post_meta['ipn_email'];
        $args['transaction_id'] = $post_meta['ipn_txn_id'];
        $args['purchase_date']  = $post_meta['ipn_purchase_date'];
        $args['item_name']      = $post_meta['ipn_paypal_item_name']; 
        
        $ipn_paypal_item_name   = $args['item_name'];        

        //ダウンロードURL生成
        $expire_hours = get_option(NSDDSP_OPTION_ID_EXPIRE_HOURS);
        $expire      = date_i18n("ymdH",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));

        //期限のタグを取得
        $expire_y = date_i18n("Y",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
        $expire_m = date_i18n("m",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
        $expire_d = date_i18n("d",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
        $expire_h = date_i18n("H",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
        $args['expire_y'] = $expire_y; 
        $args['expire_m'] = $expire_m; 
        $args['expire_d'] = $expire_d; 
        $args['expire_h'] = $expire_h; 

	    $download_url = nsddsp_getDownFileUrlFromItemName($ipn_paypal_item_name,$expire,$post_id );
        $args['download_url']   = $download_url;

        nsddsp_debug_log('nsddsp_resend_email $download_url='.$download_url, true);

        //メール送信実行
        $return_send_mail = nsddsp_send_mail( $args );        
        if( $return_send_mail == true )
        {
            $ipn_status = __('Email retransmitted','ns_digital_data_seller_plugin').'('.$expire_y.'/'.$expire_m.'/'.$expire_d.' '.$expire_h.')';
            $post_meta['ipn_status'] = $ipn_status;            
            update_post_meta($post_id, NSDDSP_METABOX_ID_IPN_DATA, $post_meta);            
            
            nsddsp_debug_log(__('nsddsp_resend_email() Email retransmission completed $post_id= ','ns_digital_data_seller_plugin').$post_id, true);       
            $ret = true;
        }
        else 
        {            
            nsddsp_debug_log(__('Error: nsddsp_resend_email Failed to send mail.','ns_digital_data_seller_plugin'), false);
            $ret = false;
        }
    }
    return $ret;
}

//ログファイルのログを表示する
function nsddsp_view_logfile()
{
    $log_text = @file_get_contents(nsddsp_get_logfile_path());
    if($log_text){        
        wp_die(nl2br($log_text),__('View a log data','ns_digital_data_seller_plugin'));
    }
    else 
    {
        wp_die('Can\'t Open Log file.');
    }
}


//ログファイルのログをクリアする
function nsddsp_reset_logfile()
{
    $log_reset = true;
    $text = '['.date_i18n('Y/m/d g:i A').'] - SUCCESS : Log file reset';
    $text .= "\n------------------------------------------------------------------\n\n";
    $fp = fopen(nsddsp_get_logfile_path(), 'w');
    
    if($fp != FALSE) 
    {
        @fwrite($fp, $text);
        @fclose($fp);
    }
    else
    {
        $log_reset = false;
    }
    return $log_reset;
}

//使い方説明ページの描画
function nsddsp_draw_manual_page() 
{
?>
<div class="wrap">
<h2><?php echo __('How to use digital data seller plugin','ns_digital_data_seller_plugin'); ?></h2>
<ol style="list-style-type: decimal">
  <li><?php echo __('Add a button generated on Paypal\'s site to the product sales page.','ns_digital_data_seller_plugin'); ?></li>
  <li><?php echo __('Register the download sales file with "Product name" set for the button of Paypal','ns_digital_data_seller_plugin'); ?></li>
  <li><?php echo __('Set "[Wordpress URL]/?nsddsp_paypal_ipn=1" in Paypal\'s Instant Payment Notification(IPN) setting.','ns_digital_data_seller_plugin'); ?></li>
</ol>
<br /><?php echo __('---','ns_digital_data_seller_plugin'); ?>
</div>
<?php 
}
?>
<?php

//設定ページの描画
function nsddsp_draw_setting_page() 
{
?>
<div class="wrap">
<h2><?php echo __('Plugin settings','ns_digital_data_seller_plugin'); ?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'ns_digital_data_seller_plugin_settings' ); ?>
    <?php do_settings_sections( 'ns_digital_data_seller_plugin_settings' ); ?>
    <?php settings_errors(); ?>
    <table class="form-table">    
        <tr valign="top">
        <th scope="row"><?php echo __('Token generation string','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_TOKENWORD ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_TOKENWORD) ); ?>" />
        </BR><?php echo __('* Please set a character string of 6 characters or more.','ns_digital_data_seller_plugin'); ?>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Use Paypal\'s sandbox','ns_digital_data_seller_plugin'); ?></th>
        <td><input type="checkbox" name="<?php echo NSDDSP_OPTION_ID_USE_SANDBOX ?>" value="1" <?php checked( '1', get_option( NSDDSP_OPTION_ID_USE_SANDBOX ) ); ?>  /></td>        
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Automatically send email to purchaser when receiving Paypal IPN','ns_digital_data_seller_plugin'); ?></th>
        <td><input type="checkbox" name="<?php echo NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL ?>" value="1" <?php checked( '1', get_option( NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL ) ); ?>  /></td>        
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Seller\'s paypal registration email address','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL) ); ?>" />
        </BR><?php echo __('* In case of blank, do not check "receiver_email" match when receiving Paypal IPN.','ns_digital_data_seller_plugin'); ?>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Source Email address whitch sent to buyer.','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_SELLER_FROM_EMAIL ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_SELLER_FROM_EMAIL) ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Name of the sender of the email sent to buyer. ','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_SELLER_FROM_NAME ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_SELLER_FROM_NAME) ); ?>" />
        </BR><?php echo __('* If it is blank, "Email address of the sender" is set.','ns_digital_data_seller_plugin'); ?>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('BCC Email address to send to buyer.','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS) ); ?>" /></td>
        </tr>        
        <tr valign="top">
        <th scope="row"><?php echo __('Download expiration(hours)','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_EXPIRE_HOURS ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_EXPIRE_HOURS) ); ?>" />
        </BR><?php echo __('* "24" will be set for 1 day, "72" for 3 days.','ns_digital_data_seller_plugin'); ?>
        </td>
        </tr>        
        <tr valign="top">
        <th scope="row"><?php echo __('Is Copy Download file','ns_digital_data_seller_plugin'); ?></th>
        <td><input type="checkbox" name="<?php echo NSDDSP_OPTION_ID_IS_COPY_DOWNFILE ?>" value="1" <?php checked( '1', get_option( NSDDSP_OPTION_ID_IS_COPY_DOWNFILE ) ); ?>  />
        </td>
        </tr>           
        <tr valign="top">
        <th scope="row"><?php echo __('Title of Email to buyer','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT) ); ?>" /></td>
        </tr>     
        <tr valign="top">
        <th scope="row"><?php echo __('Body of the Email to buyer','ns_digital_data_seller_plugin'); ?></th>
            <td>
            <textarea name="<?php echo NSDDSP_OPTION_ID_PAYER_MAIL_BODY ?>" cols="120" rows="14"><?php echo esc_textarea(get_option(NSDDSP_OPTION_ID_PAYER_MAIL_BODY)); ?></textarea>
            <br /><p class="description"><?php echo __('Tag list converted by "subject of email" and "body of Email"','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{first_name}:Buyer\'s first name','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{last_name}:Buyer\'s last name','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{payer_email}:Buyer\'s email address','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{transaction_id}:Transaction ID','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{purchase_date}:date of purchase','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{download_url}:Download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{item_name}:Paypal registered product name','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_y}:Year of expiration of download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_m}:Month of expiration of download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_d}:Day of expiration of download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_h}:Time of expiration of download URL(24 hour display)','ns_digital_data_seller_plugin'); ?>
            </p>
            </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Destination email address which is sent to seller','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL) ); ?>" /></td>
        </tr>        
        <tr valign="top">
        <th scope="row"><?php echo __('Source email address which is sent to seller','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL) ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Title of Email to seller','ns_digital_data_seller_plugin'); ?></th>
        <td><input style="width:100%;" type="text" name="<?php echo NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT ?>" value="<?php echo esc_attr( get_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT) ); ?>" /></td>
        </tr>     
        <tr valign="top">
        <th scope="row"><?php echo __('Body of Email to seller','ns_digital_data_seller_plugin'); ?></th>
            <td>
            <textarea name="<?php echo NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY ?>" cols="120" rows="14"><?php echo esc_textarea(get_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY)); ?></textarea>
            <br /><p class="description"><?php echo __('Tag list converted by "subject of email" and "body of Email"','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{first_name}:Buyer\'s first name','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{last_name}:Buyer\'s last name','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{payer_email}:Buyer\'s email address','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{transaction_id}:Transaction ID','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{purchase_date}:date of purchase','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{download_url}:Download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{item_name}:Paypal registered product name','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_y}:Year of expiration of download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_m}:Month of expiration of download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_d}:Day of expiration of download URL','ns_digital_data_seller_plugin'); ?>
            <br /><?php echo __('{expire_h}:Time of expiration of download URL(24 hour display)','ns_digital_data_seller_plugin'); ?>
            </p>
            </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Processed transaction ID','ns_digital_data_seller_plugin'); ?></th>
        <td>
        <textarea name="<?php echo NSDDSP_OPTION_ID_COMPLETED_TXN_IDS ?>" cols="120" rows="14"><?php echo esc_textarea(get_option(NSDDSP_OPTION_ID_COMPLETED_TXN_IDS)); ?></textarea>        
        </BR><?php echo __('* Used to record the processed transaction ID and avoid multiple mail transmission etc. for the same transaction ID','ns_digital_data_seller_plugin'); ?>
        </td>
        </tr>
    </table>
    <br />
    <h2><?php echo __('log file','ns_digital_data_seller_plugin'); ?></h2>    
    <p><label><?php echo __('Record log file','ns_digital_data_seller_plugin'); ?>
    <input type="checkbox" name="<?php echo NSDDSP_OPTION_ID_RECORD_LOG ?>" value="1" <?php checked( '1', get_option( NSDDSP_OPTION_ID_RECORD_LOG ) ); ?>  />
    </label></p>    
    <p><input type="submit" name="nsddsp_view_logfile" class="button" style="font-weight:bold; color:red" value=<?php echo __('"Open log file"','ns_digital_data_seller_plugin'); ?>/></p>
    <p><input type="submit" name="nsddsp_reset_logfile" class="button" style="font-weight:bold; color:red" value=<?php echo __('"Reset log file"','ns_digital_data_seller_plugin'); ?>/>
    <label><input type="text" name="nsddsp_reset_logfile_password" value="" />
    <BR/><?php echo __('* To reset the log data, enter the password "ASDFQWE" in the text box, then click the button.','ns_digital_data_seller_plugin'); ?></label></p>
    
    <?php submit_button(); ?>
</form>
</div>
<?php 
}
?>
<?php

//トークン生成文字列の有効性チェック
function nsddsp_token_srcword_check( $srcword ) 
{
	if( mb_strlen($srcword) <= 6 )
	{
        add_settings_error(
            NSDDSP_OPTION_ID_TOKENWORD,
            'invalid_word',
            __('Invalid character string','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_TOKENWORD);
	}
	return $srcword;
}

//販売者Eメールアドレスの有効性チェック
function nsddsp_paypal_receiver_email_check( $email_address ) 
{
    if(mb_strlen($email_address) == 0 || nsddsp_email_check( $email_address ))
    {
        return $email_address;
    }
    else 
    {
        add_settings_error(
            NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL,
            'invalid_word',
            __('Invalid email address','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL);                
    }   
}

//購入者へのメールの送信元アドレスの有効性チェック
function nsddsp_seller_from_email_check( $email_address ) 
{
    if(mb_strlen($email_address) == 0 || nsddsp_email_check( $email_address ))
    {
        return $email_address;
    }
    else 
    {
        add_settings_error(
            NSDDSP_OPTION_ID_SELLER_FROM_EMAIL,
            'invalid_word',
            __('Invalid email address','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_SELLER_FROM_EMAIL);                
    }
}

//購入者へのメールのBCC送信アドレスの有効性チェック
function nsddsp_payer_mail_bcc_email_check( $email_address ) 
{
    if(mb_strlen($email_address) == 0 || nsddsp_email_check( $email_address ))
    {
        return $email_address;
    }
    else 
    {
        add_settings_error(
            NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS,
            'invalid_word',
            __('Invalid email address','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS);                
    }
}

//販売者へのメールの送信先アドレスの有効性チェック
function nsddsp_to_seller_to_email_check( $email_address ) 
{
    if( mb_strlen($email_address) == 0 || nsddsp_email_check( $email_address ))
    {
        return $email_address;
    }
    else 
    {
        add_settings_error(
            NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL,
            'invalid_word',
            __('Invalid email address','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL);                
    }
}

//販売者へのメールの送信元アドレスの有効性チェック
function nsddsp_to_seller_from_email_check( $email_address ) 
{
    if( mb_strlen($email_address) == 0 || nsddsp_email_check( $email_address ))
    {
        return $email_address;
    }
    else 
    {
        add_settings_error(
            NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL,
            'invalid_word',
            __('Invalid email address','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL);                
    }
}

//メールアドレスの有効性チェック（汎用的に使用する関数）
function nsddsp_email_check( $email_address )
{
    if(filter_var($email_address, FILTER_VALIDATE_EMAIL))
    {     
        return true;
    }
    else 
    {
        return false;
    }
}

//ダウンロードURLの期限の有効性チェック
function nsddsp_expire_hours_check( $expire_hours ) 
{    
	if( intval($expire_hours) <= 0 )
	{
        add_settings_error(
            NSDDSP_OPTION_ID_EXPIRE_HOURS,
            'invalid_days',
            __('Invalid expiration time','ns_digital_data_seller_plugin'),
            'error'
        );	
		return get_option(NSDDSP_OPTION_ID_EXPIRE_HOURS);
	}	
	return $expire_hours;
}

//設定画面の追加
function nsddsp_register_settings() 
{
	register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_TOKENWORD,'nsddsp_token_srcword_check' );	
	register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_USE_SANDBOX );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_EXPIRE_HOURS,'nnsddsp_expire_hours_check' );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_IS_COPY_DOWNFILE );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL,'nsddsp_paypal_receiver_email_check' );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_PAYER_MAIL_BODY);
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_SELLER_FROM_EMAIL,'nsddsp_seller_from_email_check' );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_SELLER_FROM_NAME);  
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS,'nsddsp_payer_mail_bcc_email_check' );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_COMPLETED_TXN_IDS );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_RECORD_LOG );

    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL,'nsddsp_to_seller_to_email_check' );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL,'nsddsp_to_seller_from_email_check' );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT );
    register_setting( 'ns_digital_data_seller_plugin_settings', NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY);
}
add_action( 'admin_init', 'nsddsp_register_settings' );

//ダミーページの描画
function nsddsp_draw_dummy_page() 
{
	if ( !current_user_can( 'manage_options' ) )  
    {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>'.__('Dummy page','ns_digital_data_seller_plugin').'</p>';
	echo '</div>';
}

//Wordpress管理者ページのメニュー追加
function nsddsp_add_admin_menu() 
{
	add_menu_page( __('Paypal Data sales','ns_digital_data_seller_plugin'), __('Paypal Data sales','ns_digital_data_seller_plugin'), 'manage_options', 'nsddsp_menu_download', 'nsddsp_draw_dummy_page' , '', 7);	
	add_submenu_page( 'nsddsp_menu_download', __('Setting','ns_digital_data_seller_plugin'), __('Setting','ns_digital_data_seller_plugin'), 'manage_options', 'nsddsp_submenu_setting', 'nsddsp_draw_setting_page' );
    add_submenu_page( 'nsddsp_menu_download', __('How to use','ns_digital_data_seller_plugin'), __('How to use','ns_digital_data_seller_plugin'), 'manage_options', 'nsddsp_submenu_manual', 'nsddsp_draw_manual_page' );
}
add_action( 'admin_menu', 'nsddsp_add_admin_menu', 28 );

//メタボックス描画「ダウンロードファイル」
function nsddsp_draw_downfile_info_form() 
{
    global $post;
    wp_nonce_field(wp_create_nonce(__FILE__), 'nsddsp_nonce');
?>
  <div id="nsddsp_post_downfile">
<?php
	$id = get_the_ID();
	$post_meta = get_post_meta($id, NSDDSP_METABOX_ID_DOWN_ID, true);	
    $paypal_item_name = '';
    $file_name = '';
    $down_cnt  = '';
    if ($post_meta) 
    {
        $paypal_item_name = $post_meta['paypal_item_name'];
        $file_name = $post_meta['file_name'];
        $down_cnt  = $post_meta['down_cnt'];
    }
    if(empty($down_cnt))$down_cnt = '0';

    echo '<p><label>'.__('Product name of Paypal button (Item Name)','ns_digital_data_seller_plugin').'<br />';
    echo '<input type="text" name="paypal_item_name" value="'.esc_html($paypal_item_name).'" style="width:80%" />';
    echo '</label></p>';
    echo '<p><label>'.__('Download file name (if it is blank, the original file name is set.)','ns_digital_data_seller_plugin').'<br />';
    echo '<input type="text" name="file_name" value="'.esc_html($file_name).'" style="width:80%" />';
    echo '</label></p>';
    echo '<p><label>'.__('Number of downloads','ns_digital_data_seller_plugin').'<br />';
    echo '<input type="text" name="down_cnt" value="'.esc_html($down_cnt).'" style="width:80%" />';
    echo '</label></p>';

	$post_meta = get_post_meta($id, NSDDSP_METABOX_ID_DOWNFILE, true);	
    
    if ($post_meta) 
    {
	    echo '<p><label>'.__('File Path','ns_digital_data_seller_plugin').'<br />';
        echo esc_html($post_meta['url']).'<br /></p>';
        echo '<p><label>'.__('File URL (Current time URL)','ns_digital_data_seller_plugin').'<br />';
		$expire = date_i18n("ymdH",strtotime('+'.get_option(NSDDSP_OPTION_ID_EXPIRE_HOURS).' hours', current_time('timestamp')));
		$down_url = nsddsp_getDownFileUrlFromItemName($paypal_item_name,$expire,'0');
		echo '<p><a href="'.$down_url.'" target="_blank">'.$down_url.'</a></p>';
	}
    else 
    {
	    echo '<p><label>'.__('File Path','ns_digital_data_seller_plugin').'<br />';
        echo __('no settings','ns_digital_data_seller_plugin').'<br /></p>';
		echo '<p><label>'.__('File URL (Current time URL)','ns_digital_data_seller_plugin').'<br />';
        echo __('no settings','ns_digital_data_seller_plugin').'<br /></p></p>';
    }

	$name = NSDDSP_METABOX_ID_DOWNFILE;
	echo '
</label><p><label>'.__('[To update the file, select it below and press update button]','ns_digital_data_seller_plugin').'<br /><input type="file" name="'.$name.'" id="'.$name.'" value="" /></label>
';
	if ($post_meta) 
    {
		echo '
<label><input type="checkbox" name="'.$name.'_delete" id="'.$name.'_delete" value="1" />'.__('Delete file','ns_digital_data_seller_plugin').'</label>
';
	}
?>
  </div>
<?php
}

//メタボックス追加「ダウンロードファイル」
function nsddsp_add_downfile_meta_box() 
{
  add_meta_box('nsddsp_post_downfile_meta_box', __('Product data file','ns_digital_data_seller_plugin'), 'nsddsp_draw_downfile_info_form', NSDDSP_POST_ID_DOWNFILE, 'normal', 'high');
}
add_action('add_meta_boxes', 'nsddsp_add_downfile_meta_box');

// ファイルアップロードに必要
function nsddsp_metabox_edit_form_tag() 
{
	echo ' enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'nsddsp_metabox_edit_form_tag');

//保存処理 
function nsddsp_add_enable_mime_type($mimes) 
{
    //本来あるべきMIME Typeなど考えずに適当に書いてもあまり問題ない    
    $mimes['dat'] = 'application/octet-stream';
    return $mimes;
}
add_filter('upload_mimes', 'nsddsp_add_enable_mime_type');

function nsddsp_downfile_box_save($post_id) 
{
    global $post;
    $nsddsp_nonce = isset($_POST['nsddsp_nonce']) ? $_POST['nsddsp_nonce'] : null;
    if(!wp_verify_nonce($nsddsp_nonce, wp_create_nonce(__FILE__))) 
    {  
        return $post_id;
    }
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
    if(!current_user_can('edit_post', $post->ID)) { return $post_id; }
    // 権限チェック
    if (isset($_POST['post_type']) && NSDDSP_POST_ID_DOWNFILE == $_POST['post_type']) 
    {
        if (!current_user_can('edit_page', $post_id)) 
        {
            return;
        }
    } 
    else 
    {  
        if (!current_user_can('edit_post', $post_id)) 
        {
          return;
        }
    }
  
    //ダウンロードファイル設定の保存
    $paypal_item_name = $_POST['paypal_item_name'];
    $file_name        = $_POST['file_name'];    
    $down_cnt         = $_POST['down_cnt'];    

    $post_meta        = array('paypal_item_name' => $paypal_item_name,
                              'file_name'        => $file_name,
                              'down_cnt'         => $down_cnt
    );
    update_post_meta($post_id, NSDDSP_METABOX_ID_DOWN_ID, $post_meta);
    
    //ファイルの保存および旧ファイルの削除
    // ファイル削除を行うかチェック
    $post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_DOWNFILE, true);
    
    if ($post_meta && $_POST[NSDDSP_METABOX_ID_DOWNFILE.'_delete']) 
    {
        @unlink($post_meta['file']);
        delete_post_meta($post_id, NSDDSP_METABOX_ID_DOWNFILE);
        return;// 終了。アップロードは行わない。
    }
    
    //アップロードする
    // アップロードされたファイルのチェック
    if(empty($_FILES[NSDDSP_METABOX_ID_DOWNFILE]['name'])) 
    {
        // ファイルなし
        return;
    }  	
    $file = $_FILES[NSDDSP_METABOX_ID_DOWNFILE];
  
    // ファイル保存
    // 旧ファイルがあれば削除
    if ($post_meta) 
    {
        @unlink($post_meta['file']);
    }
  
    $overrides = array('test_form' => false);

    global $nsddsp_gloval_sub_dir;
    
    // アップロードディレクトリを一時変更する
    $nsddsp_gloval_sub_dir = strval($post_id);
    add_filter( 'upload_dir', 'nsddsp_upload_dir');
    $upload = wp_handle_upload($file, $overrides);
    // アップロードディレクトリをもとに戻す
    remove_filter( 'upload_dir', 'nsddsp_upload_dir' );
    
    if(isset($upload['error']) && $upload['error']) 
    {
      wp_die('Upload error : '.$upload['error']);
    }
    
    $upload = wp_slash($upload);
    
    $post_meta_url = str_replace( site_url(), '', $upload['url'] );

    $post_meta = array('file' => $upload['file'],
                       'url'  => $post_meta_url,
                       'type' => $upload['type']
    );
    update_post_meta($post_id, NSDDSP_METABOX_ID_DOWNFILE, $post_meta);
}
add_action('save_post', 'nsddsp_downfile_box_save');

//記事がゴミ箱から削除される時の処理
function nsddsp_downfile_metabox_before_delete_post($post_id)
{
    global $post_type;
    if ($post_type != NSDDSP_POST_ID_DOWNFILE)
		return;

	// ファイルの削除
	$post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_DOWNFILE, true);
	if ($post_meta) 
    {		
		@unlink($post_meta['file']);
	}
	// post_meta情報自体はシステムの方で削除される
}
add_action('before_delete_post', 'nsddsp_downfile_metabox_before_delete_post');

//IPNメタデータ追加
function nsddsp_ipndata_info_form() 
{ 
    global $post;
    wp_nonce_field(wp_create_nonce(__FILE__), 'nsddsp_nonce');

	$id = get_the_ID();
	$post_meta = get_post_meta($id, NSDDSP_METABOX_ID_IPN_DATA, true);	
    $ipn_email = '';
    $ipn_paypal_item_name = '';    
    $ipn_first_name = '';    
    $ipn_last_name = '';
    $ipn_txn_id  = '';
    $ipn_payment_status  = '';
    $ipn_purchase_date = '';
    $ipn_filename = '';
    $ipn_down_cnt = '';
    if ($post_meta) 
    {
        $ipn_email            = $post_meta['ipn_email'];
        $ipn_paypal_item_name = $post_meta['ipn_paypal_item_name'];
        $ipn_first_name       = $post_meta['ipn_first_name'];
        $ipn_last_name        = $post_meta['ipn_last_name'];
        $ipn_txn_id           = $post_meta['ipn_txn_id'];
        $ipn_payment_status   = $post_meta['ipn_payment_status'];
        $ipn_purchase_date    = $post_meta['ipn_purchase_date'];
        $ipn_status           = $post_meta['ipn_status'];
        $ipn_filename         = $post_meta['ipn_filename'];
        $ipn_down_cnt         = $post_meta['ipn_down_cnt'];
    }
    if(empty($ipn_down_cnt))$ipn_down_cnt = '0';
?>
  <div id="nsddsp_post_ipndata">
  <p><?php echo __('IPN data information','ns_digital_data_seller_plugin'); ?></p>
  <p><label><?php echo __('Email address','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_email" value="<?php echo esc_html($ipn_email); ?>"  style="width:80%" />
    </label></p> 
  <p><label><?php echo __('First Name   -   Last Name','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_first_name" value="<?php echo esc_html($ipn_first_name); ?>"  style="width:40%" />
    <input type="text" name="ipn_last_name" value="<?php echo esc_html($ipn_last_name); ?>"  style="width:40%" />
    </label></p>
  <p><label><?php echo __('Product name of Paypal button','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_paypal_item_name" value="<?php echo esc_html($ipn_paypal_item_name); ?>"  style="width:80%" />
    </label></p>  
  <p><label><?php echo __('Paypal transaction ID','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_txn_id" value="<?php echo esc_html($ipn_txn_id); ?>"  style="width:80%" />
    </label></p>
  <p><label><?php echo __('Purchase date','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_purchase_date" value="<?php echo esc_html($ipn_purchase_date); ?>"  style="width:80%" />
    </label></p>    
  <p><label><?php echo __('Payment status','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_payment_status" value="<?php echo esc_html($ipn_payment_status); ?>"  style="width:80%" />
    </label></p>
  <p><label><?php echo __('Email transmission status','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_status" value="<?php echo esc_html($ipn_status); ?>"  style="width:80%" />
    </label></p>
  <p><label><?php echo __('Download file name','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_filename" value="<?php echo esc_html($ipn_filename); ?>"  style="width:80%" />
    </label></p>  
<?php
    {
        echo '<p><label>'.__('Reissue File URL (Current time URL)','ns_digital_data_seller_plugin').'<br />';
		$expire = date_i18n("ymdH",strtotime('+'.get_option(NSDDSP_OPTION_ID_EXPIRE_HOURS).' hours', current_time('timestamp')));
        $down_url = nsddsp_getDownFileUrlFromItemName($ipn_paypal_item_name,$expire,$id);
        if(empty($down_url))
        {
            echo '<p>Not Found File.</p>';
        }
        else
        {
            echo '<p><a href="'.$down_url.'" target="_blank">'.$down_url.'</a></p>';
        }
		
	}
?>
  <p><label><?php echo __('Number of downloads','ns_digital_data_seller_plugin'); ?><br />
    <input type="text" name="ipn_down_cnt" value="<?php echo esc_html($ipn_down_cnt); ?>"  style="width:80%" />
    </label></p>    
  <p><label><?php echo __('【Retransmission of Email】 * If you enter password and execute "Update" it will be retransmitted.','ns_digital_data_seller_plugin'); ?><br />
  <label><input type="text" name="resend_mail_password" id="resend_mail_password" /><?php echo __(':Please enter the password "ASDFQWE"','ns_digital_data_seller_plugin'); ?></label><br />
  <label><input type="checkbox" name="resend_mail_check" id="resend_mail_check" value="1" /><?php echo __('Resend email','ns_digital_data_seller_plugin'); ?></label>
  </div>
<?php
}

//Paypal IPNデータメタデータ追加
function nsddsp_add_ipndata_meta_box() 
{
    add_meta_box('nsddsp_post_ipndata_meta_box', 'IPNデータ', 'nsddsp_ipndata_info_form', NSDDSP_POST_ID_IPNDATA, 'normal', 'high');
}
add_action('add_meta_boxes', 'nsddsp_add_ipndata_meta_box');

//Paypal IPNデータメタデータ保存処理 
function nsddsp_ipndata_box_save($post_id) 
{
    global $post;
    $nsddsp_nonce = isset($_POST['nsddsp_nonce']) ? $_POST['nsddsp_nonce'] : null;
    if(!wp_verify_nonce($nsddsp_nonce, wp_create_nonce(__FILE__))) 
    {  
        return $post_id;
    }
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
    if(!current_user_can('edit_post', $post->ID)) { return $post_id; }
    if($_POST['post_type'] == NSDDSP_POST_ID_IPNDATA)
    {
        $ipn_email            = $_POST['ipn_email'];
        $ipn_paypal_item_name = $_POST['ipn_paypal_item_name'];
        $ipn_first_name       = $_POST['ipn_first_name'];
        $ipn_last_name        = $_POST['ipn_last_name'];
        $ipn_txn_id           = $_POST['ipn_txn_id'];
        $ipn_payment_status   = $_POST['ipn_payment_status'];
        $ipn_purchase_date    = $_POST['ipn_purchase_date'];     
        $ipn_status           = $_POST['ipn_status'];
        $ipn_filename         = $_POST['ipn_filename'];
        $ipn_down_cnt         = $_POST['ipn_down_cnt'];

        $post_meta = array('ipn_email'             => $ipn_email,
                           'ipn_paypal_item_name'  => $ipn_paypal_item_name,
                           'ipn_first_name'        => $ipn_first_name,
                           'ipn_last_name'         => $ipn_last_name,
                           'ipn_txn_id'            => $ipn_txn_id,
                           'ipn_purchase_date'     => $ipn_purchase_date,
                           'ipn_payment_status'    => $ipn_payment_status,
                           'ipn_status'            => $ipn_status,
                           'ipn_filename'          => $ipn_filename,
                           'ipn_down_cnt'          => $ipn_down_cnt
        );
        update_post_meta($post_id, NSDDSP_METABOX_ID_IPN_DATA, $post_meta);  

        //メールの再送処理
        if ($_POST[resend_mail_password] == NSDDSP_HANYO_PASSWORD) 
        {
           //nsddsp_debug_log('$_POST[resend_mail_password]='.$_POST[resend_mail_password], true);
           //nsddsp_debug_log('$_POST[resend_mail_check]='.$_POST[resend_mail_check], true);
           if ($_POST[resend_mail_check] )
           {   
              nsddsp_debug_log('resend_mail $post_id='.$post_id, true);
              nsddsp_resend_email( $post_id);
           }
        }
    }
}
add_action('save_post', 'nsddsp_ipndata_box_save');

//プラグイン用のフォルダおよびアクセス制限ファイルの作成
function nsddsp_create_required_files() 
{    
    //$upload_dir = wp_upload_dir();
    $nsddsp_upload_dir = nsddsp_get_upload_dir();
    $files = array(
        array(
            //'base' => $upload_dir['basedir'] . '/'.NSDDSP_UPLOAD_DIR,
            'base' => $nsddsp_upload_dir,
            'file' => '.htaccess',
            'content' => 'deny from all'
        ),
        array(
            //'base' => $upload_dir['basedir'] . '/'.NSDDSP_UPLOAD_DIR,
            'base' => $nsddsp_upload_dir,
            'file' => 'index.html',
            'content' => ''
        )
    );

    foreach ($files as $file) 
    {
        if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) 
        {
            if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) 
            {
                fwrite($file_handle, $file['content']);
                fclose($file_handle);
            }
        }
    }
}
add_action('init', 'nsddsp_create_required_files', 10);

//「ダウンロードファイル」のカスタム投稿一覧におけるカラムの設定
function nsddsp_post_id_downfile_columns($columns) 
{
    $columns['nsddsp_column_downfile_down_cnt'] = __('Number of downloads','ns_digital_data_seller_plugin');
    return $columns;
}

//「ダウンロードファイル」のカラムの表示内容を定義
//追加したカラムに「metaname」というカスタムフィールドの内容を表示させる
function nsddsp_add_post_id_downfile_column($column_name, $post_id) 
{
    if( $column_name == 'nsddsp_column_downfile_down_cnt' ) 
    {
        $post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_DOWN_ID, true);
        if ( isset($post_meta) && $post_meta ) 
        {
            $down_cnt = $post_meta['down_cnt'];
            echo attribute_escape($down_cnt);
        } 
        else 
        {
            echo __('None');
        }
    }
}
add_filter( 'manage_edit-'.NSDDSP_POST_ID_DOWNFILE.'_columns', 'nsddsp_post_id_downfile_columns' );
add_action( 'manage_posts_custom_column', 'nsddsp_add_post_id_downfile_column', 10, 2 );

//「IPNデータ」のカラムの表示内容を定義
function nsddsp_post_id_ipndata_columns($columns) 
{
    $columns['nsddsp_column_ipndata_status'] = __('Email transmission status','ns_digital_data_seller_plugin');
    $columns['nsddsp_column_ipndata_payment_status'] = __('Payment status','ns_digital_data_seller_plugin');
    $columns['nsddsp_column_ipndata_downcnt_status'] = __('Number of downloads','ns_digital_data_seller_plugin');
    return $columns;
}

//「IPNデータ」のカラムの表示内容を定義
//追加したカラムに「metaname」というカスタムフィールドの内容を表示させる
function nsddsp_add_post_id_ipndata_column($column_name, $post_id) 
{
    if( $column_name == 'nsddsp_column_ipndata_status' ) 
    {
        $post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_IPN_DATA, true);
        if ( isset($post_meta) && $post_meta ) 
        {
            $status = $post_meta['ipn_status'];
            echo attribute_escape($status);
        } 
        else 
        {
            echo __('None');
        }
    }
    if( $column_name == 'nsddsp_column_ipndata_payment_status' ) 
    {
        $post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_IPN_DATA, true);
        if ( isset($post_meta) && $post_meta ) 
        {
            $payment_status = $post_meta['ipn_payment_status'];
            echo attribute_escape($payment_status);
        } 
        else
        {
            echo __('None');
        }
    }
    if( $column_name == 'nsddsp_column_ipndata_downcnt_status' ) 
    {
        $post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_IPN_DATA, true);
        if ( isset($post_meta) && $post_meta ) 
        {
            $payment_status = $post_meta['ipn_down_cnt'];
            echo attribute_escape($payment_status);
        } 
        else
        {
            echo __('None');
        }
    }
    
}
add_filter( 'manage_edit-'.NSDDSP_POST_ID_IPNDATA.'_columns', 'nsddsp_post_id_ipndata_columns' );
add_action( 'manage_posts_custom_column', 'nsddsp_add_post_id_ipndata_column', 10, 2 );
