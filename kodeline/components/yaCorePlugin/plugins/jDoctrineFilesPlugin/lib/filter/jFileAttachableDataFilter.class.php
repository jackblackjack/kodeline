<?php
/**
 * This filter processes to scan attached files in the posted data.
 *
 * @package     jDoctrineFilesPlugin
 * @subpackage  library
 * @category    filter
 * @author      Alexey Chugarev <chugarev@gmail.com>
 * @version     $Id$
 */
class jFileAttachableDataFilter extends sfFilter
{
  /**
   * {@inheritDoc}
   */
  public function execute($filterChain)
  {
    // Filter processing only post request.
    $request = $this->getContext()->getRequest();
   
    //if (! $request->isMethod(sfRequest::HEAD))
    if ($this->isFirstCall() && $request->isMethod(sfRequest::POST))
    {
      // Fetch array about incomplete files by last hour.
      $arUntouchFiles = Doctrine_Core::getTable('jFile')
                            ->createQuery('jf')
                            ->select('jf.id, jf.fname')
                            //->addSelect()->leftJoin('')
                            ->where('jf.fkey IS NOT NULL')
                            ->andWhere('jf.created_at >= ?', date('Y-m-d H:i:s', strtotime('-1 hour')))
                            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
     
      // If exists 
      //untouched files - connect process method.
      if (0 < ($szValues = count($arUntouchFiles)))
      {
        // Try convert posted data to array except decoded data ($_FILES).
        $postData = $request->getContent("php://input");
        $postString = (empty($postData) ? implode(null, $_POST) : $postData);

        // Filtering files into posted data.
        $arPostedFiles = array_filter($arUntouchFiles, function($value) use ($postString) {
          return (false !== strpos($postString, $value['fname']));
        });

        // Define length data of bytes.
        //$szString = mb_strlen($postString, '8bit');
        // Possibles for large poist data
        //$output = passthru("sed s/$search/$replace $oldfilename > $newfilename");
        //exec('grep "new" ext-all-debug.js -b', $result);

        if (count($arPostedFiles))
        {
          // Save list of attached user's files.
          $this->getContext()->getUser()->setAttribute('attachable.posted', $arPostedFiles);

          // Define attachable listener.
          $classListener = sfConfig::get('app_jFileAttachable_listener', 'jFileAttachableListener');

          // Create event for linkage attached files to created objects.
          $this->getContext()->getEventDispatcher()->connect('attachable.autolinkage', array($classListener, 'listenToLinkageObjectEvent'));
        }
      }
    }

    // Execute filter chain.
    $filterChain->execute($filterChain);
  }
}