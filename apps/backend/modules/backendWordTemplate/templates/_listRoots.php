<?php foreach($listRoots as $root): ?>
<li><a href="<?php echo url_for('@backend_goods_node?id=' . $root['id']) ?>"><?php echo $root['title'] ?></a></li>
<?php endforeach ?>