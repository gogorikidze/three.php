<?php
include('../three.php');
$frame = $_GET['frame'];
$rendererType = $_GET['rendererType'];

$scene = new Scene();
if(isset($rendererType) && $rendererType == 'HTMLRenderer'){
    $renderer = new HTMLRenderer(true);
}else{
    $renderer = new WebRenderer(true);
}
$camera = new RayCamera(new Vec3(0,0,5), 25, 25, 3, 6, false);

$angle = $frame*7*pi()/180;

if(isset($rendererType) && $rendererType == 'HTMLRenderer'){
    $geometry = new CubeGeometry(1, ['blue', 'red', 'yellow', 'green', 'purple', 'orange']);
}else{
    $geometry = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
}
$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$renderer->render($camera, $scene, $frame);
?>
