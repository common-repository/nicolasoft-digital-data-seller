<?php

//文字列定義
//メタボックスID
define('NSDDSP_METABOX_ID_DOWN_ID',  'nsddsp_metabox_id_down_id');
define('NSDDSP_METABOX_ID_DOWNFILE',  'nsddsp_metabox_id_downfile');
define('NSDDSP_METABOX_ID_IPN_DATA',  'nsddsp_metabox_id_ipndata');

//オプションID
define('NSDDSP_OPTION_ID_TOKENWORD',  'nsddsp_option_token_srcword');//ダウンロードURL生成時のトークン作成暗号
define('NSDDSP_OPTION_ID_USE_SANDBOX',  'nsddsp_option_use_paypal_sandbox');//Paypalサンドボックスの使用の設定
define('NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL',  'nsddsp_option_is_auto_send_payer_mail');//購入者へのメールの自動送信の設定

//オプションID（送信メール関係)
define('NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL',  'nsddsp_option_paypal_receiver_email');//Paypal登録販売者メールアドレス
define('NSDDSP_OPTION_ID_SELLER_FROM_EMAIL',  'nsddsp_ption_seller_from_email');//購入者へのメールの送信元メールアドレス
define('NSDDSP_OPTION_ID_SELLER_FROM_NAME',  'nsddsp_option_seller_from_name');//購入者へのメールの送信元の名前

define('NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS',  'nsddsp_option_to_payer_bcc_email');//購入者へのメールのBCCメールアドレス
define('NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT',  'nsddsp_option_to_payer_mail_subject');//購入者送信メールの題名
define('NSDDSP_OPTION_ID_PAYER_MAIL_BODY',  'nsddsp_option_to_payer_mail_body');//購入者送信メールの本文ソース

define('NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL',  'nsddsp_ption_to_seller_to_email');//販売者へのメールの送信先メールアドレス
define('NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL',  'nsddsp_ption_to_seller_from_email');//販売者へのメールの送信元メールアドレス
define('NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT',  'nsddsp_option_to_seller_mail_subject');//販売者送信メールの題名
define('NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY',  'nsddsp_option_to_seller_mail_body');//販売者送信メールの本文ソース


define('NSDDSP_OPTION_ID_EXPIRE_HOURS',  'nsddsp_option_expire_hours');//ダウンロードURLの期限日数

define('NSDDSP_OPTION_ID_COMPLETED_TXN_IDS',  'nsddsp_option_completed_txn_ids');//処理済みのトランザクションID

define('NSDDSP_OPTION_ID_RECORD_LOG',  'nsddsp_option_record_log');//ログを記録するかの設定
define('NSDDSP_OPTION_ID_IS_COPY_DOWNFILE',  'nsddsp_option_is_copy_downfile');//注文(IPN)毎に販売ファイルを複製コピーするかの設定

//カスタム投稿タイプID
define('NSDDSP_POST_ID_DOWNFILE',  'nsddsp_post_downfile');
define('NSDDSP_POST_ID_IPNDATA',  'nsddsp_post_ipndata');

//その他
//アップロードディレクトリ名
define('NSDDSP_UPLOAD_DIR', 'ns_digital_data_seller_plugin_dir');

//トークン生成文字列
//define('NSDDSP_INIT_TOKEN_PASSWORD',  'fajojaoj490JGGDS9grere0gja0d9fa8fafkb-a*/-lkgSDS8gfGD(89gs');//適当な文字列

//PaypalポストバックURL
//define('NSDDSP_PAYPAL_URL',         'https://www.paypal.com/cgi-bin/webscr');         //Paypal IPNポストバックURL
//define('NSDDSP_PAYPAL_SANDBOX_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr'); //Paypal Sandbox IPNポストバックURL
define('NSDDSP_PAYPAL_URL',         'https://ipnpb.paypal.com/cgi-bin/webscr');         //Paypal IPNポストバックURL
define('NSDDSP_PAYPAL_SANDBOX_URL', 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'); //Paypal Sandbox IPNポストバックURL


define('NSDDSP_HANYO_PASSWORD',  'ASDFQWE');
