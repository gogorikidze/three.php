<?php
include('tracer.php');
$frame = $_GET['frame'];

if(true){
  $frame += 1;
  header('refresh:0.1; url=index.php?frame='.$frame);
}

$tri = new Triangle(
  new Vec3(-2 - $frame/20, -2 + $frame/20, 5),
  new Vec3(2 + $frame/20, -2 - $frame/20, 5),
  new Vec3(0 - $frame/20, 2 + $frame/20, 5)
);
$camera = new Camera(new Vec3(0,0,0), 160, 280, 3, 10);
$camera->renderTriangle($tri);


?>
