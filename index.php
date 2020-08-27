<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();

$mesh = new Mesh();
$tri = new Triangle(
  new Vec3(-2 - $frame/20, -2 + $frame/20, 5),
  new Vec3(2 + $frame/20, -2 - $frame/20, 5),
  new Vec3(0 - $frame/20, 2 + $frame/20, 5),
  "#"
);
$tri2 = new Triangle(
  new Vec3(-5 - $frame/20, -5 + $frame/20, 5),
  new Vec3(-1 + $frame/20, -5 - $frame/20, 5),
  new Vec3(-3 - $frame/20, -1 + $frame/20, 5),
  "0"
);
$mesh->addTriangle($tri);
$mesh->addTriangle($tri2);
$scene->addObject($mesh);

$camera = new Camera(new Vec3(0,0,0), 156/4, 318/4, 3, 20/4);
$camera->render($scene, $frame);
?>
