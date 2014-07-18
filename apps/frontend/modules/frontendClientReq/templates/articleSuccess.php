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
            <li>Нашим клиентам</li>
            <li class="divider">&thinsp;/&thinsp;</li>
            <li class="active">Отдайте ваш сайт нам!</li>
          </ul>
          <!-- END BREADCRUMBS -->
        </section>
      </div>
    </div>

    <div class="row">
      <div class="span10 right" data-motopress-type="loop" data-motopress-loop-file="loop/loop-single.php">
        <article class="post type-post status-publish format-standard hentry category-in-faucibus-orci-luctus-et tag-ipsum-dolor tag-lorem post__holder">
          <div class="post_content">
            <h1>Отдайте ваш сайт нам!</h1>
            <div>
              <p>
                <ul class="big-bullet">
                  <li><h5>Хотите чтобы Ваш сайт или сайт вашей компании активно развивался?</h5></li>
                  <li><h5>Устали постоянно разбираться в проблемах привлечения новых клиентов посредством Интернет-технологий?</h5></li>
                  <li><h5>Хотите расширить свою клиентскую базу и получить новые заказы с вашего сайта?</h5></li>
                  <li><h5>Хотите заполнить сайт контентом, написать статью для сайта, раскрутить сайт в сети Интернет?</h5></li>
                </ul>
              </p>
              <p>
                <!--figure class="featured-thumbnail thumbnail ">
                  <img data-src="200x150.jpg" alt="" />
                </figure-->  
                Компания «Ай Фабрик!» всегда рада взять ведение вашего сайта под свой контроль!
              </p>
              <p>
                Сделаем новый сайт, займемся технической поддержкой вашего сайта, внесем изменения в дизайн, займемся продвижением сайта. Сделаем обратную связь с вашими клиентами в сети Интернет простой и понятной.
                <br />Наша компания займется продвижением сайта в социальных сетях, поможет составить хорошее мнение о вашей компании в сети Интернет.
                Мы подготовим фотографии, подберем статьи, интересные вашей целевой аудитории или найдем авторов для подготовки новых материалов.
              </p>
              <div class="clear"></div>
              <p>
                <!--figure class="featured-thumbnail thumbnail ">
                  <img data-src="200x150.jpg" alt="" />
                </figure-->  
                Наши менеджеры займутся продвижением вашего сайта, проведут технический аудит, подберут семантическое ядро и помогут привлечь новых пользователей на сайт новыми техническими решениями.
                <br />Помогут подобрать домен для сайта и зарегистрировать его. Проведут аналитику сайта, займутся проведением рекламных компаний в сети Интернет.
              </p>
              <div class="clear"></div>
              <p>
                <!--figure class="featured-thumbnail thumbnail ">
                  <img data-src="200x150.jpg" alt="" />
                </figure-->
                Наши авторы займутся ведением групп в социальных сетях, займутся обратной связью на сайте, напишут статью на сайт, сделают рерайт хорошей статьи и выложат новости на сайт.
              </p>
              <div class="clear"></div>
              <p>
                <!--figure class="featured-thumbnail thumbnail ">
                  <img data-src="200x150.jpg" alt="" />
                </figure-->
                Наши программисты разбираются во многих технических решениях, знают много языков программирования и готовы доработать ваш сайт, добавить новые модули на сайт, разработать новый модуль или заняться всей технической поддержкой вашего сайта.
                <br />Доработают модуль поиска на сайте, сделают авторизацию вашего сайта через социальные сети, добавят комментарии на сайт, помогут провести аналитику поведения пользователей на вашем сайте.
                <br />Они делают сайты на различных платформах, таких как Битрикс, Wordpress, MODx, им приходится разбираться в чужом коде и они иногда даже получают от этого кайф.
              </p>
              <p></p>
              <div class="clear"></div>
              <p>
                <!--figure class="featured-thumbnail thumbnail ">
                  <img data-src="200x150.jpg" alt="" />
                </figure-->
                Наши дизайнеры всегда рады придумать новый баннер на сайт, сделать заглушку для вашего сайта. Они знают очень много программ. Photoshop, Corel.
                <br />Знают, как подобрать цвет для сайта, сделать баннер для сайта, нарисовать иконки для сайта, сделать редизайн для сайта. 
                <br />Знают как сделать ваш сайт более динамичным, живым и современным.
              </p>
              <div class="clear"></div>
              <p>Мы находимся в городе Жуковский, это одно из решений чтобы сделать нашу цену ниже. Мы хотим создавать новые сайты, продающие сайты, предлагая качественные услуги по разработке сайтов, продвижению сайтов или дизайна для сайтов за справедливую цену.</p>
              <h5>Отдайте ведение Вашего сайта или сайта вашей компании профессионалам, и мы найдем для вас лучшее решение!</h5>
              
              <a href="<?php echo url_for('@frontend_contacts') ?>">Свяжитесь с нами</a>
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
