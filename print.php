<?php
if(!isset($_SESSION))
    session_start();
echo 'submit='.$_SESSION['submit'].'<br>';
echo 'num_rooms='.$_SESSION['num_rooms'].'<br>';
echo 'len_tunnel='.$_SESSION['len_tunnel'].'<br>';
echo 'draw_grid='.$_SESSION['draw_grid'].'<br>';
echo 'draw_grid='.(((!isset($_SESSION['submit']) && $_SESSION['draw_grid'] != 'off') || ($_SESSION['draw_grid'] === 'on')) ? true : false).'<br>';
echo 'prune_dead_ends='.$_SESSION['prune_dead_ends'].'<br>';
echo 'prune_dead_ends='.(((!isset($_SESSION['submit']) && $_SESSION['prune_dead_ends'] != 'off') || ($_SESSION['prune_dead_ends'] === 'on')) ? true : false).'<br>';
echo 'tunnel_direction='.$_SESSION['tunnel_direction'].'<br>';
echo 'room_shape='.$_SESSION['room_shape'].'<br>';
echo 'color_border='.$_SESSION['color_border'].'<br>';
echo 'color_stone='.$_SESSION['color_stone'].'<br>';
echo 'color_floor='.$_SESSION['color_floor'].'<br>';
echo 'color_walls='.$_SESSION['color_walls'].'<br>';
echo 'color_grid='.$_SESSION['color_grid'].'<br>';
echo 'color_text='.$_SESSION['color_text'].'<br>';
?>
