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
            <li><a href="<?php echo url_for('@homepage') ?>">Ай Фабрик</a></li>
            <li class="divider">&thinsp;/&thinsp;</li>
            <li>Нашим клиентам</li>
            <li class="divider">&thinsp;/&thinsp;</li>
            <li class="active">База знаний</li>
          </ul>
          <!-- END BREADCRUMBS -->
        </section>
      </div>
    </div>
    <div class="row">
      <div class="span12" id="content" data-motopress-type="loop" data-motopress-loop-file="loop/loop-faq.php">
        <dl class="faq-list">
          <dt class="faq-list_h">
            <span class="marker">?</span>Что такое домен?</dt>
            <dd class="faq-list_body">
              <span class="marker">&ndash;</span>
              <p>Доменное имя, в отличие от человеческих имен, уникально. Двух одинаковых имен быть не может.<br />Если Вы делаете серьезный сайт - уделите должное внимание выбору имени вашего сайта.</p>
              <p>Доменные имена различаются как по названию, так и по уровню вложенности:
                <ul>
                  <li>домены первого уровня — RU, COM, РФ и другие</li>
                  <li>домены второго уровня — domain.ru, домен.рф</li>
                  <li>домены третьего уровня — name.domain.ru</li>
                </ul>
                Уровни разделяются между собой точками.
            </dd>
          </dt>
          <dt class="faq-list_h">
            <span class="marker">?</span>Что такое "спам"?</dt>
            <dd class="faq-list_body">
              <span class="marker">&ndash;</span>
              <p>"Спам" — это акроним. Сложносокращенное слово, образованное от "spiced ham". </p>
              <p>В 1937 году американская фирма "Hormel Foods" выпустила колбасный фарш из скопившегося на фабрике "неликвидного" мяса третьей свежести. Малоаппетитный продукт американцы не стали покупать, поэтому "Hormel Foods" развернули масштабную маркетинговую кампанию. Широко разрекламировав свой продукт компания начала поставлять свои консервы в военные ведомства и флот.</p>
              <p>Даже в послевоенной Англии, среди экономического кризиса, спам был основным продуктом питания англичан. Так слово "спам" приобрело значение чего-то отвратительного, но неизбежного.</p>
              <p>Термин "спам" в значении навязчивой электронной рассылки появился в 1993 году. Администратор компьютерной сети Usenet Ричард Депью написал программу, ошибка которой 31 марта 1993 года спровоцировала отправку двух сотен идентичных сообщений в одну из конференций. Его недовольные собеседники быстро нашли подходящее название для навязчивых сообщений — "спам".</p>
            </dd>
          </dt>
        </dl>
      </div>
    </div>
  </div>
</div>
