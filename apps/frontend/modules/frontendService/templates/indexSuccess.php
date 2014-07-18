<?php
isicsBreadcrumbs::getInstance()->setRoot('Ай Фабрик!', url_for('@homepage'));
$arBreadcrumbs = array_reverse(isicsBreadcrumbs::getInstance()->getItems());
$arTitle = array();
foreach($arBreadcrumbs as $breadcrumb) { $arTitle[] = $breadcrumb->getText(); }
include_partial('global/block/metas', array('metas' => array('title' => "Решения компании", "description" => 'Закажите редизайн в Жуковском', 'keywords' => array('заказать поддержку сайта', 'заказать редизайн'))));
?>

<div class="row">
  <div class="span12" data-motopress-wrapper-file="page-fullwidth.php" data-motopress-wrapper-type="content">
    <div class="row">
      <div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-title.php">
        <section class="title-section">
          <h1 class="title-header">Наши решения</h1>
          <!-- BEGIN BREADCRUMBS-->
          <ul class="breadcrumb breadcrumb__t">
            <li><a href="<?php echo url_for('@homepage') ?>">Ай Фабрик</a></li>
            <li class="divider">&thinsp;/&thinsp;</li><li class="active">Решения компании</li>
          </ul>
          <!-- END BREADCRUMBS -->
        </section>
      </div>
    </div>

    <div class="row">
      <div class="span12">
        <ul class="recent-posts four-col unstyled">
          <li class="recent-posts_li post-1986 team type-team status-publish hentry">
            <figure class="thumbnail featured-thumbnail">
              <a href="#" title="Заказать сайт в Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request create site by image']);">
                <img src="/uploads/2013/wc_280x270.jpg" alt="Заказать разработку сайта">
              </a>
            </figure>
            <div class="content_holder">
              <h5>
                <a href="" title="Заказать сайт в компании Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request create site by title']);">
                  Новые решения.
                </a>
              </h5>
              <div class="excerpt justify-text">
                Создание новых простых и сложных технических решений для вашей компании. Реализация новых возможностей для ваших клиентов. Создание нового сайта и улучшение существующих решений.
              </div>
            </div>
            <div class="clear"></div>
          </li><li class="recent-posts_li post-1985 team type-team status-publish hentry">
            <figure class="thumbnail featured-thumbnail">
              <a href="#" title="Заказать поддержку сайта в Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request support site by image']);">
                <img src="/uploads/2013/ws_280x270.jpg" alt="Заказать поддержку сайта">
              </a>
            </figure>
            <div class="content_holder">
              <h5><a href="#" title="Заказать поддержку сайта в компании Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request support site by title']);">
                Увеличение продаж.
              </a></h5>
              <div class="excerpt justify-text">
                Разработка рекламной стратегии, проведение технического анализа и аудита вашего сайта, проведение анализа рынка. Реализация возможностей роста вашей компании.
              </div>
            </div>
            <div class="clear"></div>
          </li><li class="recent-posts_li post-303 team type-team status-publish hentry">
            <figure class="thumbnail featured-thumbnail">
              <a href="#" title="Заказать дизайн сайта в Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request design site by image']);">
                <img src="/uploads/2013/wd_280x270.jpg">
              </a>
            </figure>
            <div class="content_holder">
              <h5>
                <a href="#" title="Заказать дизайн сайта в компании Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request design site by title']);">
                  Привлечение аудитории.
                </a>
              </h5>
              <div class="excerpt justify-text">
                Создание промо материалов в сети Интернет и реализация рекламных кампаний. Выявление целевой аудитории и представление информации о ваших продуктах и услугах потенциальным клиентам.
              </div>
            </div>
            <div class="clear"></div>
          </li><li class="recent-posts_li post-143 team type-team status-publish hentry">
            <figure class="thumbnail featured-thumbnail">
              <a href="#" title="Заказать продвижение сайта в Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request promote site by image']);">
                <img src="/uploads/2013/wa_280x270.jpg" alt="Заказать продвижение сайта">
              </a>
            </figure>
            <div class="content_holder">
              <h5>
                <a href="#" title="Заказать продвижение сайта в компании Ай Фабрик" onClick="_gaq.push(['_trackEvent', 'Select', 'Activities', 'Request promote site by title']);">
                  Поддержка вашего роста.
                </a>
              </h5>
              <div class="excerpt justify-text">
                Проведение интеграции сайта с мировыми сервисами для увеличения качества предоставляемых услуг. Улучшение качества обратной связи с вашими клиентами.
              </div>
            </div>
            <div class="clear"></div>
          </li>
        </ul>
      </div>
    </div>

    <div class="row">
      <div class="span12">
        <h2>Наши принципы</h2>
        <div class="row">
          <div class="span4">
            <span class="dropcap">01</span>
            <h4>Всегда<br />на связи с Вами</h4>
            <div class="clear"></div>
            <p class="justify-text">
              Мы всегда на связи! И всегда готовы предложить решение для вас.
              Проконсультируем и предложим качественные решения для привлечения клиентов вашей компании. 
              Поможем вам принять лучшее решение из лучших.</p>
            <div class="spacer"></div>
          </div>

          <div class="span4">
            <span class="dropcap">02</span>
            <h4>Диалог<br />с вашим клиентом</h4>
            <div class="clear"></div>
            <p class="justify-text">
              Доступность и лаконичность - главные принципы понимания клиентом ваших услуг. 
              Мы поможем убрать недопонимание, подчеркнуть особенности вашей компании для необходимой аудитории.
              Реализуем необходимые, и ожидаемые вашими клиентами, изменения, дающие возможность роста вашей компании.
            </p>
            <div class="spacer"></div>
          </div>

          <div class="span4">
            <span class="dropcap">03</span>
            <h4>Качество выполнения</h4>
            <div class="clear"></div>
            <p class="justify-text">
              Мы работаем для вас, а значит на лучший результат. 
              Ничто не должно быть помехой. 
              Мы работаем с использованием лучших технических решений, и помогаем быстро развиваться вашей компании.</p>
            <div class="spacer"></div>
          </div>
        </div>
      </div>
    </div>

    <div id="content" class="row">
      <div class="span12" data-motopress-type="loop" data-motopress-loop-file="loop/loop-page.php">
        <div id="post-1797" class="post-1797 page type-page status-publish hentry page">
          <div class="row">
            <div class="span8">
              <div class="banner-wrap">
                <figure class="featured-thumbnail"><a href="#" title="Заказать разработку сайта">
                  <img src="/uploads/2013/08/page1_pic1.jpg" title="Заказать разработку сайта" alt=""></a>
                </figure>
                <div class="content_holder">
                  <div>
                    <h2>+7 (985) 381 4091</h2>
                    <p>
                      <ul>
                      <li><strong>Хотите заказать сайт?</strong><br />&mdash; Вы получите оптимальное решение для вас</li>
                      <li><strong>Хотите внести изменения на сайт, сделать редизайн?</strong><br />&mdash; Наша компания предложит решение с учетом ожиданий ваших клиентов.</li>
                      <li><strong>Хотите заказать техничесую поддержку сайта?</strong><br />&mdash; Обратиться к нам будет правильным решением.</li>
                      <li><strong>Хотите увеличить продажи и привлечь новых посетителей?</strong><br />&mdash; Наша компания предоставит вашим клиентам всю интересующую их информацию.</li>
                      <li><strong>Думаете о расширении спектра услуг?</strong><br />&mdash; Обеспечим техническую поддержку.</li>
                      </ul>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>