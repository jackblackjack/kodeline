<?php
isicsBreadcrumbs::getInstance()->setRoot('Ай Фабрик!', url_for('@homepage'));
$arBreadcrumbs = array_reverse(isicsBreadcrumbs::getInstance()->getItems());
$arTitle = array();
foreach($arBreadcrumbs as $breadcrumb) { $arTitle[] = $breadcrumb->getText(); }
//include_partial('global/block/metas', array('metas' => array('title' => $page['title'], 'description' => $page['description'], 'keywords' => $page['keywords'])));
include_partial('global/block/metas', array('metas' => array('title' => "Нашим клиентам")));
?>
<div class="row">
  <div class="span12" data-motopress-wrapper-file="page-faq.php" data-motopress-wrapper-type="content">
    <div class="row">
      <div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-title.php">
        <section class="title-section">
          <h1 class="title-header">Нашим клиентам</h1>
          <!-- BEGIN BREADCRUMBS-->
          <ul class="breadcrumb breadcrumb__t">
            <li><a href="<?php echo url_for('@homepage') ?>">Ай Фабрик!</a></li>
            <li class="divider">&thinsp;/&thinsp;</li>
            <li class="active">Нашим клиентам</li>
          </ul>
          <!-- END BREADCRUMBS -->
        </section>
      </div>
    </div>

    <div class="row">
      <div class="span10 right" data-motopress-type="loop" data-motopress-loop-file="loop/loop-single.php">
        <article class="post type-post status-publish format-standard hentry category-in-faucibus-orci-luctus-et tag-ipsum-dolor tag-lorem post__holder">
          <div class="post_content">
            <h1>Интернет-решения для ваших клиентов</h1>
            <div>
              <p>
                <h3>Компания «Ай Фабрик» решает следующие задачи:</h3>
                <ul class="big-bullet">
                  <li><h5>увеличение количества клиентов и посетителей вашего сайта</h5></li>
                  <li><h5>увеличение количества продаж вашего сайта</h5></li>
                  <li><h5>уменьшения времени заказа с сайта</h5></li>
                  <li><h5>привлечение аудитории для знакомства с вашей продукцией или услугами</h5></li>
                  <li><h5>разработка рекламной стратегии присутствия в сети Интернет</h5></li>
                  <li><h5>создание новых сайтов и поддержка роста вашего сайта</h5></li>
                  <li><h5>создание промо-сайтов и поддержка оффлайновой рекламной кампании</h5></li>
                  <li><h5>разработка простых и сложных проектов для вашей компании</h5></li>
                </ul>
              </p>
              <p>
                Правильные решения для вашего сайта, реализованные нашей компанией, дадут хороший результат и помогут Вам и вашей компании заявить о себе, расширить спектр предоставляемых услуг, увеличить продажи, представить ваш продукт или услуги необходимой аудитории.
              </p>                
              <p>
                Наша компания поможет выявить целевую аудиторию, провести анализ продвижения вашего сайта, провести технический аудит сайта, повысить удобство пользования вашим сайтом, расширить возможности вашего IT-отдела.
              </p>                
              <p>
                Совместно с нами ваша компания приобретет новых клиентов, утвердит своё положение на рынке, получит возможность быстрого расширения своёго присутствия в сети Интернет.
              </p>

              <p>
                Наша компания предлагает услуги поддержки, создания присутствия и продвижения бизнеса в сети Интернет, что включает в себя:
                <ul>
                  <li>продвижение ваших услуг и продукции в социальных сетях;</li>
                  <li>разработку и подготовку материалов к публикации;</li>
                  <li>регулярное наполнение сайта контентом;</li>
                  <li>разработку рекламных страниц, роликов, создание баннеров;</li>
                  <li>внесение изменений в дизайн, переоформление вашего сайта;</li>
                  <li>создания приложений для iOS и Andriod;</li>
                  <li>интеграцию вашего сайта с мировыми сервисами для улучшения качества предоставляемого сервиса;</li>
                </ul>
              </p>
             
              <a href="<?php echo url_for('@frontend_contacts') ?>">Ваше правильное решение – это позвонить нам.</a>
              <div class="clear"></div>
            </div>
          </div>
        </article>
        <!-- .share-buttons -->
        <!-- Facebook Like Button -->
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'facebook-jssdk'));
        </script>

        <!-- Google+ Button -->
        <script type="text/javascript">
          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>
        <!--ul class="share-buttons unstyled clearfix">
          <li class="twitter">
            <a href="http://twitter.com/share?url=http://livedemo00.template-help.com/wordpress_46026/in-faucibus-orci-luctus-et/phasellus-fringilla/&amp;text= - http://livedemo00.template-help.com/wordpress_46026/in-faucibus-orci-luctus-et/phasellus-fringilla/" class="twitter-share-button" data-count="horizontal">Tweet this article</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
          </li>
          <li class="facebook">
            <div id="fb-root"></div>
            <div class="fb-like" data-href="http://livedemo00.template-help.com/wordpress_46026/in-faucibus-orci-luctus-et/phasellus-fringilla/" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="arial"></div>
          </li>
          <li class="google">
            <div class="g-plusone" data-size="medium" data-href="http://livedemo00.template-help.com/wordpress_46026/in-faucibus-orci-luctus-et/phasellus-fringilla/"></div>
          </li>
          <li class="pinterest">
            <a href="javascript:void((function(){var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)})());"><img src='http://assets.pinterest.com/images/PinExt.png' alt=""></a>
          </li>
        </ul-->
      </div>
    </div>
  </div>
</div>
