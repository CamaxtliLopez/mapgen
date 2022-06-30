<?php

// Generate and render map data.
class Map {

    private $cli = false;
    private $num_rooms = 0;
    private $rooms = [];
    private $tunnels = [];
    private $min_x = 0;
    private $max_x = 0;
    private $min_y = 0;
    private $max_y = 0;

    function bounds($x, $lo, $hi) {
        if ($x < $lo)
            return $lo;
        if ($x > $hi)
            return $hi;
        return $x;
    }

    // Validate inputs and generate data.
    function __construct($cli, $num_rooms, $len_tunnel, $draw_grid, $prune_dead_ends, $tunnel_direction, $room_shape, $colors) {
        $this->cli = $cli;
        $this->num_rooms = $this->bounds($num_rooms, 1, 100);
        $this->len_tunnel = $this->bounds($len_tunnel, 1, 5);
        $this->draw_grid = $draw_grid;
        $this->prune_dead_ends = $prune_dead_ends;
        $this->tunnel_direction = $tunnel_direction;
        $this->room_shape = $room_shape;
        $this->colors = $colors;
    }

    // Generate the number of exits for a room.
    function num_exits() {
        $x = rand(1, 8);
        if ($x >= 1 && $x <= 2)
            return 1;
        elseif ($x >= 3 && $x <= 4)
            return 2;
        elseif ($x >= 5 && $x <= 7)
            return 3;
        else
            return 4;
    }

    // Return the end point of a tunnel given start point and direction.
    function direction_compass($s) {
        $d = rand(1, 12);
        $l = rand(1, $this->len_tunnel);
        if ($d >= 1 && $d <= 2)         // North
            return [$s[0], $s[1]+$l];
        elseif ($d == 3)                // NorthEast
            return [$s[0]+$l, $s[1]+$l];
        elseif ($d >= 4 && $d <= 5)     // East
            return [$s[0]+$l, $s[1]];
        elseif ($d == 6)                // SouthEast
            return [$s[0]+$l, $s[1]-$l];
        elseif ($d >= 7 && $d <= 8)     // South
            return [$s[0], $s[1]-$l];
        elseif ($d == 9)                // SouthWest
            return [$s[0]-$l, $s[1]-$l];
        elseif ($d >= 10 && $d <= 11)   // West
            return [$s[0]-$l, $s[1]];
        else                            // NorthWest
            return [$s[0]-$l, $s[1]+$l];
    }

    function direction_random($s) {
        $l = rand(1, $this->len_tunnel);
        $x=$s[0]+rand(-$l, $l);
        $y=$s[1]+rand(-$l, $l);
        while ($x==$s[0] && $y==$s[1]) {
            $x=$s[0]+rand(-$l, $l);
            $y=$s[1]+rand(-$l, $l);
        }
        return [$x, $y];
    }

    function direction($s) {
        if ($this->tunnel_direction === 'random') {
            return $this->direction_random($s);
        } else {
            return $this->direction_compass($s);
        }
    }

    function edges($x) {
        $this->min_x = min($x[0], $this->min_x);
        $this->max_x = max($x[0], $this->max_x);
        $this->min_y = min($x[1], $this->min_y);
        $this->max_y = max($x[1], $this->max_y);
    }

    // Generate map data.
    // We use simple grid coordinates with a unit of one
    // and the first room placed at the origin (x=0 y=0).
    function generate() {
        array_push($this->rooms, [0,0]);
        foreach($this->rooms as &$center) {
            $exits = [];
            for ($i = 0; $i < $this->num_exits(); $i++) {
                $dest = $this->direction($center);
                array_push($exits, $dest);
            }
            foreach ($exits as $dest) {
                $tunnel = [$center, $dest];
                if (!$this->prune_dead_ends) {
                    if (!in_array($tunnel, $this->tunnels)) {
                        array_push($this->tunnels, $tunnel);
                        $this->edges($tunnel[0]);
                        $this->edges($tunnel[1]);
                    }
                }
                if (in_array($dest, $this->rooms)) {
                    if ($this->prune_dead_ends) {
                        if (!in_array($tunnel, $this->tunnels))
                            array_push($this->tunnels, $tunnel);
                    }
                } else {
                    if (count($this->rooms) < $this->num_rooms) {
                        array_push($this->rooms, $dest);
                        $this->edges($dest);
                        if ($this->prune_dead_ends) {
                            if (!in_array($tunnel, $this->tunnels))
                                array_push($this->tunnels, $tunnel);
                        }
                    }
                }
            }
        }
    }

