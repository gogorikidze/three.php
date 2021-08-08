<?php
set_time_limit(1500);
include('../three.php');

$scene = new Scene();
$renderer = new HTMLRenderer(false);
$camera = new RayCamera(new Vec3(0,0,5), 300, 300, 3, 50, false);

$geometry = new LoadedGeometry(1, "box.obj");
//$angle = $frame*7*pi()/180;
//$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$renderer->render($camera, $scene, 0);
?>
