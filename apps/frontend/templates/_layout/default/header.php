<header class="motopress-wrapper header">
  <div class="container">
    <div class="row">
      <div class="span12" data-motopress-wrapper-file="wrapper/wrapper-header.php" data-motopress-wrapper-type="header" data-motopress-id="522c37bc79e17">
        <div class="row">
          <div class="hidden-phone" data-motopress-type="static" data-motopress-static-file="static/static-search.php">
            <!-- BEGIN SEARCH FORM -->
            <!-- END SEARCH FORM -->
          </div>
          <div class="span4" data-motopress-type="static" data-motopress-static-file="static/static-logo.php">
            <!-- BEGIN LOGO -->
            <div class="logo">
              <a href="<?php echo url_for('@homepage') ?>" class="logo_h logo_h__img">
                &nbsp;<img src="/frontend/theme/images/logo.png" alt="iFabrik" title="Ай Фабрик: Анализируем. Рекомендуем. Создаем.">
              </a>
              <p class="logo_tagline">Правильные решения</p>
            </div>
            <!-- END LOGO -->
          </div>
          <div class="span8" data-motopress-type="static" data-motopress-static-file="static/static-nav.php">
            
            <!-- BEGIN MAIN NAVIGATION -->
            <nav class="nav nav__primary clearfix">
              <ul id="topnav" class="sf-menu">
                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                  <a href="<?php echo url_for('@frontend_service') ?>">Наши решения</a>
                  <!--ul class="sub-menu">
                    <li class="menu-item menu-item-type-post_type menu-item-object-page">
                      <a href="#">Отзывы</a>
                    </li>
                    <li class="menu-item menu-item-type-post_type menu-item-object-page">
                      <a href="#">Новости</a>
                    </li>
                    <li class="menu-item menu-item-type-post_type menu-item-object-page">
                      <a href="#">Задать вопрос</a>
                    </li>
                  </ul-->
                </li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                  <a href="<?php echo url_for('@frontend_client_blog') ?>">Блог</a>
                </li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                  <a href="<?php echo url_for('@frontend_client_faq') ?>">Нашим клиентам</a>
                  <ul class="sub-menu">
                    <li class="menu-item menu-item-type-post_type menu-item-object-page">
                      <a href="<?php echo url_for('@frontend_contacts') ?>">Стать нашим клиентом</a>
                    </li>
                  </ul>
                </li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                  <a href="<?php echo url_for('@frontend_morpho_mladenec') ?>">Вакансии</a>
                </li>
              </ul>
            </nav>
            <!-- END MAIN NAVIGATION -->
          </div>
        </div>
        <div class="row"></div>
      </div>
    </div>
  </div>
</header>