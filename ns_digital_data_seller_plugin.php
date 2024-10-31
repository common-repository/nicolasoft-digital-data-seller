<?php
/*
Plugin Name: NICOLASOFT Digital Data Seller
Plugin URI: http://nicolasoft.net
Description: A plug-in that has the function of receiving an IPN from Paypal and sending the download link URL of the product file by e-mail when the customer completes the payment from the Paypal button placed on the product sales page
Version: 1.0.11
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9KTRF46DS49P4
License: GPLv3 or later
Text Domain: ns_digital_data_seller_plugin
Domain Path: /languages
Author: nicolasoft
Author URI: https://profiles.wordpress.org/nicolasoft
*/

// プラグインフォルダーパス Plugin Folder Path.
if ( ! defined( 'NSDDSP_DIR' ) ) 
{
	define( 'NSDDSP_DIR', plugin_dir_path( __FILE__ ) );
}

//プラグイン関連プログラムのインクルード Include
require_once NSDDSP_DIR. 'includes/ns_digital_data_seller_plugin_define.php';
require_once NSDDSP_DIR. 'includes/ns_digital_data_seller_plugin_post_type.php';
if ( is_admin() )
{
	require_once NSDDSP_DIR. 'includes/ns_digital_data_seller_plugin_menu.php';
}

//ログファイル名
define( 'NSDDSP_LOGFILE_NAME', 'LOG_ns_digital_data_seller_plugin.txt' );

//プラグイン用アップロードファイルパスを取得
function nsddsp_get_upload_dir() 
{
    $upload_dir = wp_upload_dir();
    return $upload_dir['basedir'] . '/'.NSDDSP_UPLOAD_DIR;
}

//ログファイルパス取得
function nsddsp_get_logfile_path() 
{
    $log_file_path = nsddsp_get_upload_dir().'/'.NSDDSP_LOGFILE_NAME;
    return $log_file_path;
}

//デバッグログ書き込み
function nsddsp_debug_log($message,$success,$end=false)
{
	$is_record_log = get_option(NSDDSP_OPTION_ID_RECORD_LOG);
	if($is_record_log)
	{
		// Timestamp
		$text = '['.date_i18n('Y/m/d g:i A').'] - '.(($success)?'SUCCESS :':'FAILURE :').$message. "\n";
		if ($end) 
		{
			$text .= "\n------------------------------------------------------------------\n\n";
		}

		// Write to log
		$fp=fopen(nsddsp_get_logfile_path(),'a');
		fwrite($fp, $text );
		fclose($fp);  // close file
	}
}

//国際言語化対応
add_action( 'plugins_loaded', function() {
    //$plugin_name = dirname(plugin_basename(__FILE__));
    $plugin_name = 'ns_digital_data_seller_plugin';
    
    $dir_name = dirname(plugin_basename(__FILE__));
    $plugin_lang_dir = $dir_name . '/languages/';
    load_plugin_textdomain( $plugin_name, false, $plugin_lang_dir );
});
//load_plugin_textdomain( 'ns_digital_data_seller_plugin', false, basename( dirname( __FILE__ ) ).'/languages');});

