<?php
include('../three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$renderer = new Renderer(true);
$camera = new RayCamera(new Vec3(0,0,5), 25, 25, 3, 6, false);

$angle = $frame*7*pi()/180;

$geometry = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera->render($renderer, $scene, $frame, true);
?>
