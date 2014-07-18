<?php
isicsBreadcrumbs::getInstance()->setRoot('Ай Фабрик', url_for('@homepage'));
$arBreadcrumbs = array_reverse(isicsBreadcrumbs::getInstance()->getItems());
$arTitle = array();
foreach($arBreadcrumbs as $breadcrumb) { $arTitle[] = $breadcrumb->getText(); }
//include_partial('global/block/metas', array('metas' => array('title' => $page['title'], 'description' => $page['description'], 'keywords' => $page['keywords'])));
include_partial('global/block/metas', array('metas' => array('title' => "Стать клиентом")));
?>
<div id="content" class="row">
  <div class="span4">
        <section class="title-section">
          <h1 class="title-header">Наши контакты</h1>
          <!-- BEGIN BREADCRUMBS-->
          <ul class="breadcrumb breadcrumb__t">
            <li><a href="<?php echo url_for('@homepage') ?>">Ай Фабрик</a></li>
        <li class="divider">&thinsp;/&thinsp;</li><li class="active">Стать нашим клиентом</li>
          </ul>
          <!-- END BREADCRUMBS -->
        </section>
      </div>
  <div class="span8" style="float: right">
    <p>
      <strong>Стратегия компании "Ай Фабрик"</strong> основана на том, что технические решения вашей компании, 
      в частности её присутствия в сети Интернет, необходимо постоянно улучшать, внося <strong>изменения  в  дизайн</strong>, 
      добавляя <strong>новую  функциональность</strong>, расширяясь и предоставляя вашим клиентам новые и <strong>новые возможности</strong>.
    </p>

    <p>
      Только такими действиями можно добиться роста популярности к вашим услугам и продукции, <strong>удержать существующих клиентов</strong> и найти новых.
    </p>

    <p>
      Некоторые компании не могут их себе позволить в силу причин различного характера.
      Такими причинами могут быть ограниченность экономических возможностей расширения отдела, отвечающего за разработку или <strong>продвижение присутствия компании в Интернете</strong>.
      Также в эти причины может входить <strong>постоянное улучшение сайта</strong>, <strong>исправление мелких багов</strong>, <strong>поддержка посетителей сайта</strong>, которые, со временем, нагружают внутренний отдел компаний, оставляя мало ресурсов для <u>качественного рывка</u>.
    </p>

    <p>
      Многие компании даже не задумываются как они могли бы улучшить предоставляемый сервис, разделив зоны ответственности между внутренними задачами, и задачами, требующими совместных усилий с компаниями со стороны.
    </p>

    <p>
      Наша компания призвана быть качественным слоем между IT-отделом и отделом продвижения компании, предлагая и внедряя новые решения поддержки вашего основного бизнеса компании, предоставляя вам возможность для <u>быстрого качественного роста</u>. 
      Мы также предоставляем <strong>услуги поддержки существующих проектов</strong>, пока вашей компании надо сосредоточиться на нововведениях, <strong>подготовить новые услуги</strong>, освобождая ваши ресурсы от бесконечной рутины.
    </p>

    <h5>Свяжитесь с нами <br />чтобы принять правильное решение</h5>
    </div>

  <div class="span4" style="float: left">
    <div class="page type-page status-publish hentry page" itemscope itemtype="http://data-vocabulary.org/Organization">
      <strong itemprop="name">Компания "Ай Фабрик"</strong>
      <h2>Наш телефон</h2>
      <h4><i class="icon-phone" itemprop="tel"></i>&nbsp;+7 (985) 381 40-91</h4>
      <!--h2>Наш адрес</h2>
              <address>
                <strong>
                  Компания "Ай Фабрик"<br>
                </strong>
                <br />
                Телефон: +7 985 381 4091<br>
                E-mail: <a href="mailto:input@ifabrik.ru">input@ifabrik.ru</a>
                <br />
              </address>
      <h5>Приходите к нам для принятия правильного решения.</h5-->
            </div>
            </div>
  </div>
</div>