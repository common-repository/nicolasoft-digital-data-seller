<?php

//トークン生成文字列生成
function nsddsp_getTokenSrcWord() 
{
	$settings = get_option( NSDDSP_OPTION_ID_TOKENWORD );
	return $settings;
}

//ダウンロードファイルのURLを取得する
function nsddsp_getDownFileUrl($post_id,$expire,$ipn_post_id)
{
	//トークン生成文字列を取得
	$down_url_token = nsddsp_getTokenSrcWord();    
    //$token = hash('sha256', $down_url_token.'/'.strval($post_id).'/'.strval($expire));//171124以前
    $token = hash('sha256', $down_url_token.'/'.strval($post_id).'/'.strval($expire).'/'.strval($ipn_post_id));    
    $down_url = home_url('','http').'/?'.'nsddsp=1&download_id='.strval($post_id).'&expire='.strval($expire).'&ipn='.strval($ipn_post_id).'&ncl_token='.$token;
    return $down_url;
}

//PaypalからのItem NameからダウンロードファイルのURLを求める
function nsddsp_getDownFileUrlFromItemName($item_name,$expire,$ipn_post_id) 
{    
    $down_url = '';
    $down_id_list = get_posts( array( 'post_type' => NSDDSP_POST_ID_DOWNFILE, 'post_status' => 'any', 'numberposts' => -1 ) );
    
	if ( $down_id_list ) 
    {
		foreach ( $down_id_list as $down_data ) 
        {            
	        $post_meta = get_post_meta($down_data->ID, NSDDSP_METABOX_ID_DOWN_ID, true);	    
            if ($post_meta) 
            {                
                $paypal_item_name = $post_meta['paypal_item_name'];                
                if( $paypal_item_name == $item_name )
                {
                    $down_url = nsddsp_getDownFileUrl($down_data->ID,$expire,$ipn_post_id);                    
                    break;
                }                                
            } 
            else 
            {
                nsddsp_debug_log('Error:nsddsp_getDownFileUrlFromItemName() get_post_meta()',false);
            }            
		}

        if( empty($down_url) )
        {
            nsddsp_debug_log(__('Error:nsddsp_getDownFileUrlFromItemName() "Item name" is not found $item_name= ','ns_digital_data_seller_plugin').$item_name,false);
        }
	}
    else 
    {
        nsddsp_debug_log(__('Error:nsddsp_getDownFileUrlFromItemName() Post data not found','ns_digital_data_seller_plugin'),false);
    }
    return $down_url;
}

//アップロードディレクトリのカスタマイズ
$nsddsp_gloval_sub_dir = '';
function nsddsp_upload_dir( $dir ) 
{
    global $nsddsp_gloval_sub_dir;
    
    $sepa = DIRECTORY_SEPARATOR;
    return array(
        'path'   => $dir['basedir'] .$sepa.NSDDSP_UPLOAD_DIR.$sepa.$nsddsp_gloval_sub_dir,
        'url'    => $dir['baseurl'] . '/'.NSDDSP_UPLOAD_DIR. '/'.$nsddsp_gloval_sub_dir,
        'subdir' => $sepa.NSDDSP_UPLOAD_DIR.$sepa.$nsddsp_gloval_sub_dir,
    ) + $dir;
}

//IPNデータからダウンロードファイルローカルURLを取得
function nsddsp_getDownFileLocalPathforIPNData($ipn_post_id) 
{
    $down_path = '';
    $new_filename = ''; 

    //ファイル名取得
    $post_meta_ipn = get_post_meta($ipn_post_id, NSDDSP_METABOX_ID_IPN_DATA, true);
    if ($post_meta_ipn)
    {
        if(isset($post_meta_ipn['ipn_filename']) == true)
        {        
            $new_filename = $post_meta_ipn['ipn_filename'];
        }
    }
    // else 
    // {        
    //     $new_filename = '______nsddsp_NOFILE______.txt';
    // }

    //ファイル名が取得できた時のみパスを設定する
    if( !empty($new_filename) )
    {
        $down_path = nsddsp_get_upload_dir().'/'.$ipn_post_id.'/'.$new_filename;
    }
    return $down_path;    
}

//ダウンロードファイルローカルURLの取得
function nsddsp_getDownFileLocalUrl($post_id) 
{
	$post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_DOWNFILE, true);	
    $down_url = '';
    if ($post_meta)
    {
        $down_url = $post_meta['url'];
    }
    return $down_url;
}
    
//ダウンロードファイルのファイル名を取得（設定が無い場合は空文字を返す）
function nsddsp_getDownFileName($post_id) 
{
	$post_meta = get_post_meta($post_id, NSDDSP_METABOX_ID_DOWN_ID, true);	
    $down_filename = '';
    if ($post_meta) 
    {
        $down_filename = $post_meta['file_name'];
    }
    return $down_filename;
}    

//指定されたダウンロードファイルのダウンロード回数をカウントアップする
function nsddsp_countupDownFile($post_id, $ipn_post_id ) 
{
    //ダウンロードファイルデータのカウントアップ
    $post_meta_donwid = get_post_meta($post_id, NSDDSP_METABOX_ID_DOWN_ID, true);
    if ($post_meta_donwid)
    {
        $down_cnt = $post_meta_donwid['down_cnt'];
        $cnt = intval($down_cnt);
        $new_cnt = '1';
        if( isset($cnt) )
        {
            $new_cnt = strval( $cnt+1 );
        }
        $post_meta_donwid['down_cnt'] = $new_cnt;
        update_post_meta($post_id, NSDDSP_METABOX_ID_DOWN_ID, $post_meta_donwid);
    }

    //IPNデータのカウントアップ
    $post_meta_ipn = get_post_meta($ipn_post_id, NSDDSP_METABOX_ID_IPN_DATA, true);
    if ($post_meta_ipn)
    {
        $ipn_down_cnt = $post_meta_ipn['ipn_down_cnt'];
        $cnt = intval($ipn_down_cnt);
        $new_cnt = '1';
        if( isset($cnt) )
        {
            $new_cnt = strval( $cnt+1 );
        }
        $post_meta_ipn['ipn_down_cnt'] = $new_cnt;
        update_post_meta($ipn_post_id, NSDDSP_METABOX_ID_IPN_DATA, $post_meta_ipn);
    }
}