// ファイルのコンテンツタイプを取得する
function nsddsp_get_file_ctype( $extension ) 
{
	switch( $extension ):
		case 'ac'       : $ctype = "application/pkix-attr-cert"; break;
		case 'adp'      : $ctype = "audio/adpcm"; break;
		case 'ai'       : $ctype = "application/postscript"; break;
		case 'aif'      : $ctype = "audio/x-aiff"; break;
		case 'aifc'     : $ctype = "audio/x-aiff"; break;
		case 'aiff'     : $ctype = "audio/x-aiff"; break;
		case 'air'      : $ctype = "application/vnd.adobe.air-application-installer-package+zip"; break;
		case 'apk'      : $ctype = "application/vnd.android.package-archive"; break;
		case 'asc'      : $ctype = "application/pgp-signature"; break;
		case 'atom'     : $ctype = "application/atom+xml"; break;
		case 'atomcat'  : $ctype = "application/atomcat+xml"; break;
		case 'atomsvc'  : $ctype = "application/atomsvc+xml"; break;
		case 'au'       : $ctype = "audio/basic"; break;
		case 'aw'       : $ctype = "application/applixware"; break;
		case 'avi'      : $ctype = "video/x-msvideo"; break;
		case 'bcpio'    : $ctype = "application/x-bcpio"; break;
		case 'bin'      : $ctype = "application/octet-stream"; break;
		case 'bmp'      : $ctype = "image/bmp"; break;
		case 'boz'      : $ctype = "application/x-bzip2"; break;
		case 'bpk'      : $ctype = "application/octet-stream"; break;
		case 'bz'       : $ctype = "application/x-bzip"; break;
		case 'bz2'      : $ctype = "application/x-bzip2"; break;
		case 'ccxml'    : $ctype = "application/ccxml+xml"; break;
		case 'cdmia'    : $ctype = "application/cdmi-capability"; break;
		case 'cdmic'    : $ctype = "application/cdmi-container"; break;
		case 'cdmid'    : $ctype = "application/cdmi-domain"; break;
		case 'cdmio'    : $ctype = "application/cdmi-object"; break;
		case 'cdmiq'    : $ctype = "application/cdmi-queue"; break;
		case 'cdf'      : $ctype = "application/x-netcdf"; break;
		case 'cer'      : $ctype = "application/pkix-cert"; break;
		case 'cgm'      : $ctype = "image/cgm"; break;
		case 'class'    : $ctype = "application/octet-stream"; break;
		case 'cpio'     : $ctype = "application/x-cpio"; break;
		case 'cpt'      : $ctype = "application/mac-compactpro"; break;
		case 'crl'      : $ctype = "application/pkix-crl"; break;
		case 'csh'      : $ctype = "application/x-csh"; break;
		case 'css'      : $ctype = "text/css"; break;
		case 'cu'       : $ctype = "application/cu-seeme"; break;
		case 'davmount' : $ctype = "application/davmount+xml"; break;
		case 'dbk'      : $ctype = "application/docbook+xml"; break;
		case 'dcr'      : $ctype = "application/x-director"; break;
		case 'deploy'   : $ctype = "application/octet-stream"; break;
		case 'dif'      : $ctype = "video/x-dv"; break;
		case 'dir'      : $ctype = "application/x-director"; break;
		case 'dist'     : $ctype = "application/octet-stream"; break;
		case 'distz'    : $ctype = "application/octet-stream"; break;
		case 'djv'      : $ctype = "image/vnd.djvu"; break;
		case 'djvu'     : $ctype = "image/vnd.djvu"; break;
		case 'dll'      : $ctype = "application/octet-stream"; break;
		case 'dmg'      : $ctype = "application/octet-stream"; break;
		case 'dms'      : $ctype = "application/octet-stream"; break;
		case 'doc'      : $ctype = "application/msword"; break;
		case 'docx'     : $ctype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
		case 'dotx'     : $ctype = "application/vnd.openxmlformats-officedocument.wordprocessingml.template"; break;
		case 'dssc'     : $ctype = "application/dssc+der"; break;
		case 'dtd'      : $ctype = "application/xml-dtd"; break;
		case 'dump'     : $ctype = "application/octet-stream"; break;
		case 'dv'       : $ctype = "video/x-dv"; break;
		case 'dvi'      : $ctype = "application/x-dvi"; break;
		case 'dxr'      : $ctype = "application/x-director"; break;
		case 'ecma'     : $ctype = "application/ecmascript"; break;
		case 'elc'      : $ctype = "application/octet-stream"; break;
		case 'emma'     : $ctype = "application/emma+xml"; break;
		case 'eps'      : $ctype = "application/postscript"; break;
		case 'epub'     : $ctype = "application/epub+zip"; break;
		case 'etx'      : $ctype = "text/x-setext"; break;
		case 'exe'      : $ctype = "application/octet-stream"; break;
		case 'exi'      : $ctype = "application/exi"; break;
		case 'ez'       : $ctype = "application/andrew-inset"; break;
		case 'f4v'      : $ctype = "video/x-f4v"; break;
		case 'fli'      : $ctype = "video/x-fli"; break;
		case 'flv'      : $ctype = "video/x-flv"; break;
		case 'gif'      : $ctype = "image/gif"; break;
		case 'gml'      : $ctype = "application/srgs"; break;
		case 'gpx'      : $ctype = "application/gml+xml"; break;
		case 'gram'     : $ctype = "application/gpx+xml"; break;
		case 'grxml'    : $ctype = "application/srgs+xml"; break;
		case 'gtar'     : $ctype = "application/x-gtar"; break;
		case 'gxf'      : $ctype = "application/gxf"; break;
		case 'hdf'      : $ctype = "application/x-hdf"; break;
		case 'hqx'      : $ctype = "application/mac-binhex40"; break;
		case 'htm'      : $ctype = "text/html"; break;
		case 'html'     : $ctype = "text/html"; break;
		case 'ice'      : $ctype = "x-conference/x-cooltalk"; break;
		case 'ico'      : $ctype = "image/x-icon"; break;
		case 'ics'      : $ctype = "text/calendar"; break;
		case 'ief'      : $ctype = "image/ief"; break;
		case 'ifb'      : $ctype = "text/calendar"; break;
		case 'iges'     : $ctype = "model/iges"; break;
		case 'igs'      : $ctype = "model/iges"; break;
		case 'ink'      : $ctype = "application/inkml+xml"; break;
		case 'inkml'    : $ctype = "application/inkml+xml"; break;
		case 'ipfix'    : $ctype = "application/ipfix"; break;
		case 'jar'      : $ctype = "application/java-archive"; break;
		case 'jnlp'     : $ctype = "application/x-java-jnlp-file"; break;
		case 'jp2'      : $ctype = "image/jp2"; break;
		case 'jpe'      : $ctype = "image/jpeg"; break;
		case 'jpeg'     : $ctype = "image/jpeg"; break;
		case 'jpg'      : $ctype = "image/jpeg"; break;
		case 'js'       : $ctype = "application/javascript"; break;
		case 'json'     : $ctype = "application/json"; break;
		case 'jsonml'   : $ctype = "application/jsonml+json"; break;
		case 'kar'      : $ctype = "audio/midi"; break;
		case 'latex'    : $ctype = "application/x-latex"; break;
		case 'lha'      : $ctype = "application/octet-stream"; break;
		case 'lrf'      : $ctype = "application/octet-stream"; break;
		case 'lzh'      : $ctype = "application/octet-stream"; break;
		case 'lostxml'  : $ctype = "application/lost+xml"; break;
		case 'm3u'      : $ctype = "audio/x-mpegurl"; break;
		case 'm4a'      : $ctype = "audio/mp4a-latm"; break;
		case 'm4b'      : $ctype = "audio/mp4a-latm"; break;
		case 'm4p'      : $ctype = "audio/mp4a-latm"; break;
		case 'm4u'      : $ctype = "video/vnd.mpegurl"; break;
		case 'm4v'      : $ctype = "video/x-m4v"; break;
		case 'm21'      : $ctype = "application/mp21"; break;
		case 'ma'       : $ctype = "application/mathematica"; break;
		case 'mac'      : $ctype = "image/x-macpaint"; break;
		case 'mads'     : $ctype = "application/mads+xml"; break;
		case 'man'      : $ctype = "application/x-troff-man"; break;
		case 'mar'      : $ctype = "application/octet-stream"; break;
		case 'mathml'   : $ctype = "application/mathml+xml"; break;
		case 'mbox'     : $ctype = "application/mbox"; break;
		case 'me'       : $ctype = "application/x-troff-me"; break;
		case 'mesh'     : $ctype = "model/mesh"; break;
		case 'metalink' : $ctype = "application/metalink+xml"; break;
		case 'meta4'    : $ctype = "application/metalink4+xml"; break;
		case 'mets'     : $ctype = "application/mets+xml"; break;
		case 'mid'      : $ctype = "audio/midi"; break;
		case 'midi'     : $ctype = "audio/midi"; break;
		case 'mif'      : $ctype = "application/vnd.mif"; break;
		case 'mods'     : $ctype = "application/mods+xml"; break;
		case 'mov'      : $ctype = "video/quicktime"; break;
		case 'movie'    : $ctype = "video/x-sgi-movie"; break;
		case 'm1v'      : $ctype = "video/mpeg"; break;
		case 'm2v'      : $ctype = "video/mpeg"; break;
		case 'mp2'      : $ctype = "audio/mpeg"; break;
		case 'mp2a'     : $ctype = "audio/mpeg"; break;
		case 'mp21'     : $ctype = "application/mp21"; break;
		case 'mp3'      : $ctype = "audio/mpeg"; break;
		case 'mp3a'     : $ctype = "audio/mpeg"; break;
		case 'mp4'      : $ctype = "video/mp4"; break;
		case 'mp4s'     : $ctype = "application/mp4"; break;
		case 'mpe'      : $ctype = "video/mpeg"; break;
		case 'mpeg'     : $ctype = "video/mpeg"; break;
		case 'mpg'      : $ctype = "video/mpeg"; break;
		case 'mpg4'     : $ctype = "video/mpeg"; break;
		case 'mpga'     : $ctype = "audio/mpeg"; break;
		case 'mrc'      : $ctype = "application/marc"; break;
		case 'mrcx'     : $ctype = "application/marcxml+xml"; break;
		case 'ms'       : $ctype = "application/x-troff-ms"; break;
		case 'mscml'    : $ctype = "application/mediaservercontrol+xml"; break;
		case 'msh'      : $ctype = "model/mesh"; break;
		case 'mxf'      : $ctype = "application/mxf"; break;
		case 'mxu'      : $ctype = "video/vnd.mpegurl"; break;
		case 'nc'       : $ctype = "application/x-netcdf"; break;
		case 'oda'      : $ctype = "application/oda"; break;
		case 'oga'      : $ctype = "application/ogg"; break;
		case 'ogg'      : $ctype = "application/ogg"; break;
		case 'ogx'      : $ctype = "application/ogg"; break;
		case 'omdoc'    : $ctype = "application/omdoc+xml"; break;
		case 'onetoc'   : $ctype = "application/onenote"; break;
		case 'onetoc2'  : $ctype = "application/onenote"; break;
		case 'onetmp'   : $ctype = "application/onenote"; break;
		case 'onepkg'   : $ctype = "application/onenote"; break;
		case 'opf'      : $ctype = "application/oebps-package+xml"; break;
		case 'oxps'     : $ctype = "application/oxps"; break;
		case 'p7c'      : $ctype = "application/pkcs7-mime"; break;
		case 'p7m'      : $ctype = "application/pkcs7-mime"; break;
		case 'p7s'      : $ctype = "application/pkcs7-signature"; break;
		case 'p8'       : $ctype = "application/pkcs8"; break;
		case 'p10'      : $ctype = "application/pkcs10"; break;
		case 'pbm'      : $ctype = "image/x-portable-bitmap"; break;
		case 'pct'      : $ctype = "image/pict"; break;
		case 'pdb'      : $ctype = "chemical/x-pdb"; break;
		case 'pdf'      : $ctype = "application/pdf"; break;
		case 'pki'      : $ctype = "application/pkixcmp"; break;
		case 'pkipath'  : $ctype = "application/pkix-pkipath"; break;
		case 'pfr'      : $ctype = "application/font-tdpfr"; break;
		case 'pgm'      : $ctype = "image/x-portable-graymap"; break;
		case 'pgn'      : $ctype = "application/x-chess-pgn"; break;
		case 'pgp'      : $ctype = "application/pgp-encrypted"; break;
		case 'pic'      : $ctype = "image/pict"; break;
		case 'pict'     : $ctype = "image/pict"; break;
		case 'pkg'      : $ctype = "application/octet-stream"; break;
		case 'png'      : $ctype = "image/png"; break;
		case 'pnm'      : $ctype = "image/x-portable-anymap"; break;
		case 'pnt'      : $ctype = "image/x-macpaint"; break;
		case 'pntg'     : $ctype = "image/x-macpaint"; break;
		case 'pot'      : $ctype = "application/vnd.ms-powerpoint"; break;
		case 'potx'     : $ctype = "application/vnd.openxmlformats-officedocument.presentationml.template"; break;
		case 'ppm'      : $ctype = "image/x-portable-pixmap"; break;
		case 'pps'      : $ctype = "application/vnd.ms-powerpoint"; break;
		case 'ppsx'     : $ctype = "application/vnd.openxmlformats-officedocument.presentationml.slideshow"; break;
		case 'ppt'      : $ctype = "application/vnd.ms-powerpoint"; break;
		case 'pptx'     : $ctype = "application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
		case 'prf'      : $ctype = "application/pics-rules"; break;
		case 'ps'       : $ctype = "application/postscript"; break;
		case 'psd'      : $ctype = "image/photoshop"; break;
		case 'qt'       : $ctype = "video/quicktime"; break;
		case 'qti'      : $ctype = "image/x-quicktime"; break;
		case 'qtif'     : $ctype = "image/x-quicktime"; break;
		case 'ra'       : $ctype = "audio/x-pn-realaudio"; break;
		case 'ram'      : $ctype = "audio/x-pn-realaudio"; break;
		case 'ras'      : $ctype = "image/x-cmu-raster"; break;
		case 'rdf'      : $ctype = "application/rdf+xml"; break;
		case 'rgb'      : $ctype = "image/x-rgb"; break;
		case 'rm'       : $ctype = "application/vnd.rn-realmedia"; break;
		case 'rmi'      : $ctype = "audio/midi"; break;
		case 'roff'     : $ctype = "application/x-troff"; break;
		case 'rss'      : $ctype = "application/rss+xml"; break;
		case 'rtf'      : $ctype = "text/rtf"; break;
		case 'rtx'      : $ctype = "text/richtext"; break;
		case 'sgm'      : $ctype = "text/sgml"; break;
		case 'sgml'     : $ctype = "text/sgml"; break;
		case 'sh'       : $ctype = "application/x-sh"; break;
		case 'shar'     : $ctype = "application/x-shar"; break;
		case 'sig'      : $ctype = "application/pgp-signature"; break;
		case 'silo'     : $ctype = "model/mesh"; break;
		case 'sit'      : $ctype = "application/x-stuffit"; break;
		case 'skd'      : $ctype = "application/x-koan"; break;
		case 'skm'      : $ctype = "application/x-koan"; break;
		case 'skp'      : $ctype = "application/x-koan"; break;
		case 'skt'      : $ctype = "application/x-koan"; break;
		case 'sldx'     : $ctype = "application/vnd.openxmlformats-officedocument.presentationml.slide"; break;
		case 'smi'      : $ctype = "application/smil"; break;
		case 'smil'     : $ctype = "application/smil"; break;
		case 'snd'      : $ctype = "audio/basic"; break;
		case 'so'       : $ctype = "application/octet-stream"; break;
		case 'spl'      : $ctype = "application/x-futuresplash"; break;
		case 'spx'      : $ctype = "audio/ogg"; break;
		case 'src'      : $ctype = "application/x-wais-source"; break;
		case 'stk'      : $ctype = "application/hyperstudio"; break;
		case 'sv4cpio'  : $ctype = "application/x-sv4cpio"; break;
		case 'sv4crc'   : $ctype = "application/x-sv4crc"; break;
		case 'svg'      : $ctype = "image/svg+xml"; break;
		case 'swf'      : $ctype = "application/x-shockwave-flash"; break;
		case 't'        : $ctype = "application/x-troff"; break;
		case 'tar'      : $ctype = "application/x-tar"; break;
		case 'tcl'      : $ctype = "application/x-tcl"; break;
		case 'tex'      : $ctype = "application/x-tex"; break;
		case 'texi'     : $ctype = "application/x-texinfo"; break;
		case 'texinfo'  : $ctype = "application/x-texinfo"; break;
		case 'tif'      : $ctype = "image/tiff"; break;
		case 'tiff'     : $ctype = "image/tiff"; break;
		case 'torrent'  : $ctype = "application/x-bittorrent"; break;
		case 'tr'       : $ctype = "application/x-troff"; break;
		case 'tsv'      : $ctype = "text/tab-separated-values"; break;
		case 'txt'      : $ctype = "text/plain"; break;
		case 'ustar'    : $ctype = "application/x-ustar"; break;
		case 'vcd'      : $ctype = "application/x-cdlink"; break;
		case 'vrml'     : $ctype = "model/vrml"; break;
		case 'vsd'      : $ctype = "application/vnd.visio"; break;
		case 'vss'      : $ctype = "application/vnd.visio"; break;
		case 'vst'      : $ctype = "application/vnd.visio"; break;
		case 'vsw'      : $ctype = "application/vnd.visio"; break;
		case 'vxml'     : $ctype = "application/voicexml+xml"; break;
		case 'wav'      : $ctype = "audio/x-wav"; break;
		case 'wbmp'     : $ctype = "image/vnd.wap.wbmp"; break;
		case 'wbmxl'    : $ctype = "application/vnd.wap.wbxml"; break;
		case 'wm'       : $ctype = "video/x-ms-wm"; break;
		case 'wml'      : $ctype = "text/vnd.wap.wml"; break;
		case 'wmlc'     : $ctype = "application/vnd.wap.wmlc"; break;
		case 'wmls'     : $ctype = "text/vnd.wap.wmlscript"; break;
		case 'wmlsc'    : $ctype = "application/vnd.wap.wmlscriptc"; break;
		case 'wmv'      : $ctype = "video/x-ms-wmv"; break;
		case 'wmx'      : $ctype = "video/x-ms-wmx"; break;
		case 'wrl'      : $ctype = "model/vrml"; break;
		case 'xbm'      : $ctype = "image/x-xbitmap"; break;
		case 'xdssc'    : $ctype = "application/dssc+xml"; break;
		case 'xer'      : $ctype = "application/patch-ops-error+xml"; break;
		case 'xht'      : $ctype = "application/xhtml+xml"; break;
		case 'xhtml'    : $ctype = "application/xhtml+xml"; break;
		case 'xla'      : $ctype = "application/vnd.ms-excel"; break;
		case 'xlam'     : $ctype = "application/vnd.ms-excel.addin.macroEnabled.12"; break;
		case 'xlc'      : $ctype = "application/vnd.ms-excel"; break;
		case 'xlm'      : $ctype = "application/vnd.ms-excel"; break;
		case 'xls'      : $ctype = "application/vnd.ms-excel"; break;
		case 'xlsx'     : $ctype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
		case 'xlsb'     : $ctype = "application/vnd.ms-excel.sheet.binary.macroEnabled.12"; break;
		case 'xlt'      : $ctype = "application/vnd.ms-excel"; break;
		case 'xltx'     : $ctype = "application/vnd.openxmlformats-officedocument.spreadsheetml.template"; break;
		case 'xlw'      : $ctype = "application/vnd.ms-excel"; break;
		case 'xml'      : $ctype = "application/xml"; break;
		case 'xpm'      : $ctype = "image/x-xpixmap"; break;
		case 'xsl'      : $ctype = "application/xml"; break;
		case 'xslt'     : $ctype = "application/xslt+xml"; break;
		case 'xul'      : $ctype = "application/vnd.mozilla.xul+xml"; break;
		case 'xwd'      : $ctype = "image/x-xwindowdump"; break;
		case 'xyz'      : $ctype = "chemical/x-xyz"; break;
		case 'zip'      : $ctype = "application/zip"; break;
		default         : $ctype = "application/force-download";
	endswitch;

	if( wp_is_mobile() ) {
		$ctype = 'application/octet-stream';
	}
	return $ctype;
}

