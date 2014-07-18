<?php
/**
 * Helper for file image.
 */
class jFileMime
{
  /**
   * List of the mimetypes.
   * 
   * @var array
   * @static
   */
  static protected $arMimeTypeList = array(
    // text.
    'text' => array(
      'txt' => 'text/plain',
      'htm' => 'text/html',
      'html' => 'text/html',
      'php' => 'text/html',
      'css' => 'text/css',
      'js' => 'application/javascript',
      'json' => 'application/json',
      'xml' => 'application/xml',
      'swf' => 'application/x-shockwave-flash',
      'flv' => 'video/x-flv'
    ),

    // images in the internet
    'web_image' => array(
      'png' => 'image/png',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg' => 'image/jpeg',
      'gif' => 'image/gif',
      'bmp' => 'image/bmp'
    ),

    // images
    'image' => array(
      'png' => 'image/png',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg' => 'image/jpeg',
      'gif' => 'image/gif',
      'bmp' => 'image/bmp',
      'ico' => 'image/vnd.microsoft.icon',
      'tiff' => 'image/tiff',
      'tif' => 'image/tiff',
      'svg' => 'image/svg+xml',
      'svgz' => 'image/svg+xml'
    ),

    // archives
    'archive' => array(
      'zip' => 'application/zip',
      'rar' => 'application/x-rar-compressed',
      'exe' => 'application/x-msdownload',
      'msi' => 'application/x-msdownload',
      'cab' => 'application/vnd.ms-cab-compressed'
    ),

    // audio
    'audio' => array(
      'mp3'   => 'audio/mpeg',
    ),

    // video
    'video' => array(
      'mp3'   => 'audio/mpeg',
      'qt'    => 'video/quicktime',
      'mov'   => 'video/quicktime',
      'mp2'   => 'video/mpeg',
      'mpa'   => 'video/mpeg',
      'mpe'   => 'video/mpeg',
      'mpeg'  => 'video/mpeg',
      'mpg'   => 'video/mpeg',
      'mpv2'  => 'video/mpeg',
      'avi'   => 'video/x-msvideo',
      'movie' => 'video/x-sgi-movie'
    ),

    // adobe
    'adobe' => array(
      'pdf' => 'application/pdf',
      'psd' => 'image/vnd.adobe.photoshop',
      'ai' => 'application/postscript',
      'eps' => 'application/postscript',
      'ps' => 'application/postscript'
    ),

    // ms office
    'msoffice' => array(
      'doc' => 'application/msword',
      'rtf' => 'application/rtf',
      'xls' => 'application/vnd.ms-excel',
      'ppt' => 'application/vnd.ms-powerpoint'
    ),

    // ms office
    'openoffice' => array(
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
    )
  );

  /**
   * List of the mimetypes by extension.
   * 
   * @var array
   * @static
   */
  static protected $arMimeTypeExtensions = array();

  /**
   * List of the mimetypes to category.
   * 
   * @var array
   * @static
   */
  static protected $arMimeTypeCategory = array();

  /**
   * Prepare mime type's list.
   */
  protected static function prepareTypes()
  {
    if (count(self::$arMimeTypeExtensions) && count(self::$arMimeTypeCategory)) return true;

    $_ =& self::$arMimeTypeList;
    foreach(self::$arMimeTypeList as $group => $extensions)
    {
      self::$arMimeTypeExtensions += $extensions;

      $_1 =& $extensions;
      foreach($extensions as $extension => $type)
      {
        self::$arMimeTypeCategory[$type] = [$group];
      }
    }
  }
 
  /**
   * Return mimetype of the file.
   * 
   * @static
   * @param string $sFilePath Path to file.
   * @return string
   */
  public static function getMimeType($sFilePath)
  {
    self::prepareTypes();

    // Definition file extension.
    $fExtension = strtolower(@array_pop(@explode('.', $sFilePath)));

    // if extension has exists in the mime type's list.
    if (array_key_exists($fExtension, self::$arMimeTypeExtensions))
    {
      return self::$arMimeTypeExtensions[$fExtension];
    }
    // if extension Fileinfo is exists.
    elseif (function_exists('finfo_open'))
    {
      $finfo = finfo_open(FILEINFO_MIME);
      $mimetype = finfo_file($finfo, $sFilePath);
      finfo_close($finfo);

      $armime = explode(';', $mimetype);
      return array_shift($armime);
    }
    // if function mime_content_type is exists.
    elseif (function_exists('mime_content_type'))
    {
      return mime_content_type($sFilePath);
    }

    return 'application/octet-stream';    
  }
  
  /**
   * Return mimetype category of the file.
   * 
   * @static
   * @param string $sFilePath Path to file.
   * @return string
   */
  public static function getMimeTypeCategory($sFilePath)
  {
    self::prepareTypes();

    // Define mimetype.
    $mimetype = self::getMimeType($sFilePath);

    if (array_key_exists($mimetype, self::$arMimeTypeCategory))
    {
      return self::$arMimeTypeCategory[$mimetype];
    }

    return 'application';
  }

