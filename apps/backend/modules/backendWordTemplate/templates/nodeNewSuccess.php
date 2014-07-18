<h2>Шаблоны слов для товаров</h2>

<div class="form">
  <div class="comp-edit-row">
    <label for="<?php echo $form['goods_id']->renderId() ?>"><?php echo $form['goods_id']->renderLabel() ?>:</label>
    <?php echo $form['goods_id']->render() ?>
  </div>

  <table id="rounded-corner">
    <thead>
      <tr>
        <th scope="col" class="rounded-company"></th>
        <th scope="col" class="rounded">Список всех слов</th>
        <th scope="col" class="rounded-q4">Список слов для товара</th>
      </tr>
    </thead>
  <tfoot>
    <tr>
      <td colspan="2" class="rounded-foot-left">
        <input type="text" id="word_new" /> <input type="button" id="word_add" value="Добавить" />
      </td>
      <td class="rounded-foot-right"><input type="button" id="words_save" value="Сохранить" /></td>
    </tr>
  </tfoot>
    <tbody>
      <tr>
        <td colspan="2"><select name="allwords" id="word_template_all"></select></td>
        <td><select name="wordsgoods" id="word_template_goods"></select></td>
      </tr>
    </tbody>
  </table>
</div>

<?php $hlpBroker->js->beginInlineJavascript(yaWebResponse::LOCATION_BODY) ?>
function initFCBKCompleteInit() {
  if ('undefined' !== jWidgetFormDoctrineFBSuggestCalls) {
    jQuery.each(jWidgetFormDoctrineFBSuggestCalls, function(i,f) { f.apply(); });
  }
};

(function(){
  /**
   * Добавление нового слова в таблицу слов.
   */
  jQuery('input[type="button"]#word_add').bind('click', function(event){
    /* Fetch all items from words */
    jQuery.ajax({ type: 'POST', data: { "word": jQuery('#word_new').val() }, dataType: "json", url: '<?php echo url_for('@backend_wordt_new') ?>', timeout: 500, 
      beforeSubmit: function(xhr) {
        /* jQuery('#word_template_all').empty(); */
      },
      success: function(response) {
        if (response.length) {
          /*jQuery('#word_template_all').attr('size', response.length);*/
          jQuery.each(response, function(key, item) {
            var option = jQuery("<option></option>").attr("value", item.value).text(item.caption);
            jQuery('#word_template_all').append(option);
          });
        }
      },
      error: function(error) {
      },        
      complete: function() {
      }
    });
  });

  jQuery('input[type="button"]#words_save').bind('click', function(event){
    var pushdata = { "good": jQuery('#word_template_goods_id option:selected').val(), "words":[] };
    jQuery('#word_template_goods option').each(function(idx, element){
      pushdata.words.push(jQuery(element).val());
    });

    jQuery.ajax({ type: 'POST', data: pushdata, dataType: "json", url: '<?php echo url_for('@backend_wordt_link') ?>', timeout: 500, 
      beforeSubmit: function(xhr) {
        /* jQuery('#word_template_all').empty(); */
      },
      success: function(response) {
        if (response.length) {
          /*jQuery('#word_template_all').attr('size', response.length);*/
          jQuery.each(response, function(key, item) {
            var option = jQuery("<option></option>").attr("value", item.value).text(item.caption);
            jQuery('#word_template_all').append(option);
          });
        }
      },
      error: function(error) {
      },        
      complete: function() {
      }
    });
  });


  /* Инициализация списков */
  jQuery('#word_template_goods_id').bind('DOMSubtreeModified', function(event) {
    if (0 < event.target.innerHTML.length)
    {
      var selected = jQuery(event.target).find('option:selected');
      if (selected.length)
      {
        /* Fetch all items from words */
        jQuery.ajax({ type: 'GET', dataType: "json", url: '<?php echo url_for('@backend_wordt_list') ?>', timeout: 500, 
          beforeSubmit: function(xhr) {
            jQuery('#word_template_all').empty();
          },
          success: function(response) {
            if (response.length) {
              jQuery('#word_template_all').attr('size', response.length);
              jQuery.each(response, function(key, item)
              {
                var option = jQuery("<option></option>").attr("value", item.value).text(item.caption);
                option.bind('click', function(event){
                  jQuery('#word_template_goods').attr('size', (jQuery('#word_template_goods option').length + 1));

                  var optsel = jQuery(event.target).clone();
                  jQuery('#word_template_goods').append(optsel);

                });

                jQuery('#word_template_all').append(option);
              });
            }
          },
          error: function(error) {
          },        
          complete: function() {
          }
        });

        /* Fetch all items from words by object */
        jQuery.ajax({ type: 'GET', dataType: "json", url: '<?php echo url_for('@backend_wordt_list') ?>?good=' + selected.val(), timeout: 500, 
          beforeSubmit: function(xhr) {
            jQuery('#word_template_goods').empty();
          },
          success: function(response) {
            if (response.length) {
              jQuery('#word_template_goods').attr('size', response.length);

              jQuery.each(response, function(key, item) {
                var option = jQuery("<option></option>").attr("value", item.value).text(item.caption);
                jQuery('#word_template_goods').append(option);
              });
            }
          },
          error: function(error) {
          },        
          complete: function() {
          }
        });
      }
    }
  });
})();
<?php $hlpBroker->js->endInlineJavascript() ?>