//メール本文のタグ文字を変換する
function nsddsp_modify_tags_on_email_body($args)
{
    $tags = array("{first_name}",     //01
	              "{last_name}",      //02
				  "{payer_email}",    //03
				  "{transaction_id}", //04
				  "{purchase_date}",  //05
				  "{download_url}",   //06
				  "{item_name}",      //07
				  "{expire_y}",       //08
				  "{expire_m}",       //09
				  "{expire_d}",       //10
				  "{expire_h}"        //11				  
				  );
    $vals = array($args['first_name'],    //01
	              $args['last_name'],     //02
				  $args['payer_email'],   //03
				  $args['transaction_id'],//04
				  $args['purchase_date'], //05
				  $args['download_url'],  //06
				  $args['item_name'],     //07
				  $args['expire_y'],      //08
				  $args['expire_m'],      //09
				  $args['expire_d'],      //10
				  $args['expire_h']       //11
				  );
    $body = stripslashes(str_replace($tags, $vals, $args['email_body']));
    return $body;
}

//購入者へメールを送信する（販売者へも確認のため同メール送信）
function nsddsp_send_mail( $args )
{
	$ret = true;

	$first_name    = $args['first_name'];
	$last_name     = $args['last_name'];			
	$payer_email   = $args['payer_email'];
	$txn_id        = $args['transaction_id'];
	$download_url  = $args['download_url'];
	$purchase_date = $args['purchase_date'];
	$item_name     = $args['item_name'];

	$from_email = get_option(NSDDSP_OPTION_ID_SELLER_FROM_EMAIL);
	$from_name  = get_option(NSDDSP_OPTION_ID_SELLER_FROM_NAME);
   
	$bcc_email  = get_option(NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS);

	//題名の中のダグを変換する
	$subject = get_option(NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT);	        
	$args['email_body'] = $subject;	
	$subject = nsddsp_modify_tags_on_email_body($args);

    //本文の中のダグを変換する
	$body    = get_option(NSDDSP_OPTION_ID_PAYER_MAIL_BODY);
	$args['email_body'] = $body;
	
	$body = nsddsp_modify_tags_on_email_body($args);

	//nsddsp_debug_log('Applying filter - nsddsp_modify_tags_on_email_body', true);

    nsddsp_debug_log('$from_email = '.$from_email.' $from_name = '.$from_name, true);	
    if( isset($from_name) )
	{
		$headers[] = 'From: "'.$from_name.'"<'.$from_email.'>';
	}
	else 
	{
		$headers[] = 'From: "'.$from_email.'"<'.$from_email.'>';
	}
    if( isset($bcc_email) )
	{
	    $headers[] = 'Bcc: '.$bcc_email;
	}

	if(!empty($from_email) && !empty($payer_email))
	{	
		$return = wp_mail($payer_email, $subject, $body, $headers);
		if($return)
		{
			nsddsp_debug_log('Product Email successfully sent to '.$payer_email,true);			
		}
		else 
		{
			nsddsp_debug_log('Product Email Error sent to '.$payer_email,false);			
			$ret = false;
		}
	}
	else 
	{
		nsddsp_debug_log('Email Empty Error sent to ['.$payer_email.'] from ['.$from_email.']',false);			
		$ret = false;
	}

	//販売者へのメール送信
	//題名の中のダグを変換する
	$subject = get_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT);	        
	$args['email_body'] = $subject;	
	$subject = nsddsp_modify_tags_on_email_body($args);

    //本文の中のダグを変換する
	$body    = get_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY);
	$args['email_body'] = $body;	
	$body = nsddsp_modify_tags_on_email_body($args);

	$from_to_seller_email = get_option(NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL);
	$to_to_seller_email = get_option(NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL);

    nsddsp_debug_log('$from_to_seller_email = '.$from_to_seller_email, true);
    
    //ヘッダーをまず初期化する（BCCなどの設定を削除するため）
    $headers = array();
    $headers[] = 'From: "'.$from_to_seller_email.'"<'.$from_to_seller_email.'>';	

	if(!empty($from_to_seller_email) && !empty($to_to_seller_email))
	{	
		$return = wp_mail($to_to_seller_email, $subject, $body, $headers);
		if($return)
		{
			nsddsp_debug_log('Product Email successfully sent to '.$to_to_seller_email,true);			
		}
		else 
		{
			nsddsp_debug_log('Product Email Error sent to '.$to_to_seller_email,false);			
			$ret = false;
		}
	}
	else 
	{
		nsddsp_debug_log('Email Empty Error sent to ['.$to_to_seller_email.'] from ['.$from_to_seller_email.']',false);			
		$ret = false;
	}

	return $ret;
}

