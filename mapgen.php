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

    // Validate inputs and generate data.
    function __construct($cli, $num_rooms) {

        if ($num_rooms < 2)
            $num_rooms = 2;
        if ($num_rooms > 100)
            $num_rooms = 100;
        $this->num_rooms = $num_rooms;
        $this->cli = $cli;
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
    function direction($s) {
        $x = rand(1,12);
        if ($x >= 1 && $x <= 2)         // North
            return [$s[0], $s[1]+1];
        elseif ($x == 3)                // NorthEast
            return [$s[0]+1, $s[1]+1];
        elseif ($x >= 4 && $x <= 5)     // East
            return [$s[0]+1, $s[1]];
        elseif ($x == 6)                // SouthEast
            return [$s[0]+1, $s[1]-1];
        elseif ($x >= 7 && $x <= 8)     // South
            return [$s[0], $s[1]-1];
        elseif ($x == 9)                // SouthWest
            return [$s[0]-1, $s[1]-1];
        elseif ($x >= 10 && $x <= 11)   // West
            return [$s[0]-1, $s[1]];
        else                            // NorthWest
            return [$s[0]-1, $s[1]+1];
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
                if (in_array($dest, $this->rooms)) {
                    if (!in_array($tunnel, $this->tunnels))
                        array_push($this->tunnels, $tunnel);
                } else {
                    if (count($this->rooms) < $this->num_rooms) {
                        array_push($this->rooms, $dest);
                        $this->min_x = min($dest[0], $this->min_x);
                        $this->max_x = max($dest[0], $this->max_x);
                        $this->min_y = min($dest[1], $this->min_y);
                        $this->max_y = max($dest[1], $this->max_y);
                        if (!in_array($tunnel, $this->tunnels))
                            array_push($this->tunnels, $tunnel);
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

    // A tunnel is a line which may be vertical, horizontal, or diagonal.
    // Convert it into a polygon where $t is the desired thickness.
    function line_to_polygon($x1, $y1, $x2, $y2, $t) {
        if ($y1==$y2)   // horizontal
            return [$x1, $y1-$t, $x1, $y1+$t, $x2, $y2+$t, $x2, $y2-$t];
        else            // vertical or diagonal
            return [$x1-$t, $y1, $x1+$t, $y1, $x2+$t, $y2, $x2-$t, $y2];
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
        $color_white = imagecolorallocate($im, 255, 255, 255);
        $color_yellow = imagecolorallocate($im, 255, 255, 204);
        $color_black = imagecolorallocate($im, 0, 0, 0);
        $color_blue = imagecolorallocate($im, 102, 153, 255);
        $color_gray = imagecolorallocate($im, 128, 128, 128);
        // Draw the border.
        imagefilledrectangle($im, 0, 0, $w, $h, $color_blue);
        // Draw the stone.
        imagefilledrectangle($im, $border, $border, $w-$border, $h-$border, $color_gray);
        // Draw the tunnel walls.
        foreach ($tunnels as $t) {
            $values = $this->line_to_polygon($t[0], $t[1], $t[2], $t[3], $tunnel_size + $wall_thickness * 2);
            imagefilledpolygon($im, $values, 4, $color_blue);
        }
        // Draw the room walls.
        foreach ($rooms as $r) {
            $a = $room_size + $wall_thickness;
            imagefilledrectangle($im, $r[0]-$a, $r[1]-$a, $r[0]+$a, $r[1]+$a, $color_blue);
        }
        // Draw the tunnel floors.
        foreach ($tunnels as $t) {
            $values = $this->line_to_polygon($t[0], $t[1], $t[2], $t[3], $tunnel_size);
            imagefilledpolygon($im, $values, 4, $color_yellow);
        }
        // Draw the room floors and labels.
        for ($i = 0; $i < count($rooms); $i++) {
            $r = $rooms[$i];
            $a = $room_size - $wall_thickness;
            imagefilledrectangle($im, $r[0]-$a, $r[1]-$a, $r[0]+$a, $r[1]+$a, $color_yellow);
            $contents = $this->get_contents();
            $font=realpath('OpenSans-Regular.ttf');
            imagettftext($im, 12, 0, $r[0]-45, $r[1]+5, $color_black, $font, ($i+1).' '.$contents[0]);
            if ($contents[1])
                imagettftext($im, 10, 0, $r[0]-27, $r[1]+25, $color_black, $font, 'Treasure');
        }
        // Draw the grid.
        $grid=20;
        for ($x=$border+$grid; $x<$w-$border; $x+=$grid)
            imageline($im, $x, $border, $x, $h-$border, $color_black);
        for ($y=$border+$grid; $y<$h-$border; $y+=$grid)
            imageline($im, $border, $y, $w-$border, $y, $color_black);
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
} else {
    session_start();
    $num_rooms = intval($_SESSION['num_rooms']);
    $cli = false;
}

$map = new Map($cli, $num_rooms);
$map->generate();
//var_dump($map);
$map->render();

?>
