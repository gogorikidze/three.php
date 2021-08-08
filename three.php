<?php session_start();
function rand_color() {
  return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
class Vec3{
  public function __construct($x, $y, $z) {
    $this->x = $x;
    $this->y = $y;
    $this->z = $z;
  }
  public function subv($other){
    return new Vec3($this->x - $other->x, $this->y - $other->y, $this->z - $other->z);
  }
  public function scalarProduct($other){
    return $this->x * $other->x + $this->y * $other->y + $this->z * $other->z;
  }
  public function vectorProduct($other){
    return new Vec3($this->y * $other->z - $this->z * $other->y, $this->z * $other->x - $this->x * $other->z, $this->x * $other->y - $this->y * $other->x);
  }
  public function distanceTo($other){
    $first = $other->x - $this->x;
    $second = $other->y - $this->y;
    $third = $other->z - $this->z;
    $dist = sqrt($first*$first + $second*$second + $third*$third);
    return $dist;
  }
}
class Camera{
  public function __construct($position, $h, $w, $far, $density, $cacheEnabled){
    $this->frameStart = microtime(true);
    $this->frameNum = 0;
    $this->density = $density;
    $this->far = $far;
    $this->position = $position;
    $this->h = $h;
    $this->w = $w;
    $this->frustum = [];
    $this->buffer = [];
    $this->computations = 0;

    if($cacheEnabled){
      if(isset($_SESSION['emptyBuffer'])){
        $this->buffer = $_SESSION['emptyBuffer'];
      }else{
        $this->createBuffer();
      }

      if(isset($_SESSION['frustum'])){
        $this->frustum = $_SESSION['frustum'];
      }else{
        $this->createfrustum();
      }
    }else{
      $this->createBuffer();
      $this->createfrustum();
    }
  }
  public function createfrustum(){ // this grid creates an array of pixels in front of the camera
    $frustum = [];
    //these variables are used to center the array in front of the camera
    $halfwfrustum = $this->w/$this->density/2 - 1/$this->density/2;
    $halfhfrustum = $this->h/$this->density/2 - 1/$this->density/2;

    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        $thisray = new Vec3(
          $this->position->x - $w/$this->density + $halfwfrustum,
          $this->position->y - $h/$this->density + $halfhfrustum,
          $this->position->z + $this->far
        );
        array_push($line, $thisray); //every pixel is pushed in line array
      }
      array_push($frustum, $line); //this scanline (arr) is pushed to main array
    }

    $_SESSION['frustum'] = $frustum;
    $this->frustum = $_SESSION['frustum'];
  }
  public function createBuffer(){
    $buffer = [];
    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        array_push($line, ["empty", 1000]);
      }
      array_push($buffer, $line);
    }
    $_SESSION['emptyBuffer'] = $buffer;
    $this->buffer = $_SESSION['emptyBuffer'];
  }
}
class RayCamera extends Camera{
  public function renderFace($face, $geometry, $mesh){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        $rayorigin = $this->frustum[$h][$w];
        $raydirection = new Vec3(0,0,-1);
        $ray = $face->checkRay($rayorigin, $raydirection, $geometry);
        if($ray && $ray < $this->buffer[$h][$w][1]){
            $this->buffer[$h][$w] = [$face->colorChar, $ray];
        }
        $this->computations += 1;
      }
    }
  }
}
class Face{
  public function __construct($v0index, $v1index, $v2index, $colorChar){
    $this->v0index = $v0index;
    $this->v1index = $v1index;
    $this->v2index = $v2index;
    $this->colorChar = $colorChar;
    $this->originalZ = new Vec3(0,0,0);
  }
  public function checkRay($o, $d, $geometry){ //checks for ray triangle intersection
    $v0 = $geometry->vertices[$this->v0index];
    $v1 = $geometry->vertices[$this->v1index];
    $v2 = $geometry->vertices[$this->v2index];
    $eO = $o; // origin point
    $eD = $d; // direction
    $eV0 = $v0;
    $eV1 = $v1;
    $eV2 = $v2;
    $eE1 = $eV1->subv($eV0);
    $eE2 = $eV2->subv($eV0);
    $eT = $eO->subv($eV0);
    $eP = $eD->vectorProduct($eE2);
    $eQ = $eT->vectorProduct($eE1);
    if($eP->scalarProduct($eE1) != 0){
      $equc = 1 / $eP->scalarProduct($eE1);
    }else{
      return false;
    }
    $u = $equc * $eP->scalarProduct($eT);
    if($u < 0){return false;}
    $v = $equc * $eQ->scalarProduct($eD);
    if($v < 0 || $v + $u > 1){return false;}
    $t = $equc * $eQ->scalarProduct($eE2);
    if($t < 0){return false;}
    return $t;
  }
  public function isPointInTriangle($barycentric){
    $a = $barycentric->x > 0 && $barycentric->x < 1;
    $b = $barycentric->y > 0 && $barycentric->y < 1;
    $c = $barycentric->z > 0 && $barycentric->z < 1;
    return $a && $b && $c;
  }
  public function barycentricAt($point, $geometry){
    $a = $geometry->vertices[$this->v0index];
    $b = $geometry->vertices[$this->v1index];
    $c = $geometry->vertices[$this->v2index];

    $v0 = $b->subv($a);
    $v1 = $c->subv($a);
    $v2 = $point->subv($a);
    $d00 = $v0->scalarProduct($v0);
    $d01 = $v0->scalarProduct($v1);
    $d11 = $v1->scalarProduct($v1);
    $d20 = $v2->scalarProduct($v0);
    $d21 = $v2->scalarProduct($v1);
    $denom = $d00 * $d11 - $d01 * $d01;
    if($denom == 0) return new Vec3(0,0,0);
    $v = ($d11 * $d20 - $d01 * $d21) / $denom;
    $w = ($d00 * $d21 - $d01 * $d20) / $denom;
    $u = 1 - $v - $w;
    return new Vec3($u, $v, $w);
  }
  public function zCoordAt($face, $barycentric){
    $v0z = $face->originalZ->x;
    $v1z = $face->originalZ->y;
    $v2z = $face->originalZ->z;
    $z = $barycentric->y*$v0z + $barycentric->x*$v1z + $barycentric->z*$v2z;
    return $z;
  }
}
class Scene{
  public function __construct(){
    $this->meshes = [];
  }
  public function addMesh($mesh){
    array_push($this->meshes, $mesh);
  }
}
class Mesh{
  public function __construct($geometry, $colorChar){
    $this->geometry = $geometry;
    $this->colorChar = $colorChar;
  }
  public function setPosition($x, $y, $z){
    foreach ($this->geometry->vertices as $vertex) {
      $vertex->x += $x;
      $vertex->y += $y;
      $vertex->z += $z;
    }
  }
}
class Geometry{
  public function __construct(){
    $this->faces = [];
    $this->vertices = [];
  }
  public function addFace($face){
    array_push($this->faces, $face);
  }
  public function addVertex($vertex){
    array_push($this->vertices, $vertex);
  }
  public function rotate($pitch, $roll, $yaw) {
    $cosa = cos($yaw);
    $sina = sin($yaw);

    $cosb = cos($pitch);
    $sinb = sin($pitch);

    $cosc = cos($roll);
    $sinc = sin($roll);

    $Axx = $cosa*$cosb;
    $Axy = $cosa*$sinb*$sinc - $sina*$cosc;
    $Axz = $cosa*$sinb*$cosc + $sina*$sinc;

    $Ayx = $sina*$cosb;
    $Ayy = $sina*$sinb*$sinc + $cosa*$cosc;
    $Ayz = $sina*$sinb*$cosc - $cosa*$sinc;

    $Azx = -$sinb;
    $Azy = $cosb*$sinc;
    $Azz = $cosb*$cosc;

    foreach($this->vertices as $vertex){
      $px = $vertex->x;
      $py = $vertex->y;
      $pz = $vertex->z;

      $vertex->x = $Axx*$px + $Axy*$py + $Axz*$pz;
      $vertex->y = $Ayx*$px + $Ayy*$py + $Ayz*$pz;
      $vertex->z = $Azx*$px + $Azy*$py + $Azz*$pz;
    }
  }
}
class CubeGeometry extends Geometry{
  public function __construct($size, $colors){
    $this->faces = [];
    $this->vertices = [];

    $top = $colors[0];
    $bottom = $colors[1];
    $front = $colors[2];
    $back = $colors[3];
    $left = $colors[4];
    $right = $colors[5];

    $this->addVertex(new Vec3(-$size,-$size,-$size));
    $this->addVertex(new Vec3($size,-$size,-$size));
    $this->addVertex(new Vec3($size,-$size,$size));
    $this->addVertex(new Vec3(-$size,-$size,$size));

    $this->addVertex(new Vec3(-$size,$size,-$size));
    $this->addVertex(new Vec3($size,$size,-$size));
    $this->addVertex(new Vec3($size,$size,$size));
    $this->addVertex(new Vec3(-$size,$size,$size));

    //top
    $this->addFace(new Face(3,2,0,$top));
    $this->addFace(new Face(2,1,0,$top));
    //bottom
    $this->addFace(new Face(7,6,5,$bottom));
    $this->addFace(new Face(4,7,5,$bottom));

    $this->addFace(new Face(0,5,1,$front));
    $this->addFace(new Face(0,5,4,$front));

    $this->addFace(new Face(4,7,0,$left));
    $this->addFace(new Face(0,3,7,$left));

    $this->addFace(new Face(5,6,1,$right));
    $this->addFace(new Face(6,2,1,$right));

    $this->addFace(new Face(7,6,3,$back));
    $this->addFace(new Face(6,2,3,$back));
  }
}
class LoadedGeometry extends Geometry{
  public function __construct($size, $filepath){
    $this->faces = [];
    $this->vertices = [];

    foreach($this->getLines($filepath) as $line) {
      if($line[0].$line[1] == "v "){
        $values = explode(" ",$line);
        $this->addVertex(new Vec3(
            floatval($values[1]),
            floatval($values[2]),
            floatval($values[3]))
        );
      }
      if($line[0].$line[1] == "f "){
        $values = explode(" ",$line);
        if(count($values) == 5){
          $this->addFace(new Face(
            intval(explode("/", $values[1])[0]) - 1,
            intval(explode("/", $values[2])[0]) - 1,
            intval(explode("/", $values[3])[0]) - 1, rand_color())
          );
          $this->addFace(new Face(
            intval(explode("/", $values[1])[0]) - 1,
            intval(explode("/", $values[3])[0]) - 1,
            intval(explode("/", $values[4])[0]) - 1, rand_color())
          );
        }else if(count($values) == 4){
          $this->addFace(new Face(
            intval(explode("/", $values[1])[0]) - 1,
            intval(explode("/", $values[2])[0]) - 1,
            intval(explode("/", $values[3])[0]) - 1, rand_color())
          );
        }else{
          echo "wtf";
        }
      }
    }
    // var_dump($this->vertices);
    // echo "<br>";
    // var_dump($this->faces);
  }
  function getLines($filepath){
    $file = fopen($filepath, "r");
    if (!$file)
        die('file does not exist or cannot be opened');

    while (($line = fgets($file)) !== false) {
        yield $line;
    }

    fclose($file);
  }
}
class WebRenderer{
  public function __construct($animate){
    $this->animate = $animate;
    $this->RendererType = "";
  }
  public function nextFrame($frame){
    if($this->animate){
      header('refresh:0.001; url='.basename($_SERVER['PHP_SELF']).'?frame='.($frame+1)."&rendererType=".$this->RendererType);
    }
  }
  public function renderBuffer($camera){
    for($h = 0; $h < $camera->h; $h++){
      for($w = 0; $w < $camera->w; $w++){
        if($camera->buffer[$h][$w][0] != "empty"){
          echo $camera->buffer[$h][$w][0];
          echo $camera->buffer[$h][$w][0];
        }else{
          echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
      }
      echo nl2br("\n");
    }
  }
  public function render($camera, $scene, $frame, $sysInfo = true){
    $this->nextFrame($frame);

    foreach($scene->meshes as $mesh){
      foreach($mesh->geometry->faces as $face){
          $camera->renderFace($face, $mesh->geometry, $mesh);
      }
    }

    $this->renderBuffer($camera);

    $this->displayStats($camera, $sysInfo, $scene);
  }
  public function displayStats($camera, $display, $scene){
    if($display){
      $frameTime = microtime(true) - $camera->frameStart;
      $fps = 1 / $frameTime;
      $faces = 0;
      foreach ($scene->meshes as $mesh) foreach ($mesh->geometry->faces as $face) $faces += 1;
      echo nl2br("stats: \n");
      echo nl2br("FramesPerSecond: ".$fps."\n");
      echo nl2br("frameTime: ".$frameTime."\n");
      echo nl2br("resolution: ".$camera->w."x".$camera->h."CPX \n");
      echo nl2br("Calculations: ".$camera->computations."\n");
    }
  }
}
class HTMLRenderer extends WebRenderer{
  public function __construct($animate){
    $this->animate = $animate;
    $this->RendererType = "HTMLRenderer";
  }
  public function renderBuffer($camera){
    for($h = 0; $h < $camera->h; $h++){
      echo "<div style='overflow:hidden'>";
      for($w = 0; $w < $camera->w; $w++){
        if($camera->buffer[$h][$w][0] != "empty"){
          echo "<div style='float: left;height:2px;width:2px;background-color:".$camera->buffer[$h][$w][0].";padding:0;margin:0'></div>";
        }else{
          echo "<div style='float: left;height:2px;width:2px;background-color:cyan;padding:0;margin:0'></div>";
        }
      }
      echo "</div>";
    }
  }
}
class ConsoleRenderer{
  public function displayStats($camera, $display, $scene){
    if($display){
      $time = microtime(true);
      $frameTime = $time - $camera->frameStart;
      $camera->frameStart = $time;
      $fps = 1 / $frameTime;
      $faces = 0;
      foreach ($scene->meshes as $mesh) foreach ($mesh->geometry->faces as $face) $faces += 1;
      //echo "stats: \n";
      echo "FramesPerSecond: ".$fps."";
      //echo "frameTime: ".$frameTime."\n";
      //echo "resolution: ".$camera->w."x".$camera->h."CPX \n";
      //echo "Calculations: ".$camera->computations."\n";
    }
  }
  public function renderBuffer($camera){
    //system('cls');
    $DisplayFrame = "";
    for($h = 0; $h < $camera->h; $h++){
      $line = "";
      for($w = 0; $w < $camera->w; $w++){
        if($camera->buffer[$h][$w][0] != "empty"){
          $line = $line . "\e[".$camera->buffer[$h][$w][0]."m  \033[m";
        }else{
          $line = $line . "  ";
        }
      }
      $DisplayFrame = $DisplayFrame.$line."\n";
    }
    echo $DisplayFrame;
  }
  public function render($camera, $scene, $logic = 'none', $logicParams = null, $sysInfo = true){
    if($logic != 'none') $logic($logicParams);

    foreach($scene->meshes as $mesh){
      foreach($mesh->geometry->faces as $face){
          $camera->renderFace($face, $mesh->geometry, $mesh);
      }
    }

    $this->renderBuffer($camera);

    $this->displayStats($camera, $sysInfo, $scene);

    sleep(0.1);
    $camera->frame += 1;
    $this->render($camera, $scene, $sysInfo);
  }
}
?>