//エラーが発生した場合に販売者へメールを送信する
function nsddsp_send_error_mail( $args, $error_message )
{
	$ret = true;

	nsddsp_debug_log('-- nsddsp_send_error_mail --', true);

	$first_name    = $args['first_name'];
	$last_name     = $args['last_name'];			
	$payer_email   = $args['payer_email'];
	$txn_id        = $args['transaction_id'];
	$download_url  = $args['download_url'];
	$purchase_date = $args['purchase_date'];
	$item_name     = $args['item_name'];

	//販売者へのエラーメール送信
	//題名の中のダグを変換する
	$subject = "[error] ".get_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT);	        
	$args['email_body'] = $subject;	
	$subject = nsddsp_modify_tags_on_email_body($args);

    //本文の中のダグを変換する
	$body    = get_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY);
	$args['email_body'] = $body;	
	$body = nsddsp_modify_tags_on_email_body($args);

	//エラーメッセージを追加
	$body =  $error_message."\n\n".$body; 

	$from_to_seller_email = get_option(NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL);
	$to_to_seller_email = get_option(NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL);

    nsddsp_debug_log('$from_to_seller_email = '.$from_to_seller_email, true);
    
    //ヘッダーをまず初期化する（BCCなどの設定を削除するため）
    $headers = array();
    $headers[] = 'From: "'.$from_to_seller_email.'"<'.$from_to_seller_email.'>';	

	if(!empty($from_to_seller_email) && !empty($to_to_seller_email))
	{	
		$return = wp_mail($to_to_seller_email, $subject, $body, $headers);
		if($return)
		{
			nsddsp_debug_log('Product Email successfully sent to '.$to_to_seller_email,true);			
		}
		else 
		{
			nsddsp_debug_log('Product Email Error sent to '.$to_to_seller_email,false);			
			$ret = false;
		}
	}
	else 
	{
		nsddsp_debug_log('Email Empty Error sent to ['.$to_to_seller_email.'] from ['.$from_to_seller_email.']',false);			
		$ret = false;
	}
	return $ret;
}

