<?php
include('three.php');

$scene = new Scene();
$renderer = new ConsoleRenderer(true);
$camera = new RayCamera(new Vec3(0,0,5), 30, 30, 3, 7, false);

$geometry = new CubeGeometry(1, ['43', '41', '42', '44', '46', '100']);
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$renderer->render($camera, $scene, function($logicParams){
    $logicParams["geometry"]->rotate(3,7,2);
}, array("geometry"=>$geometry));
?>
