<?php
include('tracer.php');
$frame = $_GET['frame'];

$scene = new Scene();
$tri = new Triangle(
  new Vec3(-2 - $frame/20, -2 + $frame/20, 5),
  new Vec3(2 + $frame/20, -2 - $frame/20, 5),
  new Vec3(0 - $frame/20, 2 + $frame/20, 5),
  "#"
);
$scene->addObject($tri);
$camera = new Camera(new Vec3(0,0,0), 156/4, 319/4, 3, 20/4);
$camera->render($scene, $frame);


?>