//ダウンロードファイルをポストデータ毎にコピーする
function nsddsp_downfile_copy_to_post($item_name,$ipn_post_id) 
{   
	//ダウンロードするファイルのファイル名
	$new_filename = '';

	// nsddsp_debug_log('nsddsp_downfile_copy_to_post()',false);
	// nsddsp_debug_log('$item_name='.$item_name,false);
	// nsddsp_debug_log('$ipn_post_id='.$ipn_post_id,false);

	//ダウンロードファイルデータから販売ファイルを取得し、IPNデータ用にコピーする
	$down_id_list = get_posts( array( 'post_type' => NSDDSP_POST_ID_DOWNFILE, 'post_status' => 'any', 'numberposts' => -1 ) );
	if ( $down_id_list ) 
	{
		$isFindItem = false;
		foreach ( $down_id_list as $down_data ) 
		{            
			$post_meta = get_post_meta($down_data->ID, NSDDSP_METABOX_ID_DOWN_ID, true);	    
			if ($post_meta) 
			{                
				$paypal_item_name = $post_meta['paypal_item_name'];                
				if( $paypal_item_name == $item_name )
				{					
					//ファイルをサーバーサイドでコピーする
					$requested_file = nsddsp_getDownFileLocalUrl( $down_data->ID );
					//ダウンロードファイルのローカル保管場所（ファイルパスなど）を取得する
					$file_path  = str_replace( site_url(), '', $requested_file );//180117 記録時に「site_url()」を削除しているため現在不要だが一応残しておく
					$file_path  = realpath( ABSPATH . $file_path );
					
					//ファイル名の調整					
					$new_filename = basename( $requested_file );					
					//ダウンロードさせるファイル名を調整（設定が無ければそのままとする）
					$down_filename = nsddsp_getDownFileName( $down_data->ID );
					if( !empty($down_filename) )
					{
						$new_filename = $down_filename;
					}
					$post_id_copy_dir = strval($ipn_post_id);

					$newdir = nsddsp_get_upload_dir().'/'.$post_id_copy_dir;
					mkdir($newdir);//ディレクトリ作成
					$newfile = $newdir.'/'.$new_filename;					
					nsddsp_debug_log('src $file_path='.$file_path,true);
					nsddsp_debug_log('dst $newfile  ='.$newfile,true);

					if (!copy($file_path, $newfile)) 
					{
						nsddsp_debug_log('Error:nsddsp_downfile_copy_to_post() copy()',false);						
					}
					else 
					{
						$isFindItem = true;
					}
					break;
				}                                
			} 
			else
			{
				nsddsp_debug_log('Error:nsddsp_downfile_copy_to_post() get_post_meta()',false);
			}
		}

		if( $isFindItem == false )
		{
		 	nsddsp_debug_log(__('Error:nsddsp_downfile_copy_to_post() "Item name" is not found $item_name= ','ns_digital_data_seller_plugin').$item_name,false);
		}
	}
	else 
	{
		nsddsp_debug_log(__('Error:nsddsp_downfile_copy_to_post() Post data not found','ns_digital_data_seller_plugin'),false);
	}

	return $new_filename;
}