  /**
   * Convert number of bytes largest unit bytes will fit into.
   *
   * It is easier to read 1kB than 1024 bytes and 1MB than 1048576 bytes. Converts
   * number of bytes to human readable number by taking the number of that unit
   * that the bytes will go into it. Supports TB value.
   *
   * Please note that integers in PHP are limited to 32 bits, unless they are on
   * 64 bit architecture, then they have 64 bit size. If you need to place the
   * larger size then what PHP integer type will hold, then use a string. It will
   * be converted to a double, which should always have 64 bit length.
   *
   * @param int|string $bytes Number of bytes. Note max integer size for integers.
   * @param int $decimals Precision of number of decimal places. Deprecated.
   * @return bool|string False on failure. Number string on success.
   */
  public static function size_format( $bytes, $decimals = 0 )
  {
    $quant = array(
      // ========================= Origin ====
      'TB' => 1099511627776,  // pow( 1024, 4)
      'GB' => 1073741824,     // pow( 1024, 3)
      'MB' => 1048576,        // pow( 1024, 2)
      'kB' => 1024,           // pow( 1024, 1)
      'B ' => 1,              // pow( 1024, 0)
    );

    foreach ( $quant as $unit => $mag )
    {
      if ( doubleval($bytes) >= $mag )
      {
        return number_format(($bytes / $mag), $decimals, ',', '') . ' ' . $unit;
        //return number_format_i18n( $bytes / $mag, $decimals )
      }
    }

    return false;
  }

  /**
   * Convert integer number to format based on the locale.
   *
   * @since 2.3.0
   *
   * @param int $number The number to convert based on locale.
   * @param int $decimals Precision of the number of decimal places.
   * @return string Converted number in string format.
   */
  function number_format_i18n( $number, $decimals = 0 )
  {
    global $wp_locale;
    $formatted = number_format( $number, absint( $decimals ), $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep'] );
    return apply_filters( 'number_format_i18n', $formatted );
  }

  /**
   * Retrieve list of allowed mime types and file extensions.
   *
   * @return array Array of mime types keyed by the file extension regex corresponding to those types.
   */
  public static function get_allowed_mime_types()
  {
    static $mimes = false;

    if ( !$mimes ) {
      // Accepted MIME types are set here as PCRE unless provided.
      $mimes = apply_filters( 'upload_mimes', array(
      'jpg|jpeg|jpe' => 'image/jpeg',
      'gif' => 'image/gif',
      'png' => 'image/png',
      'bmp' => 'image/bmp',
      'tif|tiff' => 'image/tiff',
      'ico' => 'image/x-icon',
      'asf|asx|wax|wmv|wmx' => 'video/asf',
      'avi' => 'video/avi',
      'divx' => 'video/divx',
      'flv' => 'video/x-flv',
      'mov|qt' => 'video/quicktime',
      'mpeg|mpg|mpe' => 'video/mpeg',
      'txt|asc|c|cc|h' => 'text/plain',
      'csv' => 'text/csv',
      'tsv' => 'text/tab-separated-values',
      'rtx' => 'text/richtext',
      'css' => 'text/css',
      'htm|html' => 'text/html',
      'mp3|m4a|m4b' => 'audio/mpeg',
      'mp4|m4v' => 'video/mp4',
      'ra|ram' => 'audio/x-realaudio',
      'wav' => 'audio/wav',
      'ogg|oga' => 'audio/ogg',
      'ogv' => 'video/ogg',
      'mid|midi' => 'audio/midi',
      'wma' => 'audio/wma',
      'mka' => 'audio/x-matroska',
      'mkv' => 'video/x-matroska',
      'rtf' => 'application/rtf',
      'js' => 'application/javascript',
      'pdf' => 'application/pdf',
      'doc|docx' => 'application/msword',
      'pot|pps|ppt|pptx|ppam|pptm|sldm|ppsm|potm' => 'application/vnd.ms-powerpoint',
      'wri' => 'application/vnd.ms-write',
      'xla|xls|xlsx|xlt|xlw|xlam|xlsb|xlsm|xltm' => 'application/vnd.ms-excel',
      'mdb' => 'application/vnd.ms-access',
      'mpp' => 'application/vnd.ms-project',
      'docm|dotm' => 'application/vnd.ms-word',
      'pptx|sldx|ppsx|potx' => 'application/vnd.openxmlformats-officedocument.presentationml',
      'xlsx|xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
      'docx|dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
      'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
      'swf' => 'application/x-shockwave-flash',
      'class' => 'application/java',
      'tar' => 'application/x-tar',
      'zip' => 'application/zip',
      'gz|gzip' => 'application/x-gzip',
      'exe' => 'application/x-msdownload',
      // openoffice formats
      'odt' => 'application/vnd.oasis.opendocument.text',
      'odp' => 'application/vnd.oasis.opendocument.presentation',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
      'odg' => 'application/vnd.oasis.opendocument.graphics',
      'odc' => 'application/vnd.oasis.opendocument.chart',
      'odb' => 'application/vnd.oasis.opendocument.database',
      'odf' => 'application/vnd.oasis.opendocument.formula',
      // wordperfect formats
      'wp|wpd' => 'application/wordperfect',
      ) );
    }

    return $mimes;
  }
}
