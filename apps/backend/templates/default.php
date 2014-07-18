<!DOCTYPE html>
<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $sf_user->getCulture() ?>"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8" lang="<?php echo $sf_user->getCulture() ?>"><![endif]-->
<!--[if IE 8]><html class="lt-ie9" lang="<?php echo $sf_user->getCulture() ?>"><![endif]-->
<!--[if gt IE 8]><!--><html lang="<?php echo $sf_user->getCulture() ?>"><!--<![endif]-->
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <link rel="shortcut icon" href="/favicon.ico" />
  <link href="/favicon.ico" type="image/png" rel="icon">
</head>
<body>

  <!-- Header -->
  <div id="mws-header" class="clearfix">
    
      <!-- Logo Container -->
      <div id="mws-logo-container">
        
          <!-- Logo Wrapper, images put within this wrapper will always be vertically centered -->
          <div id="mws-logo-wrap">
              <img src="images\mws-logo.png" alt="mws admin">
      </div>
        </div>
        
        <!-- User Tools (notifications, logout, profile, change password) -->
        <div id="mws-user-tools" class="clearfix">
        
          <!-- Notifications -->
          <div id="mws-user-notif" class="mws-dropdown-menu">
              <a href="#" data-toggle="dropdown" class="mws-dropdown-trigger"><i class="icon-exclamation-sign"></i></a>
                
                <!-- Unread notification count -->
                <span class="mws-dropdown-notif">35</span>
                
                <!-- Notifications dropdown -->
                <div class="mws-dropdown-box">
                  <div class="mws-dropdown-content">
                        <ul class="mws-notifications">
                          <li class="read">
                              <a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                          <li class="read">
                              <a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                          <li class="unread">
                              <a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                          <li class="unread">
                              <a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <div class="mws-dropdown-viewall">
                          <a href="#">View All Notifications</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Messages -->
            <div id="mws-user-message" class="mws-dropdown-menu">
              <a href="#" data-toggle="dropdown" class="mws-dropdown-trigger"><i class="icon-envelope"></i></a>
                
                <!-- Unred messages count -->
                <span class="mws-dropdown-notif">35</span>
                
                <!-- Messages dropdown -->
                <div class="mws-dropdown-box">
                  <div class="mws-dropdown-content">
                        <ul class="mws-messages">
                          <li class="read">
                              <a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                          <li class="read">
                              <a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                          <li class="unread">
                              <a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                          <li class="unread">
                              <a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <div class="mws-dropdown-viewall">
                          <a href="#">View All Messages</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Information and functions section -->
            <div id="mws-user-info" class="mws-inset">
            
              <!-- User Photo -->
              <div id="mws-user-photo">
                  <img src="example\profile.jpg" alt="User Photo">
                </div>
                
                <!-- Username and Functions -->
                <div id="mws-user-functions">
                    <div id="mws-username">
                        Hello, John Doe
                    </div>
                    <ul>
                      <li><a href="#">Profile</a></li>
                        <li><a href="#">Change Password</a></li>
                        <li><a href="index.html">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Start Main Wrapper -->
    <div id="mws-wrapper">
    
      <!-- Necessary markup, do not remove -->
    <div id="mws-sidebar-stitch"></div>
    <div id="mws-sidebar-bg"></div>
        
        <!-- Sidebar Wrapper -->
        <div id="mws-sidebar">
        
            <!-- Hidden Nav Collapse Button -->
            <div id="mws-nav-collapse">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
          <!-- Searchbox -->
          <div id="mws-searchbox" class="mws-inset">
              <form action="typography.html">
                  <input type="text" class="mws-search-input" placeholder="Search...">
                    <button type="submit" class="mws-search-submit"><i class="icon-search"></i></button>
                </form>
            </div>
            
          <!-- Main Navigation -->
          <?php include_component('klCmsBackend', 'backendMenu'); ?>
        </div>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
          <!-- Inner Container Start -->
          <div class="container">
            <?php include_partial('global/layout/flash') ?>
            <?php echo $sf_content ?>
          <ul>
            <li>
              <a href="<?php echo url_for('@homepage') ?>">Главная</a>
            </li>
            <li>
              <a href="<?php echo url_for('@backend_product_item_nodes') ?>">Типы содержания</a>
            </li>
            <li>
              <a href="<?php echo url_for('@backend_fxshop_list_nodes') ?>">Словари</a>
            </li>
            <li>
              <a href="<?php echo url_for('@backend_fxshop_filter_nodes') ?>">Фильтры<!--[if IE 7]><!--></a><!--<![endif]-->
              <!--[if lte IE 6]><table><tr><td><![endif]-->
              <ul>
                <li>
                  <a href="<?php echo url_for('@backend_fxshop_filter_nodes') ?>" title="">Список фильтров</a>
                </li>
              </ul>
              <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </li>
          </ul>
          </div>
          <!-- Inner Container End -->
          <!-- Footer -->
          <div id="mws-footer">
            Copyright Your Website 2012. All Rights Reserved.
          </div>
        </div>
        <!-- Main Container End -->
    </div>
      <?php $hlpBroker->js->beginInlineJavascript(yaWebResponse::LOCATION_BODY) ?>
        function initPage() {
          jQuery(document).ready(function() {       
            jQuery('.ask').jConfirmAction({ question: "Вы уверены?", yesAnswer: "Да", cancelAnswer: "Отмена" });
          });
        }
      <?php $hlpBroker->js->endInlineJavascript() ?>
</body>
</html>
