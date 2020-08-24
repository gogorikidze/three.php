<?php
include('tracer.php');
$frame = $_GET['frame'];

$tri = new Triangle(
  new Vec3(-2 - $frame/200, -2 + $frame/200, 5),
  new Vec3(2 + $frame/200, -2 - $frame/200, 5),
  new Vec3(0 - $frame/200, 2 + $frame/200, 5)
);
$camera = new Camera(new Vec3(0,0,0), 30, 50, 3, 3);
$camera->renderTriangle($tri);

if(true){
  echo 'frame:'.$frame;
  $frame += 1;
  header('refresh:0.001; url=index.php?frame='.$frame);
}
?>
