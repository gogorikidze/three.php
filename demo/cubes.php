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
$camera = new RayCamera(new Vec3(0,0,5), 60, 60, 3, 6, false);

$angle = $frame*7*pi()/180;

for ($a=0; $a < 3; $a++) { 
    for ($b=0; $b < 3; $b++) { 
        if(isset($rendererType) && $rendererType == 'HTMLRenderer'){
            $geometry = new CubeGeometry(1, ['blue', 'red', 'yellow', 'green', 'purple', 'orange']);
        }else{
            $geometry = new CubeGeometry(1, ['1', '2', '3', '4', '5', '6']);
        }
        $geometry->rotate($angle,-$angle,$angle*3);
        $mesh = new Mesh($geometry, '#');
        $mesh->setPosition(3.5*$b - 3.5, 3.5*$a - 3.5,0);
        $scene->addMesh($mesh);
    }
}

$renderer->render($camera, $scene, $frame);
?>