//Paypal IPNメッセージを処理する
function nsddsp_process_paypalipn() 
{
    if(isset($_REQUEST['nsddsp_paypal_ipn']) == false)
    {
    	//関連のオプションがない場合は、以降の処理は実行せずに戻す（別のWordpressの処理にまかせる）
    	return;
    }

	//paypalのサンドボックス使用かをチェックし、ポストバックURLを変更する
    //$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
	//$paypal_url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
	$paypal_url = NSDDSP_PAYPAL_URL;
    $sandbox_mode = false;

    $sandbox = get_option(NSDDSP_OPTION_ID_USE_SANDBOX);
    if ($sandbox) // Enable sandbox testing
    {
        //$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		//$paypal_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
		$paypal_url = NSDDSP_PAYPAL_SANDBOX_URL;
        $sandbox_mode = true;
    }

	$ipn_data = array();

    $post_string = '';
    foreach ($_POST as $field => $value) 
	{
        $ipn_data["$field"] = $value;
        $post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
    }

	//ポストバック正常時の処理
	$array_temp = $ipn_data;
	$ipn_data   = array_map('sanitize_text_field', $array_temp);	
	$txn_id     = isset($ipn_data['txn_id']) ? $ipn_data['txn_id'] : '';//トランザクションID
	$transaction_type = isset($ipn_data['txn_type']) ? $ipn_data['txn_type'] : '';
	$payment_status = isset($ipn_data['payment_status']) ? $ipn_data['payment_status'] : '';
	$transaction_subject = isset($ipn_data['transaction_subject']) ? $ipn_data['transaction_subject'] : '';
	$custom_value_str = isset($ipn_data['custom']) ? $ipn_data['custom'] : '';
	$first_name = isset($ipn_data['first_name']) ? $ipn_data['first_name'] : '';
	$last_name  = isset($ipn_data['last_name']) ? $ipn_data['last_name'] : '';        
	$street_address = isset($ipn_data['address_street']) ? $ipn_data['address_street'] : '';
	$city       = isset($ipn_data['address_city']) ? $ipn_data['address_city'] : '';
	$state      = isset($ipn_data['address_state']) ? $ipn_data['address_state'] : '';
	$zip        = isset($ipn_data['address_zip']) ? $ipn_data['address_zip'] : '';
	$country    = isset($ipn_data['address_country']) ? $ipn_data['address_country'] : '';
	$phone      = isset($ipn_data['contact_phone']) ? $ipn_data['contact_phone'] : '';
	$address    = $street_address.", ".$city.", ".$state.", ".$zip.", ".$country;
	
	//独自追加
	$mc_gross = isset($ipn_data['mc_gross']) ? $ipn_data['mc_gross'] : '';//金額
	$tax      = isset($ipn_data['tax']) ? $ipn_data['tax'] : '';//税額
	
	$payment_date = isset($ipn_data['payment_date']) ? $ipn_data['payment_date'] : '';//日時（00:24:30 Dec 23, 2016 PST）
	$charset  = isset($ipn_data['charset']) ? $ipn_data['charset'] : '';//文字コード        
	$mc_fee   = isset($ipn_data['mc_fee']) ? $ipn_data['mc_fee'] : '';//Paypal手数料
	
	$business = isset($ipn_data['business']) ? $ipn_data['business'] : '';//売り手Eメールアドレス？アカウント？
	$quantity = isset($ipn_data['quantity']) ? $ipn_data['quantity'] : '';//購入数（ライセンスなので１つでないとおかしい）
	$verify_sign = isset($ipn_data['verify_sign']) ? $ipn_data['verify_sign'] : '';//トランザクション暗号文字列	
	$receiver_email = isset($ipn_data['receiver_email']) ? $ipn_data['receiver_email'] : '';//売り手Eメールアドレス	
	$payer_email = isset($ipn_data['payer_email']) ? $ipn_data['payer_email'] : '';//購入者Eメールアドレス
	$item_name   = isset($ipn_data['item_name']) ? $ipn_data['item_name'] : '';//ライセンス製品名
	$mc_currency = isset($ipn_data['mc_currency']) ? $ipn_data['mc_currency'] : '';//通貨（例：JPYなど）
	$item_number = isset($ipn_data['item_number']) ? $ipn_data['item_number'] : '';//アイテムID（例：OTSK0010など）	
	$IPN_status  = isset($ipn_data['IPN_status']) ? $ipn_data['IPN_status'] : '';//「Verified」など

	//IPNデータを記録登録しておく
	//カスタム投稿に投稿する
	$ipn_content = '';
	foreach ($ipn_data as $key => $value) 
	{
		$ipn_content .= $key.'---'.$value.'<br/>';
	}
	if($ipn_content=='')$ipn_content='Nothing.'.'<br/>';
	
	////区切り線を追加
	//$ipn_content .= '------------------------------------'.'<br/>';

	$post_value = array(	
		'post_title' => '0000',// 投稿のタイトル。
		'post_type'     => NSDDSP_POST_ID_IPNDATA,//カスタム投稿タイプ「IPNデータ」
		'post_content'  => $ipn_content,
		'post_status' => 'trash' // 公開ステータス。
	);
	$insert_post_id = wp_insert_post($post_value); //$insert_idには投稿のID（「wp_posts」テーブルの「ID」）が入る。 投稿に失敗した場合は0が返る。
	if($insert_post_id)
	{		
		$updated_post_value = array(
			'ID'             => $insert_post_id,
			'post_title'    => $insert_post_id,
			'post_type'     => NSDDSP_POST_ID_IPNDATA,
			'post_content'  => $ipn_content,
			'post_status' => 'publish'
		);
		wp_update_post($updated_post_value);

		$is_copy_downfile = get_option(NSDDSP_OPTION_ID_IS_COPY_DOWNFILE);
		if($is_copy_downfile)
		{
			//171121
			//IPNデータ毎に販売データをコピーする(再配布対応) 戻り値として、ファイル名を取得する
			$ipn_filename = nsddsp_downfile_copy_to_post($item_name,$insert_post_id);
		}
		else 
		{
			$ipn_filename = "";
		}

		//購入日を記録する
		$purchase_date = date_i18n("Y-m-d");

		$ipn_email              = $payer_email;
		$ipn_paypal_item_name   = $item_name;
		$ipn_first_name         = $first_name;
		$ipn_last_name          = $last_name;
		$ipn_txn_id             = $txn_id;
		$ipn_purchase_date      = $purchase_date;	
		$ipn_payment_status     = $payment_status;
		$ipn_status             = __('Email not sent','ns_digital_data_seller_plugin');//'メール未送信';

		$post_meta = array( 'ipn_email'             => $ipn_email,
							'ipn_paypal_item_name'  => $ipn_paypal_item_name,
							'ipn_first_name'        => $ipn_first_name,
							'ipn_last_name'         => $ipn_last_name,
							'ipn_txn_id'            => $ipn_txn_id,
							'ipn_purchase_date'     => $ipn_purchase_date,
							'ipn_payment_status'    => $ipn_payment_status,
							'ipn_status'            => $ipn_status,
							'ipn_filename'          => $ipn_filename
		);
		update_post_meta($insert_post_id, NSDDSP_METABOX_ID_IPN_DATA, $post_meta);		
	}
	else 
	{
		nsddsp_debug_log('Error: wp_insert_post($post_value)', false);
	}

    //paypalポストバック準備
    $validate_ipn = array( 'cmd' => '_notify-validate' );
    $validate_ipn += wp_unslash( $_POST );
    $params = array(
                'body'        => $validate_ipn,
                'timeout'     => 60,
                'httpversion' => '1.1',
                'compress'    => false,
                'decompress'  => false,
                'user-agent'  => 'ns_digital_data_seller_plugin'
    );

    //paypalポストバックの返答確認
	$connection_url = $paypal_url;
    $response = wp_safe_remote_post( $connection_url, $params );
    if ( ! is_wp_error( $response ) && strstr( $response['body'], 'VERIFIED' ) ) 
	{
        if($payment_status == "Completed")
		{            
			//支払完了時の処理
            //「txn_id」の重複チェック
			$option_completed_txn_ids = get_option(NSDDSP_OPTION_ID_COMPLETED_TXN_IDS);
			$pos_txn_ids = strpos($option_completed_txn_ids, $ipn_txn_id);
			if ($pos_txn_ids !== false ) 
			{
				nsddsp_debug_log(__('Duplicate transaction ID','ns_digital_data_seller_plugin').' $ipn_txn_id = '.$ipn_txn_id, false);
				return;												
			} 
			//「txn_id」の重複データの更新
			$kugiri = ',';
			$option_completed_txn_ids = $ipn_txn_id.$kugiri.$option_completed_txn_ids;			
			$txn_ids = explode( $kugiri, $option_completed_txn_ids );
			$new_completed_txn_ids = '';	
			$ids_max = min(count($txn_ids),300);//300データまで記録する
			for( $i = 0; $i < $ids_max; $i++ )
			{
				if( !empty($txn_ids[$i]) )
				{
					$new_completed_txn_ids .= $txn_ids[$i].$kugiri;
				}
			}
			update_option(NSDDSP_OPTION_ID_COMPLETED_TXN_IDS,$new_completed_txn_ids);

            //「receiver_email」が売り手（自分）のメールアドレスになっているかチェック！			
			$option_receiver_email = get_option(NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL);
			if (!empty($option_receiver_email) && $option_receiver_email != $receiver_email)
			{
				nsddsp_debug_log('$option_receiver_email != $receiver_email', false);
				return;
			}
            
            //TODO：商品番号や価格などチェック※現在未実装QQQQQQQQQQQQQQQQQQQQQQQQQ

			//期限を求める
			$expire_hours = get_option(NSDDSP_OPTION_ID_EXPIRE_HOURS);
            $expire = date_i18n("ymdH",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));			
			//ファイルダウンロードURLを求める
			$download_url = nsddsp_getDownFileUrlFromItemName($item_name,$expire,$insert_post_id);
            
			if( empty($download_url) )
			{
				nsddsp_debug_log(__('Download file not found.','ns_digital_data_seller_plugin').' $item_name ='.$item_name,false);				
				return;
			}

			//購入への自動メール送信の設定を取得する
			$is_auto_send_payer_mail = get_option(NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL);
			if( isset($is_auto_send_payer_mail) && $is_auto_send_payer_mail == true )
			{
				//期限のタグを取得
				$expire_y = date_i18n("Y",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
				$expire_m = date_i18n("m",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
				$expire_d = date_i18n("d",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));
				$expire_h = date_i18n("H",strtotime('+'.$expire_hours.' hours', current_time('timestamp')));

				//問題がなければ、購入者宛にメール送信			
				$args = array();

				$args['first_name']     = $first_name;
				$args['last_name']      = $last_name;			
				$args['payer_email']    = $payer_email;
				$args['transaction_id'] = $txn_id;
				$args['download_url']   = $download_url;
				$args['purchase_date']  = $purchase_date;
				$args['item_name']      = $item_name;
				$args['expire_y']       = $expire_y;
				$args['expire_m']       = $expire_m;
				$args['expire_d']       = $expire_d;
				$args['expire_h']       = $expire_h;

				$return_send_mail = nsddsp_send_mail( $args );
				if( $return_send_mail == true )
				{
					$ipn_status              = __('Email transmission completion','ns_digital_data_seller_plugin');//'メール送信済み';
					$post_meta['ipn_status'] = $ipn_status;
					update_post_meta($insert_post_id, NSDDSP_METABOX_ID_IPN_DATA, $post_meta);
				}
			}
			else
			{
				//メールを自動で送信しない設定になっている場合
				nsddsp_debug_log('$is_auto_send_payer_mail = '.$is_auto_send_payer_mail, true);
			}				
		}
		else 
		{
			nsddsp_debug_log('$payment_status is not Completed $payment_status = '.$payment_status,true);	
		}
    }
    else 
    {
		$ipn_status              = __('Paypal post-back INVALID','ns_digital_data_seller_plugin');//'Paypalポストバック INVALID発生';
		$post_meta['ipn_status'] = $ipn_status;
 		update_post_meta($insert_post_id, NSDDSP_METABOX_ID_IPN_DATA, $post_meta);

		 $error_message = '';
        //ポストバック異常発生時の処理        
        if ( is_wp_error( $response ) ) 
		{
			nsddsp_debug_log('Error response: ' . $response->get_error_message(), false);
			$error_message = 'Error response: '.$response->get_error_message();
		}
		else 
		{
			nsddsp_debug_log('$response[\'body\']= '.$response['body'],false );
			$error_message = '$response[\'body\']= '.$response['body'];
		}
		
		//販売者へエラーメッセージ送信		
		nsddsp_send_error_mail( $args, $error_message );
    }
	exit;
}
add_action( 'init', 'nsddsp_process_paypalipn', 2 );

