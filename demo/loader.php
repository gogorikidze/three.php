<?php
set_time_limit(1500);
include('../three.php');

$scene = new Scene();
$renderer = new HTMLRenderer(false);
$camera = new RayCamera(new Vec3(0,0,5), 100, 100, 3, 35, false);

$geometry = new LoadedGeometry(1, 'obj/suzanne.obj');
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$renderer->render($camera, $scene, 0);
?>
