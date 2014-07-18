<div id="mws-navigation">
<?php if ($menu->count()): ?>
  <ul>
  <?php foreach($menu as $section => $sectionMenu): ?>
    <li>
      <a><i class="icon-home"></i> <?php echo $section ?></a>
      <ul>
      <?php foreach($sectionMenu as $listMenu): ?>
        <?php foreach($listMenu as $menu): ?>
          <li><a href="<?php echo url_for($menu['url']) ?>"><?php echo $menu['name'] ?></a></li>
        <?php endforeach ?>
      <?php endforeach ?>
      </ul>
    </li>
  <?php endforeach ?>
  </ul>
<?php endif ?>
</div>