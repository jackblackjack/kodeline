<?php use_javascript('libs/plupload/plupload.full.js') ?>
<?php $formKey = $hlpBroker->jFileAttachable->getAttachmentsFormKey($form); ?>
<?php $arAttachableParams = (isset($useKey) && $useKey) ? array('key' => $formKey) : array(); ?>
<div class="clearfix">
  <div>
    <?php echo $form->renderFormTag(null, array('method' => 'post', 'id' => 'attachments-form-' . $formKey)) ?>
      <?php echo $form->renderHiddenFields() ?>
      <div class="message-container"></div>

      <div class="clearfix">
        <div id="attachments-key-<?php echo $formKey ?>">
          <div class="box__r">
            <div class="attachments-menu">
              <a class="attachments-menu-header" href="#">Прикрепить дополнительнное изображение</a>

              <div id="attachments-accordion-<?php echo $formKey ?>">
                <div>
                  <p>
                    <div class="popup_text box__mb0">
                      <div class="clearfix">
                        <span class="gray-btn gray-btn__small btn-file ib box__w170  box__mr13 box__l">
                          <span class="gray-btn gray-btn__small btn-file ib box__w170  box__mr13 box__l" id="add-file-upload-block">
                            <a href="<?php echo url_for2('j_file_attachable_post', $arAttachableParams) ?>" id="add-file-upload-href"></a>
                            <span class="btn-file-text" id="add-file-upload-button">Кликните <span style="text-decoration: underline">сюда</span> и выберите изображение</span>
                          </span>
                        </span>
                        <span class="box__l label label__small box__pt10 ib" id="add-file-upload-status">Файл не выбран.</span>
                      </div>
                      <span class="label label__small box__pt10 db">JPEG, PNG, GIF. Максимальный размер 8MB</span>
                    </div>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="attachments-list" id="attachments-list-container-<?php echo $formKey ?>">
        <ul id="attachments-list-<?php echo $formKey ?>"></ul>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function() {
  /* Uploader init */
  var context = jQuery('form#attachments-form-<?php echo $formKey ?>');

  if (1 == context.length)
  {
    // Create uploader.
    var pUploader = new plupload.Uploader({
      runtimes : 'gears,html5,flash,silverlight,browserplus',
      container: 'add-file-upload-block',
        browse_button: 'add-file-upload-button',
        max_file_size: '16mb',
          url : context.find('#add-file-upload-href').attr('href'),
          flash_swf_url: '/js/libs/plupload/plupload.flash.swf',
          silverlight_xap_url: '/js/libs/plupload/plupload.silverlight.xap',
          filters : [ {title : "Image files", extensions : "jpg,gif,png,bmp"} ]
      }
    );

    pUploader.init();

    // Callback добавления файла в очередь.
    pUploader.bind('QueueChanged', function(up)
    {
      context.find('#add-file-upload-status').html('<img src="/images/preloader/1601.gif" /> загрузка файла...');
      
      up.start();
      up.refresh();
    });

    // Callback ошибки передачи.
    pUploader.bind('Error', function(up, err) {
      context.find('#add-file-upload-status').html('Ошибка загрузки файла.');
      up.refresh();
    });

    // Callback окончания передачи файла.
    pUploader.bind('FileUploaded', function(up, file, response)
    {
      var data = jQuery.parseJSON(response.response);

      context.find('#add-file-upload-status').html('файл загружен');
    });
  }
});
/* ]]> */
</script>




