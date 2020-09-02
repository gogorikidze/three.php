<?php
include('../three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$renderer = new Renderer(true);
$camera = new RasterCamera(new Vec3(0,0,5), 60, 60, 3, 6, false);

$angle = $frame*7*pi()/180;

$geometry = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$mesh->setPosition(-3.5,3.5,0);
$scene->addMesh($mesh);

$geometry1 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry1->rotate(-$angle*1.2,$angle,-$angle);
$mesh1 = new Mesh($geometry1, '#');
$mesh1->setPosition(0,3.5,0);
$scene->addMesh($mesh1);

$geometry2 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry2->rotate($angle,-$angle*2,-$angle);
$mesh2 = new Mesh($geometry2, '#');
$mesh2->setPosition(3.5,3.5,0);
$scene->addMesh($mesh2);

$geometry3 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry3->rotate($angle,-$angle,$angle*3);
$mesh3 = new Mesh($geometry3, '#');
$mesh3->setPosition(-3.5,-3.5,0);
$scene->addMesh($mesh3);

$geometry4 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry4->rotate(-$angle*1.2,$angle,-$angle);
$mesh4 = new Mesh($geometry4, '#');
$mesh4->setPosition(0,-3.5,0);
$scene->addMesh($mesh4);

$geometry5 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry5->rotate($angle,-$angle*2,-$angle);
$mesh5 = new Mesh($geometry5, '#');
$mesh5->setPosition(3.5,-3.5,0);
$scene->addMesh($mesh5);

//
$geometry6 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry6->rotate(-$angle,$angle,-$angle*3);
$mesh6 = new Mesh($geometry6, '#');
$mesh6->setPosition(-3.5,0,0);
$scene->addMesh($mesh6);

$geometry7 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry7->rotate($angle*1.2,-$angle,$angle);
$mesh7 = new Mesh($geometry7, '#');
$mesh7->setPosition(0,0,0);
$scene->addMesh($mesh7);

$geometry8 = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
$geometry8->rotate(-$angle,$angle*2,$angle);
$mesh8 = new Mesh($geometry8, '#');
$mesh8->setPosition(3.5,0,0);
$scene->addMesh($mesh8);

$camera->render($renderer, $scene, $frame, true);
?>
