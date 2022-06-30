<?php
session_start();
$_SESSION['submit'] = $_POST['submit'];
$_SESSION['num_rooms'] = $_POST['num_rooms'];
$_SESSION['len_tunnel'] = $_POST['len_tunnel'];
$_SESSION['draw_grid'] = $_POST['draw_grid'];
$_SESSION['prune_dead_ends'] = $_POST['prune_dead_ends'];
$_SESSION['tunnel_direction'] = $_POST['tunnel_direction'];
$_SESSION['room_shape'] = $_POST['room_shape'];
$_SESSION['color_border'] = $_POST['color_border'];
$_SESSION['color_stone'] = $_POST['color_stone'];
$_SESSION['color_floor'] = $_POST['color_floor'];
$_SESSION['color_walls'] = $_POST['color_walls'];
$_SESSION['color_grid'] = $_POST['color_grid'];
$_SESSION['color_text'] = $_POST['color_text'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.98.0">
    <title>Pointcrawl Generator</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/navbar-static/">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="navbar-top.css" rel="stylesheet">
  </head>
  <body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Pointcrawl Generator</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.html">About</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main class="container">
  <div class="bg-light p-5 rounded mb-3">
    <h1>Pointcrawl Generator</h1>
    <p class="lead">WWN provides an algorithm for generating point crawls randomly.  This script automates that algorithm.</p>
  </div>

<form class="mb-3" method='POST'>
  <div class="row mb-3">
    <label for="inputNumRooms" class="col-sm-2 col-form-label">Number of Rooms</label>
    <div class="col-sm-1">
      <input type="text" class="form-control" id="inputNumRooms" name="num_rooms" aria-describedby="numRoomsHelpInline" value="<?php echo isset($_POST['num_rooms']) ? htmlspecialchars($_POST['num_rooms'] ?? '', ENT_QUOTES) : 5; ?>">
    </div>
    <div class="col-sm-3">
      <small id="numRoomsHelpInline" class="text-muted">Between 1 and 100</small>
    </div>
    <label for="inputLenTunnel" class="col-sm-2 col-form-label">Max Tunnel Length</label>
    <div class="col-sm-1">
      <input type="text" class="form-control" id="inputLenTunnel" name="len_tunnel" aria-describedby="lenTunnelHelpInline" value="<?php echo isset($_POST['len_tunnel']) ? htmlspecialchars($_POST['len_tunnel'] ?? '', ENT_QUOTES) : 1; ?>">
    </div>
    <div class="col-sm-3">
      <small id="lenTunnelHelpInline" class="text-muted">Between 1 and 5</small>
    </div>
  </div>
  <div class="row mb-3">
    <label for="checkDrawGrid" class="col-sm-2 col-form-label">Grid</label>
    <div class="col-sm-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="checkDrawGrid" name="draw_grid" value="on" <?php echo ((!isset($_POST['submit']) && $_SESSION['draw_grid'] != 'off') || ($_SESSION['draw_grid'] === 'on')) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="checkDrawGrid">Draw gridlines</label>
      </div>
    </div>
    <label for="checkPrune" class="col-sm-2 col-form-label">Dead Ends</label>
    <div class="col-sm-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="checkPrune" name="prune_dead_ends" value="on" <?php echo ((!isset($_POST['submit']) && $_SESSION['prune_dead_ends'] != 'off') || ($_SESSION['prune_dead_ends'] === 'on')) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="checkPrune">Prune dead ends</label>
      </div>
    </div>
  </div>
  <fieldset class="row mb-3">
    <legend class="col-form-label col-sm-2 pt-0">Tunnel Direction</legend>
    <div class="col-sm-4">
      <div class="form-check">
        <input class="form-check-input" type="radio" id="radioDirectionsCompass" name="tunnel_direction" value="compass" <?php echo ((!isset($_POST['submit']) && $_SESSION['tunnel_direction'] != 'compass') || ($_SESSION['tunnel_direction'] === 'compass')) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="radioDirectionsCompass">Eight points of the compass</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="radioDirectionsRandom" name="tunnel_direction" value="random" <?php echo (isset($_POST['tunnel_direction']) && $_POST['tunnel_direction'] === 'random') ? 'checked' : ''; ?>>
        <label class="form-check-label" for="radioDirectionsRandom">
          Random
        </label>
      </div>
    </div>
    <legend class="col-form-label col-sm-2 pt-0">Room Shape</legend>
    <div class="col-sm-4">
      <div class="form-check">
        <input class="form-check-input" type="radio" id="radioRoomShapeSquare" name="room_shape" value="square" <?php echo ((!isset($_POST['submit']) && $_SESSION['room_shape'] != 'square') || ($_SESSION['room_shape'] === 'square')) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="radioRoomShapeSquare">
          Square
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="radioRoomShapeCircle" name="room_shape" value="circle" <?php echo (isset($_POST['room_shape']) && $_POST['room_shape'] === 'circle') ? 'checked' : ''; ?>>
        <label class="form-check-label" for="radioRoomShapeCircle">
          Circle
        </label>
      </div>
    </div>
  </fieldset>
  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Colors</label>
    <div class="col-sm-10">
      <input type="color" class="form-control-color" id="inputColorBorder" name="color_border" value="<?php echo isset($_POST['color_border']) ? $_POST['color_border'] : "#6699FF"; ?>" title="Choose border color">
      <label for="inputColorBorder" class="form-label m-2">Border</label>
      <input type="color" class="form-control-color" id="inputColorStone" name="color_stone" value="<?php echo isset($_POST['color_stone']) ? $_POST['color_stone'] : "#808080"; ?>" title="Choose stone color">
      <label for="inputColorStone" class="form-label m-2">Stone</label>
      <input type="color" class="form-control-color" id="inputColorFloor" name="color_floor" value="<?php echo isset($_POST['color_floor']) ? $_POST['color_floor'] : "#FFFFCC"; ?>" title="Choose floor color">
      <label for="inputColorFloor" class="form-label m-2">Floor</label>
      <input type="color" class="form-control-color" id="inputColorWalls" name="color_walls" value="<?php echo isset($_POST['color_walls']) ? $_POST['color_walls'] : "#6699FF"; ?>" title="Choose wall color">
      <label for="inputColorWalls" class="form-label m-2">Walls</label>
      <input type="color" class="form-control-color" id="inputColorGrid" name="color_grid" value="<?php echo isset($_POST['color_grid']) ? $_POST['color_grid'] : "#000000"; ?>" title="Choose grid color">
      <label for="inputColorGrid" class="form-label m-2">Grid</label>
      <input type="color" class="form-control-color" id="inputColorText" name="color_text" value="<?php echo isset($_POST['color_text']) ? $_POST['color_text'] : "#000000"; ?>" title="Choose text color">
      <label for="inputColorText" class="form-label m-2">Text</label>
    </div>
  </div>
  <button type="submit" name="submit" class="btn btn-primary">Generate</button>
</form>

</main>

<div class="m-3 text-center">
  <img src="mapgen.php" alt="generated image"/>
  <!--?php include 'print.php'; ?-->
</div>

  </body>
</html>