    // Determine whether a given room contains treasure.
    function get_treasure($n) {
        $x = rand(1,6);
        return $x < $n;
    }
    
    // Determine a room's contents.
    function get_contents() {
        $x = rand(1,8);
        if ($x >= 1 && $x <= 2)
            return ['Creature', $this->get_treasure(4)];
        elseif ($x == 3)
            return ['Hazard', $this->get_treasure(3)];
        elseif ($x == 4)
            return ['Enigma', $this->get_treasure(3)];
        elseif ($x <= 5 && $x <= 6)
            return ['Distractor', $this->get_treasure(2)];
        else
            return ['Empty', $this->get_treasure(2)];
    }

    // Convert a line into a polygon with thickness $t * 2.
    function line_to_polygon($x1, $y1, $x2, $y2, $t) {
        $L = sqrt(($x1-$x2)*($x1-$x2)+($y1-$y2)*($y1-$y2));
        $x1p = $x1 - $t * ($y2-$y1) / $L;
        $x2p = $x2 - $t * ($y2-$y1) / $L;
        $y1p = $y1 - $t * ($x1-$x2) / $L;
        $y2p = $y2 - $t * ($x1-$x2) / $L;
        $x1q = $x1 + $t * ($y2-$y1) / $L;
        $x2q = $x2 + $t * ($y2-$y1) / $L;
        $y1q = $y1 + $t * ($x1-$x2) / $L;
        $y2q = $y2 + $t * ($x1-$x2) / $L;
        return [$x1p, $y1p, $x1q, $y1q, $x2q, $y2q, $x2p, $y2p];
    }

    function get_color($im, $hex) {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return imagecolorallocate($im, $r, $g, $b);
    }
    
    // Render the map data.
    // The grid with unit size 1 must be scaled up to the desired resolution.
    function render() {
        $unit = 180;
        $border = 10;
        $room_size = 50;
        $tunnel_size = 6;
        $wall_thickness = 4;
        // Scale up the tunnels.
        $tunnels = [];
        foreach ($this->tunnels as $tunnel) {
            $x1 = ($tunnel[0][0] - $this->min_x + 0.5) * $unit + $border;
            $y1 = ($tunnel[0][1] - $this->min_y + 0.5) * $unit + $border;
            $x2 = ($tunnel[1][0] - $this->min_x + 0.5) * $unit + $border;
            $y2 = ($tunnel[1][1] - $this->min_y + 0.5) * $unit + $border;
            array_push($tunnels, [$x1, $y1, $x2, $y2]);
        }
        // Scale up the rooms.
        $rooms = [];
        foreach ($this->rooms as $room) {
            $x = ($room[0] - $this->min_x + 0.5) * $unit + $border;
            $y = ($room[1] - $this->min_y + 0.5) * $unit + $border;
            array_push($rooms, [$x, $y]);
        }
        // Scale up the map.
        $w = ($this->max_x - $this->min_x + 1) * $unit + $border * 2;
        $h = ($this->max_y - $this->min_y + 1) * $unit + $border * 2;
        // Render the map.
        $im = imagecreatetruecolor($w, $h);
        $color_border = $this->get_color($im, $this->colors['border']);
        $color_stone = $this->get_color($im, $this->colors['stone']);
        $color_floor = $this->get_color($im, $this->colors['floor']);
        $color_walls = $this->get_color($im, $this->colors['walls']);
        $color_grid = $this->get_color($im, $this->colors['grid']);
        $color_text = $this->get_color($im, $this->colors['text']);

        // Draw the border.
        imagefilledrectangle($im, 0, 0, $w, $h, $color_border);
        // Draw the stone.
        imagefilledrectangle($im, $border, $border, $w-$border, $h-$border, $color_stone);
        // Draw the tunnel walls.
        foreach ($tunnels as $t) {
            $values = $this->line_to_polygon($t[0], $t[1], $t[2], $t[3], $tunnel_size + $wall_thickness * 2);
            imagefilledpolygon($im, $values, 4, $color_walls);
        }
        // Draw the room walls.
        foreach ($rooms as $r) {
            $a = $room_size + $wall_thickness;
            if ($this->room_shape === 'square')
                imagefilledrectangle($im, $r[0]-$a, $r[1]-$a, $r[0]+$a, $r[1]+$a, $color_walls);
            else
                imagefilledellipse($im, $r[0], $r[1], $a * 2, $a* 2, $color_walls);
        }
        // Draw the tunnel floors.
        foreach ($tunnels as $t) {
            $values = $this->line_to_polygon($t[0], $t[1], $t[2], $t[3], $tunnel_size);
            imagefilledpolygon($im, $values, 4, $color_floor);
        }
        // Draw the room floors and labels.
        for ($i = 0; $i < count($rooms); $i++) {
            $r = $rooms[$i];
            $a = $room_size - $wall_thickness;
            if ($this->room_shape === 'square')
                imagefilledrectangle($im, $r[0]-$a, $r[1]-$a, $r[0]+$a, $r[1]+$a, $color_floor);
            else
                imagefilledellipse($im, $r[0], $r[1], $a * 2, $a * 2, $color_floor);
            $contents = $this->get_contents();
            $font=realpath('OpenSans-Regular.ttf');
            imagettftext($im, 12, 0, $r[0]-45, $r[1]+5, $color_text, $font, ($i+1).' '.$contents[0]);
            if ($contents[1])
                imagettftext($im, 10, 0, $r[0]-27, $r[1]+25, $color_text, $font, 'Treasure');
        }
        // Draw the grid.
        if ($this->draw_grid) {
            $grid=20;
            for ($x=$border+$grid; $x<$w-$border; $x+=$grid)
                imageline($im, $x, $border, $x, $h-$border, $color_grid);
            for ($y=$border+$grid; $y<$h-$border; $y+=$grid)
                imageline($im, $border, $y, $w-$border, $y, $color_grid);
        }
        // Output the map.
        if ($this->cli) {
            imagepng($im, "map.png");
        } else {
            imagepng($im);
            imagedestroy($im);
        }
    }
}

