<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();

$mesh = new Mesh();
$mesh->addTriangle(new Triangle(
  new Vec3(0.5, 0, 0.5),
  new Vec3(0, 0, 0),
  new Vec3(0.5, 1, 0.5),
  "$"
));
$mesh->addTriangle(new Triangle(
  new Vec3(1, 0, 0),
  new Vec3(0.5, 0, 0.5),
  new Vec3(0.5, 1, 0.5),
  "#"
));
$mesh->addTriangle(new Triangle(
  new Vec3(0, 0, 0),
  new Vec3(1, 0, 0),
  new Vec3(0.5, 0, 0.5),
  "0"
));
$scene->addObject($mesh);

$camera = new Camera(new Vec3(0,0,-5), 133, 239, 3, 30, false);
$camera->render($scene, $frame);
?>
