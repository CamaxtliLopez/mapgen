<?php
session_start();
echo $_SESSION['num_rooms'].' ';
echo $_SESSION['len_tunnel'].' ';
echo $_SESSION['draw_grid'].' ';
echo $_SESSION['prune_dead_ends'].' ';
echo $_SESSION['tunnel_direction'].' ';
echo $_SESSION['room_shape'].' ';
echo $_SESSION['color_border'].' ';
echo $_SESSION['color_stone'].' ';
echo $_SESSION['color_floor'].' ';
echo $_SESSION['color_walls'].' ';
echo $_SESSION['color_grid'].' ';
echo $_SESSION['color_text'].' ';
?>