//IPアドレスを取得
function nsddsp_get_Ip_Address()
{
    $ip= array();
    if (isset($_SERVER['HTTP_SP_HOST']) && preg_match('/^\d+(?:\.\d+){3}$/D', $_SERVER['HTTP_SP_HOST']))
        $ip[]= $_SERVER['HTTP_SP_HOST'];
    if (isset($_SERVER['HTTP_VIA']) && preg_match('/.*\s(\d+(?:\.\d+){3})/', $_SERVER['HTTP_VIA'], $match))
        $ip[]= $match[1];
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^\d+(?:\.\d+){3}/', $_SERVER['HTTP_CLIENT_IP'], $match))
        $ip[]= $match[0];
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})/i', $_SERVER['HTTP_CLIENT_IP'], $match))
        $ip[]= implode('.', array(hexdec($match[1]), hexdec($match[2]), hexdec($match[3]), hexdec($match[4])));
    if (isset($_SERVER['HTTP_FORWARDED']) && preg_match('/.*\s(\d+(?:\.\d+){3})/', $_SERVER['HTTP_FORWARDED'], $match))
        $ip[]= $match[1];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/^\d+(?:\.\d+){3}/', $_SERVER['HTTP_X_FORWARDED_FOR'], $match))
        $ip[]= $match[0];
    if (isset($_SERVER['HTTP_FROM']) && preg_match('/^\d+(?:\.\d+){3}$/D', $_SERVER['HTTP_FROM']))
        $ip[]= $_SERVER['HTTP_FROM'];

    $addr= '';
	foreach ($ip as $value)
	{
		if (!preg_match('/^(?:10|172\.16|192\.168|127\.0|0\.|169\.254)\./', $value) and $addr=$value)
		{
			 break;
		}
	}

    return($addr ? $addr : $_SERVER['REMOTE_ADDR']);
}

//クライアントからのファイルダウンロード要求に対応
function nsddsp_process_download() 
{
	//ログファイルのリセット命令をここで処理する
	if(isset($_POST['nsddsp_reset_logfile'])) 
	{
		if(isset($_POST['nsddsp_reset_logfile_password']) && $_POST['nsddsp_reset_logfile_password'] == NSDDSP_HANYO_PASSWORD)
		{
			// Reset the debug log file
			nsddsp_reset_logfile();
		}
		else
		{
			wp_die('Password you entered is incorrect.');
		}
		return;
	}

	//ログファイルの内容を表示する
	if(isset($_POST['nsddsp_view_logfile'])) 
	{        
        nsddsp_view_logfile();
		return;
	}

	//GETパラメータを取得する
	$args = array(
		'nsddsp'       => ( isset( $_GET['nsddsp']      ) ) ? $_GET['nsddsp']      : '',
		'download_id'  => ( isset( $_GET['download_id'] ) ) ? $_GET['download_id'] : '',
		'expire'       => ( isset( $_GET['expire']      ) ) ? $_GET['expire']      : '',
		'token'        => ( isset( $_GET['ncl_token']   ) ) ? $_GET['ncl_token']   : '',
		'ipn'          => ( isset( $_GET['ipn']         ) ) ? $_GET['ipn']         : ''
	);
	
	//if ( empty( $args['nsddsp'] ) || empty( $args['download_id'] ) || empty( $args['expire'] ) ) 
	if ( empty( $args['nsddsp'] ) ) 
	{
		//関連のオプションがない場合は、以降の処理は実行せずに戻す（別のWordpressの処理にまかせる）
		return;
	}
	
	//IPNのポストIDを記録する
	$ipn_post_id = $args['ipn'];
	// if ( isset( $_GET['ipn'] ) ) 
	// {
	// 	$ipn_post_id = $_GET['ipn'];
	// }

	//IPアドレス取得
	$ip_address = nsddsp_get_Ip_Address();

	//ダウンロードさせても良いか判断する（有効なトークン、有効な期限内のダウンロード）
	//if(nsddsp_enableDownFileUrl( $args['download_id'],$args['expire'],$args['token'] ) == false )
	if(nsddsp_enableDownFileUrl( $args['download_id'],$args['expire'],$ipn_post_id,$args['token'] ) == false )
	{		
		//旧バージョン(1.0.7以前)の有効トークンもチェックする
		if(nsddsp_enableDownFileUrl_oldversion( $args['download_id'],$args['expire'],$args['token'] ) == false )
		{
			nsddsp_debug_log('Disable URL DownloadFile download_id='.$args['download_id'].' expire='.$args['expire'].' ip='.$ip_address.' ipn_post_id ='.$ipn_post_id, true);
			
					$message = __('This downloading URL seems to be abnormality or over a deadline.','ns_digital_data_seller_plugin');//"ダウンロードURLが異常か、もしくはダウンロード期限を超えているようです。";
					$title = __('Download failed','ns_digital_data_seller_plugin');//"ダウンロード失敗";
					$status = 400;
					wp_die( $message, $title, array( 'response' => $status ));	
					return;			
		}
	}

	$file_path = '';
	$requested_file = '';
	//IPN DATA POST IDからローカル保管場所（URL）を取得する
	$file_path = nsddsp_getDownFileLocalPathforIPNData( $ipn_post_id );
	
	//ダウンロードファイルIDからダウンロードファイルのローカル保管場所（URL）を取得する
	if( empty($file_path) )
	{		
		$requested_file = nsddsp_getDownFileLocalUrl( $args['download_id'] );
		//ダウンロードファイルのローカル保管場所（ファイルパスなど）を取得する
		$file_path  = str_replace( site_url(), '', $requested_file );//180117 記録時に「site_url()」を削除しているため現在不要だが一応残しておく
		$file_path  = realpath( ABSPATH . $file_path );
		nsddsp_debug_log('get DownloadFile for download_id ='.$args['download_id'], true);
	}
	else 
	{
		nsddsp_debug_log('get DownloadFile for ipn_post_id ='.$ipn_post_id, true);
	}
	
	//ログ記録
	nsddsp_debug_log('DownloadFile download_id='.$args['download_id'].' expire='.$args['expire'].' ip='.$ip_address.' ipn_post_id ='.$ipn_post_id, true);		

	//拡張子とコンテンツタイプを取得
	$parts = explode( '.', $file_path );
	$file_extension = end( $parts );
	$ctype = nsddsp_get_file_ctype($file_extension);
	
	//ファイルの転送処理
	$chunksize = 1024 * 1024;
	$buffer    = '';
	$cnt       = 0;
	$handle    = @fopen( $file_path, 'r' );

	if ( false === $handle ) 
	{	
		$ip_address = nsddsp_get_Ip_Address();
		nsddsp_debug_log('Error DownloadFileUrl Not Found Download File', false);		
		
		$message = __('Download file could not be found.','ns_digital_data_seller_plugin');//"ダウンロードファイルが見つかりませんでした。";
		$title = __('Download failed','ns_digital_data_seller_plugin');//"ダウンロード失敗";
		$status = 400;
		wp_die( $message, $title, array( 'response' => $status ));
		return;
	}

	//ヘッダー出力
	//$filename = basename( $requested_file );
	$filename = basename( $file_path );
	
    //ダウンロードさせるファイル名を調整（設定が無ければそのままとする）
    $down_filename = nsddsp_getDownFileName( $args['download_id'] );
    if( !empty($down_filename) )
    {
        $filename = $down_filename;
    }
    
	nocache_headers();
	header("Robots: none");
	header("Content-Type: " . $ctype . "");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=\"" .$filename. "\"");
	header("Content-Transfer-Encoding: binary");

	if ( $size = @filesize( $file_path ) ) 
	{
		header("Content-Length: " . $size );
	}

	//バイナリで順に出力
	while ( ! @feof( $handle ) ) 
	{
		$buffer = @fread( $handle, $chunksize );
		echo $buffer;
		if ( $retbytes ) 
		{
			$cnt += strlen( $buffer );
		}
	}
	$status = @fclose( $handle );
	
	//ダウンロード回数をカウントアップ
	nsddsp_countupDownFile( $args['download_id'], $ipn_post_id);

	exit;
}
add_action( 'init', 'nsddsp_process_download', 1 );

