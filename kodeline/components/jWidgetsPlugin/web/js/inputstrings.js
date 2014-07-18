"use strict";

/**
 * Модуль управления текстовыми полями
 * для работы с виджетом jWidgetFormInputStrings.
 */
var inputStringsJsHelper = (function() {
  /**
   * Назначает обработчик редактирования текста для textarea.
   */
  var binder = function(node)
  {
    if ('INPUT' == node[0].tagName) { 
      node.bind('input paste propertychange', inputsWrapper);
    }
  };

  /**
   * Эскейпит имена полей.
   */
  var escapeName = function(string) { 
    return new String(string).replace(/(\[|\])/g,'\\$1');
  }

  /**
   * Обработчик количества полей ввода.
   */
  var inputsWrapper = function(event) {
    var emptyFields = [];

    /* Определение количества полей с путыми значениями */
    jQuery('[name=' + escapeName(jQuery(this).attr('name')) + ']').each(function(){ 
      if (0 == jQuery(this).val().length) {
        emptyFields.push(jQuery(this));
      }
    });

    /* Удаление лишних полей */
    if (1 < emptyFields.length) {
      do { emptyFields.pop().remove(); } while(1 != emptyFields.length);
    }

    /* Добавление новых полей */
    if (! emptyFields.length) {
      var input = jQuery(this).clone(true).val('');
      jQuery(this).after(input);
    }
  };

  return {
    init: function(context) {
      if ('undefined' !== typeof(context)) {
        var fldName = jQuery('#' + context).attr('name');
        jQuery('[name=' + escapeName(fldName) + ']').each(function(){ 
          binder($(this));
        });
      };
    }
  }
}).call(inputStringsJsHelper);
