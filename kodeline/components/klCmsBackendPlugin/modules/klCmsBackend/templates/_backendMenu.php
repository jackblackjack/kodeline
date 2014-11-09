<div id="mws-navigation">
  <ul>
    <li><a href="<?php echo url_for('@homepage') ?>"><i class="icon-home"></i> Главная</a></li>
    <li>
      <a href="#">
        <i class="icon-delicious"></i> Содержание
      </a>
      <ul>
        <li><a href="<?php echo url_for('@backend_product_item_nodes') ?>">Типы</a></li>
        <li><a href="<?php echo url_for('@backend_fxshop_list_nodes') ?>">Словари</a></li>
      </ul>
    </li>
    <li>
      <a href="<?php echo url_for('@backend_fxshop_filter_nodes') ?>">
        <i class="icon-network"></i> Фильтры
      </a>
      <ul>
        <li><a href="<?php echo url_for('@backend_fxshop_filter_nodes') ?>">Готовые фильтры</a></li>
      </ul>
    </li>

    <?php if ($menu->count()): ?>
      <?php foreach($menu as $section => $sectionMenu): ?>
        <li>
          <a>
            <i class="icon-home"></i> <?php echo $section ?>
          </a>

          <?php if (0 < ($szCount = count($sectionMenu))): ?>
          <ul>
            <?php foreach($sectionMenu as $listMenu): ?>
              <?php foreach($listMenu as $menu): ?>
              <li><a href="<?php echo url_for($menu['url']) ?>"><?php echo $menu['name'] ?></a></li>
              <?php endforeach ?>
            <?php endforeach ?>
          </ul>
          <?php endif ?>
          
        </li>
      <?php endforeach ?>
    <?php endif ?>
  </ul>
</div>