if (php_sapi_name() == "cli") {
    $cli = true;
    $num_rooms = intval($argv[1]);
    $len_tunnel = intval($argv[2]);
    $draw_grid = $argv[3];
    $prune_dead_ends = $argv[4];
    $tunnel_direction = $argv[5];
    $room_shape = $argv[6];
    $color_border = $argv[7];
    $color_stone = $argv[8];
    $color_floor = $argv[9];
    $color_walls = $argv[10];
    $color_grid = $argv[11];
    $color_text = $argv[12];
} else {
    $cli = false;
    session_start();
    $num_rooms = intval($_SESSION['num_rooms'] ?? 5);
    $len_tunnel = intval($_SESSION['len_tunnel'] ?? 1);
    $draw_grid = ((!isset($_SESSION['submit']) && $_SESSION['draw_grid'] != 'off') || ($_SESSION['draw_grid'] === 'on')) ? true : false;
    $prune_dead_ends = ((!isset($_SESSION['submit']) && $_SESSION['prune_dead_ends'] != 'off') || ($_SESSION['prune_dead_ends'] === 'on')) ? true : false;
    $tunnel_direction = $_SESSION['tunnel_direction'] ?? 'compass';
    $room_shape = $_SESSION['room_shape'] ?? 'square';
    $color_border = $_SESSION['color_border'] ?? '#6699FF';
    $color_stone = $_SESSION['color_stone'] ?? '#808080';
    $color_floor = $_SESSION['color_floor'] ?? '#FFFFCC';
    $color_walls = $_SESSION['color_walls'] ?? '#6699FF';
    $color_grid = $_SESSION['color_grid'] ?? '#000000';
    $color_text = $_SESSION['color_text'] ?? '#000000';
}

$colors = [
    'border' => $color_border,
    'stone' => $color_stone,
    'floor' => $color_floor,
    'walls' => $color_walls,
    'grid' => $color_grid,
    'text' => $color_text,
];

$map = new Map($cli, $num_rooms, $len_tunnel, $draw_grid, $prune_dead_ends, $tunnel_direction, $room_shape, $colors);
$map->generate();
//var_dump($map);
$map->render();

?>