// ランダム文字列取得
function nsddsp_get_random_str($length = 30)
{
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', $length)), 0, $length);
}

// プラグイン登録時の処理
function nsddsp_register_settings_activate() 
{
    // オプションの追加と初期値設定
	$random_init_token = nsddsp_get_random_str(30);
	add_option(NSDDSP_OPTION_ID_TOKENWORD, $random_init_token, '', 'no' );//初期値適当なパスワード
	//add_option(NSDDSP_OPTION_ID_TOKENWORD, NSDDSP_INIT_TOKEN_PASSWORD, '', 'no' );//初期値適当なパスワード
    
	add_option(NSDDSP_OPTION_ID_USE_SANDBOX, '1', '', 'no' );
	add_option(NSDDSP_OPTION_ID_IS_AUTO_SEND_PAYER_MAIL, '1', '', 'no' );
    add_option(NSDDSP_OPTION_ID_RECORD_LOG, '1', '', 'no' );

    add_option(NSDDSP_OPTION_ID_EXPIRE_HOURS, '72', '', 'no' );
    add_option(NSDDSP_OPTION_ID_IS_COPY_DOWNFILE, '0', '', 'no' );
	add_option(NSDDSP_OPTION_ID_PAYPAL_RECEIVER_EMAIL, 'seller_paypal_emailaddress@dummy.co.jp', '', 'no' );
    add_option(NSDDSP_OPTION_ID_SELLER_FROM_EMAIL, 'seller_from_emailaddress@dummy.co.jp', '', 'no' );
    add_option(NSDDSP_OPTION_ID_SELLER_FROM_NAME, '', '', 'no' );
    add_option(NSDDSP_OPTION_ID_PAYER_MAIL_BCC_ADDRESS, 'seller_bcc_emailaddress@dummy.co.jp', '', 'no' );
    add_option(NSDDSP_OPTION_ID_PAYER_MAIL_SUBJECT, __('Thank you for your purchase.','ns_digital_data_seller_plugin'), '', 'no' );
    
    add_option(NSDDSP_OPTION_ID_PAYER_MAIL_BODY, __('◆◆◆◆◆◆ Thank you for purchasing ◆ ◆ ◆ ◆ ◆ ◆

-----------------------------------------------------
This email is an important email regarding your order.
Please save it carefully until dealings are completed.
-----------------------------------------------------

Dear {first_name} {last_name} 

This time,
Thank you for purchasing our product "{item_name}".

"Order details" and "Download URL" are as follows.

Please download the purchased file from "Download URL"
and use it.

-------------------------------------------------------
■ Order contents ■
[Order number (transaction ID)]
{transaction_id}

[Purchase date]
{purchase_date}

■ Download URL ■
[URL]
{download_url}

[Expiration date]
{expire_y}/{expire_m}/{expire_d} {expire_h}

-------------------------------------------------------
','ns_digital_data_seller_plugin'),'', 'no' );
    
    
//    add_option(NSDDSP_OPTION_ID_PAYER_MAIL_BODY, <<<END_OF_TEXT
//◆◆◆◆◆◆ ご購入ありがとうございました ◆◆◆◆◆◆
//
//-----------------------------------------------------
//このメールは、お客様の注文に関する大切なメールです。
//お取引が完了するまで大切に保存しておいてください。
//-----------------------------------------------------
//
//{last_name} {first_name}様
//
//この度は、
//弊社の製品「{item_name}」を
//ご購入いただき、誠にありがとうございました。
//
//「ご注文内容」および「ダウンロードURL」は以下の通りです。
//「ダウンロードURL」より、
//ご購入されたファイルをダウンロードし、ご利用ください。
//
//-------------------------------------------------------
//■ご注文内容■
//[ご注文番号（トランザクションID）]
//{transaction_id}
//
//[購入日] 
//{purchase_date}
//
//■ダウンロードURL■
//[URL]
//{download_url} 
//
//[ダウンロードURLの期限] 
//{expire_y}年{expire_m}月{expire_d}日{expire_h}時まで
//
//-------------------------------------------------------
//
//以上、よろしくお願いいたします。
//END_OF_TEXT
//, '', 'no' );

    add_option(NSDDSP_OPTION_ID_TO_SELLER_TO_EMAIL, 'to_seller_to_emailaddress@dummy.co.jp', '', 'no' );
	add_option(NSDDSP_OPTION_ID_TO_SELLER_FROM_EMAIL, 'to_seller_from_emailaddress@dummy.co.jp', '', 'no' );
    add_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_SUBJECT, __('Payment completion notification','ns_digital_data_seller_plugin'), '', 'no' );

    add_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY,__('
{first_name}:Buyer\'s first name
{last_name}:Buyer\'s last name
{payer_email}:Buyer\'s email address
{transaction_id}:Transaction ID
{purchase_date}:date of purchase
{download_url}:Download URL
{item_name}:Paypal registered product name
{expire_y}:Year of expiration of download URL
{expire_m}:Month of expiration of download URL
{expire_d}:Day of expiration of download URL
{expire_h}:Time of expiration of download URL(24 hour display)
','ns_digital_data_seller_plugin'), '', 'no' );

//    add_option(NSDDSP_OPTION_ID_TO_SELLER_MAIL_BODY, <<<END_OF_TEXT
//{first_name} : 購入者の名前
//{last_name} : 購入者の名字
//{payer_email} : 購入者のメールアドレス
//{transaction_id} : トランザクションID
//{purchase_date} : 購入日
//{download_url} : ダウンロードURL
//{item_name} : Paypal登録製品名
//{expire_y} : ダウンロードURLの有効期限の西暦年
//{expire_m} : ダウンロードURLの有効期限の月
//{expire_d} : ダウンロードURLの有効期限の日
//{expire_h} : ダウンロードURLの有効期限の時間（24時間表示）
//END_OF_TEXT
//, '', 'no' );

	add_option(NSDDSP_OPTION_ID_COMPLETED_TXN_IDS, '', '', 'no' );
}
register_activation_hook(  __FILE__, 'nsddsp_register_settings_activate' );




// //Restores the ability to upload non-image files in WordPress 4.7.1 and 4.7.2.
// function nsddsp_wp_disable_real_mime_check( $data, $file, $filename, $mimes ) {
// 	$wp_filetype = wp_check_filetype( $filename, $mimes );

// 	$ext = $wp_filetype['ext'];
// 	$type = $wp_filetype['type'];
// 	$proper_filename = $data['proper_filename'];

// 	return compact( 'ext', 'type', 'proper_filename' );
// }
// add_filter( 'wp_check_filetype_and_ext', 'nsddsp_wp_disable_real_mime_check', 10, 4 );


