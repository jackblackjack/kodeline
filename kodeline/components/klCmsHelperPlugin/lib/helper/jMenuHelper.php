<?php
/**
 * apMenuHelper.
 *
 * @package    aip
 * @subpackage plugin
 * @author     freeman
 * @version    
 */
function jMenuHelper(&$menu_in, $options = array(), &$menu_out = array())
{
  $empty_level = false;
  
  if(empty($menu_out))
  { 
    $menu_out[] = '<div '.(isset($options['id'])?'id="'.$options['id'].'"':'').' class="yuimenubar yuimenubarnav">';
    $menu_out[] = '<div class="bd">';
    $menu_out[] = '<ul class="first-of-type">';
    
    $empty_level = true;
  }
  
  if(is_array($menu_in))
  {
    $i = 0;
    
    foreach($menu_in as $item)
    {
      $class_li = $empty_level?'yuimenubaritem'.(!$i?' first-of-type':''):'yuimenuitem';
      $class_a  = $empty_level?'yuimenubaritemlabel':'yuimenuitemlabel';
      
      $menu_out[] = '<li class="'.$class_li.'">';
      
      if(empty($item['url']))
      {
        $menu_out[] = '<a href="#" class="'.$class_a.'">'.$item['name'].'</a>'; 
      }
      else
      {
       $menu_out[] = link_to($item['name'], $item['url'], array('class' => $class_a)); 
      }
      
      if(!empty($item['submenu']) && is_array($item['submenu']))
      {
        $menu_out[] = '<div class="yuimenu">';
        $menu_out[] = '<div class="bd">';
        $menu_out[] = '<ul>';
        $menu_out = yui_menu($item['submenu'], $options, $menu_out);
        $menu_out[] = '</ul>';
        $menu_out[] = '</div>';
        $menu_out[] = '</div>';
      }
      
      $menu_out[] = '</li>';

      $i++;
    }
  }
  
  if($empty_level)
  {
    $menu_out[] = '</ul>';
    $menu_out[] = '</div>';
    $menu_out[] = '</div>';
  }
  
  return $menu_out;
}

/*
<ul id="nav" class="dropdown dropdown-horizontal">
	<li><a href="./">Home</a></li>
	<li class="dir">About Us
		<ul>
			<li><a href="./">History</a></li>
			<li><a href="./">Our Vision</a></li>
			<li class="dir"><a href="./">The Team</a>
				<ul>
					<li><a href="./">Brigita</a></li>
					<li><a href="./">John</a></li>
					<li><a href="./">Michael</a></li>
					<li><a href="./">Peter</a></li>
					<li><a href="./">Sarah</a></li>
				</ul>
			</li>
			<li><a href="./">Clients</a></li>
			<li><a href="./">Testimonials</a></li>
			<li><a href="./">Press</a></li>
			<li><a href="./">FAQs</a></li>
		</ul>
	</li>
	<li class="dir">Services
		<ul>
*/
/**
 * 
 */
function css_menu(&$menu_in, $options = array(), &$menu_out = array())
{
  $empty_level = false;

  if (empty($menu_out))
  {
    $menu_out[] = '<ul '.(isset($options['id'])?'id="'.$options['id'].'"':'').' class="cssmenubar dropdown dropdown-horizontal">';
    $empty_level = true;
  }

  if (is_array($menu_in))
  {
    $i = 0;

    foreach($menu_in as $item)
    {
      $branchClass = (! empty($item['submenu']) && is_array($item['submenu'])) ? 'dir' : null;
      $menu_out[] = '<li' . ($branchClass ? ' class="' . $branchClass . '"' : '') . '>';

      if (empty($item['url']))
      {
        $menu_out[] = $item['name'];
      }
      else
      {
       $menu_out[] = link_to($item['name'], $item['url']);
      }

      if (! empty($item['submenu']) && is_array($item['submenu']))
      {
        $menu_out[] = '<ul>';
        $menu_out = css_menu($item['submenu'], $options, $menu_out);
        $menu_out[] = '</ul>';
      }

      $menu_out[] = '</li>';
      $i++;
    }
  }

  if($empty_level)
  {
    $menu_out[] = '</ul>';
  }

  return $menu_out;
}
