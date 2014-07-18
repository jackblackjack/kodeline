<?php
class klUpdateToolkit
{
  /**
   * List of the disabled functions.
   * @var array
   */
  protected static $arDisabledFunctions = array();

  public static function getDigestStatus()
  {

  }

  public static function createDigest()
  {
    // Fetch system paths.
    $arPaths = self::getFilelist();

    $arDigest = self::calculateDigest($arPaths);
  }

  /**
   * Calculate digest for paths.
   * 
   * @param array $arPaths List of the paths for calculate.
   * @return boolean
   */
  public static function calculateDigest($arPaths)
  {
    // Calculate checksum for array.
    array_multisort($arPaths);
    $pathsChecksum = md5(json_encode($arPaths));

    $arP = array();

    try {
      // Create temporary digest file.
      $tmpFile = self::getTemporaryDir() . DIRECTORY_SEPARATOR . $pathsChecksum . '.dgt';

      $_ =& $arPaths;
      foreach ($arPaths as $path)
      {
        if (! is_dir($path))
        {
          $arP[str_replace(sfConfig::get('sf_lib_dir'), '', $path)] = md5_file($path);
        }
      }

      var_dump($arP); die;
    }
    // Catch exceptions.
    catch (klUpdateException $exception)
    {

    }
  }

  /**
   * Retrieve temporary directory.
   * 
   * @return string Path to system temporary directory.
   */
  public static function getTemporaryDir()
  {
    // Find by enviroments variables.
    $arEnviroments = array('TMP', 'TEMP', 'TMPDIR');
    foreach ($arEnviroments as $var) {
      if ($temp = getenv($var)) {
        return $temp;
      }
    }

    // Find temporary by creating temporary file.
    $tmpFile = tempnam(__FILE__, mt_rand(0, 9999999));
    if (! file_exists($tmpFile)) {
      throw new klUpdateException("Cannot find temporary directory", 1);
      return null;
    }

    unlink($tmpFile);
    return dirname($tmpFile);
  }

  public static function execAvailable()
  {
    static $available;

    if (!isset($available)) {
        $available = true;
        if (ini_get('safe_mode')) {
            $available = false;
        } else {
            $d = ini_get('disable_functions');
            $s = ini_get('suhosin.executor.func.blacklist');
            if ("$d$s") {
                $array = preg_split('/,\s*/', "$d,$s");
                if (in_array('exec', $array)) {
                    $available = false;
                }
            }
        }
    }

    return $available;
  }

  /**
   */
  public static function getFilelist()
  {
    if (self::isDisabled('glob') && self::isDisabled('readdir') && !class_exists('RecursiveDirectoryIterator'))
    {
      throw new klUpdateException();
    }

    // Fetch list of the files throught glob.
    if (! self::isDisabled('glob'))
    {
      // Fetch all components files.
      $arComponentsFiles = self::getFilelistByGlob(sprintf('%s/components/*', sfConfig::get('sf_lib_dir')), GLOB_NOSORT | GLOB_ERR);

      // Fetch all vendor files.
      $arVendorFiles = self::getFilelistByGlob(sprintf('%s/vendor/*', sfConfig::get('sf_lib_dir')), GLOB_NOSORT);
    }

    return array_merge($arComponentsFiles, $arVendorFiles);
  }

  public static function getFilelistByGlob($pattern, $flags = 0)
  {
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
      $files = array_merge($files, self::getFilelistByGlob($dir.'/'.basename($pattern), $flags));
    }

    return $files;
  }

  public static function getFilelistByIterator($folder, $pattern)
  {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
  }

  public static function getFilelistByReaddir($folder, $pattern)
  {
  }

  /**
   * Check function is disabled.
   * 
   * @param string $functionName Function name
   * @return boolean
   */
  public static function isDisabled($functionName)
  {
    self::$arDisabledFunctions = explode(',', ini_get('disable_functions'));
    return in_array($functionName, self::$arDisabledFunctions);
  }
}
