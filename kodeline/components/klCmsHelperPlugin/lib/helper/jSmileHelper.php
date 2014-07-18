<?php
/**
 * jSmileHelper class.
 *
 * @package     yatutu
 * @subpackage  lib.helper
 * @author      gen
 * @version     SVN: $Id: yaAssetHelper.php 1529 2010-04-28 13:33:27Z pinhead $
 */
class jSmileHelper
{
  /**
   * Convert smiley code to the icon graphic file equivalent.
   *
   * You can turn off smilies, by going to the write setting screen and unchecking
   * the box, or by setting 'use_smilies' option to false or removing the option.
   *
   * Plugins may override the default smiley list by setting the $wpsmiliestrans
   * to an array, with the key the code the blogger types in and the value the
   * image file.
   *
   * The $wp_smiliessearch global is for the regular expression and is set each
   * time the function is called.
   *
   * The full list of smilies can be found in the function and won't be listed in
   * the description. Probably should create a Codex page for it, so that it is
   * available.
   *
   * @global array $wpsmiliestrans
   * @global array $wp_smiliessearch
   */
  public static function smilies_init()
  {
    $wpsmiliestrans = array(
      ':mrgreen:' => 'icon_mrgreen.gif',
      ':neutral:' => 'icon_neutral.gif',
      ':twisted:' => 'icon_twisted.gif',
        ':arrow:' => 'icon_arrow.gif',
        ':shock:' => 'icon_eek.gif',
        ':smile:' => 'icon_smile.gif',
          ':???:' => 'icon_confused.gif',
         ':cool:' => 'icon_cool.gif',
         ':evil:' => 'icon_evil.gif',
         ':grin:' => 'icon_biggrin.gif',
         ':idea:' => 'icon_idea.gif',
         ':oops:' => 'icon_redface.gif',
         ':razz:' => 'icon_razz.gif',
         ':roll:' => 'icon_rolleyes.gif',
         ':wink:' => 'icon_wink.gif',
          ':cry:' => 'icon_cry.gif',
          ':eek:' => 'icon_surprised.gif',
          ':lol:' => 'icon_lol.gif',
          ':mad:' => 'icon_mad.gif',
          ':sad:' => 'icon_sad.gif',
            '8-)' => 'icon_cool.gif',
            '8-O' => 'icon_eek.gif',
            ':-(' => 'icon_sad.gif',
            ':-)' => 'icon_smile.gif',
            ':-?' => 'icon_confused.gif',
            ':-D' => 'icon_biggrin.gif',
            ':-P' => 'icon_razz.gif',
            ':-o' => 'icon_surprised.gif',
            ':-x' => 'icon_mad.gif',
            ':-|' => 'icon_neutral.gif',
            ';-)' => 'icon_wink.gif',
             '8)' => 'icon_cool.gif',
             '8O' => 'icon_eek.gif',
             ':(' => 'icon_sad.gif',
             ':)' => 'icon_smile.gif',
             ':?' => 'icon_confused.gif',
             ':D' => 'icon_biggrin.gif',
             ':P' => 'icon_razz.gif',
             ':o' => 'icon_surprised.gif',
             ':x' => 'icon_mad.gif',
             ':|' => 'icon_neutral.gif',
             ';)' => 'icon_wink.gif',
            ':!:' => 'icon_exclaim.gif',
            ':?:' => 'icon_question.gif',
      );

    if (count($wpsmiliestrans) == 0) {
      return;
    }

    /*
     * NOTE: we sort the smilies in reverse key order. This is to make sure
     * we match the longest possible smilie (:???: vs :?) as the regular
     * expression used below is first-match
     */
    krsort($wpsmiliestrans);

    $wp_smiliessearch = '/(?:\s|^)';

    $subchar = '';
    foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
      $firstchar = substr($smiley, 0, 1);
      $rest = substr($smiley, 1);

      // new subpattern?
      if ($firstchar != $subchar) {
        if ($subchar != '') {
          $wp_smiliessearch .= ')|(?:\s|^)';
        }
        $subchar = $firstchar;
        $wp_smiliessearch .= preg_quote($firstchar, '/') . '(?:';
      } else {
        $wp_smiliessearch .= '|';
      }
      $wp_smiliessearch .= preg_quote($rest, '/');
    }

    $wp_smiliessearch .= ')(?:\s|$)/m';
  }
}