//旧バージョン(1.0.7以前)のトークンの要求されたダウンロードファイルのURLが有効かチェックする
function nsddsp_enableDownFileUrl_oldversion($down_post_id,$expire,$token) 
{
	$down_url_token = nsddsp_getTokenSrcWord();
	    
    //期限が有効かチェックする
    $i_cur_date_ymd = intval(date_i18n('ymdH'));
    if( intval($expire) < $i_cur_date_ymd )
    {
        return false;
    }

    //与えられたトークンが有効なものかチェックする
    $check_token = hash('sha256', $down_url_token.'/'.strval($down_post_id).'/'.strval($expire));        
    $isEnableTolen = ($token==$check_token);
    if( $isEnableTolen == false )
    {
        return false;
    }
    //すべてのチェックに有効と判断された場合のみtrueを返す
    return true;
}

//要求されたダウンロードファイルのURLが有効かチェックする
function nsddsp_enableDownFileUrl($down_post_id,$expire,$ipn_post_id,$token) 
{
	$down_url_token = nsddsp_getTokenSrcWord();
	    
    //期限が有効かチェックする
    $i_cur_date_ymd = intval(date_i18n('ymdH'));
    if( intval($expire) < $i_cur_date_ymd )
    {
        return false;
    }

    //与えられたトークンが有効なものかチェックする
    //$check_token = hash('sha256', $down_url_token.'/'.strval($down_post_id).'/'.strval($expire));    
    $check_token = hash('sha256', $down_url_token.'/'.strval($down_post_id).'/'.strval($expire).'/'.strval($ipn_post_id));    
    $isEnableTolen = ($token==$check_token);
    if( $isEnableTolen == false )
    {
        return false;
    }
    //すべてのチェックに有効と判断された場合のみtrueを返す
    return true;
}

// ダウンロードファイル登録のためのカスタム投稿タイプを作成する
function nsddsp_setup_post_types() 
{
	//カスタム投稿「ダウンロードファイル」の設定
	$download_args = array(
        'labels' => array(
            'name' => __('File','ns_digital_data_seller_plugin'),
            'singular_name' => __('File','ns_digital_data_seller_plugin'),
            'all_items' => __('File list','ns_digital_data_seller_plugin'),
            'add_new' => __('Add file','ns_digital_data_seller_plugin'),
            'add_new_item' => __('Add files','ns_digital_data_seller_plugin'),
            'edit_item' => __('Edit file','ns_digital_data_seller_plugin'),
            'new_item' => __('Add file','ns_digital_data_seller_plugin'),
            'view_item' => __('File display','ns_digital_data_seller_plugin'),
            'search_items' => __('File search','ns_digital_data_seller_plugin'),
            'not_found' =>  __('File not found','ns_digital_data_seller_plugin'),
            'not_found_in_trash' => __('File was not found in the trash can.','ns_digital_data_seller_plugin'), 
            'parent_item_colon' => ''
        ),
      'public' => false,
      'publicly_queryable' => false,
      'show_ui' => true,
      'show_in_menu' => 'nsddsp_menu_download',
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => false,
      'menu_position' => 5,
      //'supports' => array('title', 'editor', 'publicize', 'excerpt', 'custom-fields', 'thumbnail', 'tags', 'comments','author')
      'supports' => array('title')
    );
	
	register_post_type( NSDDSP_POST_ID_DOWNFILE, $download_args );

	//カスタム投稿「IPNデータ」の設定
	$ipndata_args = array(
        'labels' => array(
            'name' => __('IPN data','ns_digital_data_seller_plugin'),
            'singular_name' => __('IPN data','ns_digital_data_seller_plugin'),
            'all_items' => __('IPN data list','ns_digital_data_seller_plugin'),
            'add_new' => __('Add IPN data','ns_digital_data_seller_plugin'),
            'add_new_item' => __('Add IPN data','ns_digital_data_seller_plugin'),
            'edit_item' => __('Edit IPN data','ns_digital_data_seller_plugin'),
            'new_item' => __('Add IPN data','ns_digital_data_seller_plugin'),
            'view_item' => __('IPN data display','ns_digital_data_seller_plugin'),
            'search_items' => __('IPN data search','ns_digital_data_seller_plugin'),
            'not_found' =>  __('IPN data not found','ns_digital_data_seller_plugin'),
            'not_found_in_trash' => __('IPN data was not found in the trash can.','ns_digital_data_seller_plugin'), 
            'parent_item_colon' => ''
        ),
      'public' => false,
      'publicly_queryable' => false,
      'show_ui' => true,
      'show_in_menu' => 'nsddsp_menu_download',
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => false,
      'menu_position' => 6,
      //'supports' => array('title', 'editor', 'publicize', 'excerpt', 'custom-fields', 'thumbnail', 'tags', 'comments','author')
      'supports' => array('title', 'editor')
    );
	
	register_post_type( NSDDSP_POST_ID_IPNDATA, $ipndata_args );
}
add_action( 'init', 'nsddsp_setup_post_types', 1 );
