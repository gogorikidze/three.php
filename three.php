<?php session_start();
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
}
class Camera{
  public function __construct($position, $h, $w, $far, $density, $cacheEnabled){
    $this->frameStart = microtime(true);
    $this->density = $density;
    $this->far = $far;
    $this->position = $position;
    $this->h = $h;
    $this->w = $w;
    $this->frustum = [];
    $this->buffer = [];

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
  public function renderFace($face, $geometry, $mesh){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        $rayorigin = $this->frustum[$h][$w];
        $raydirection = new Vec3(0,0,-1);
        $ray = $face->checkRay($rayorigin, $raydirection, $geometry);
        if($ray && $ray < $this->buffer[$h][$w][1]){
            $this->buffer[$h][$w] = [$face->colorChar, $ray];
        }
      }
    }
  }
  public function render($scene, $frame, $filename, $sysInfo){
    $frame += 1;
    header('refresh:0.001; url='.$filename.'?frame='.$frame);

    foreach($scene->meshes as $mesh){
      foreach($mesh->geometry->faces as $face){
          $this->renderFace($face, $mesh->geometry, $mesh);
      }
    }

    $this->renderBuffer();


    if($sysInfo){
      $frameTime = microtime(true) - $this->frameStart;
      $fps = 1 / $frameTime;
      echo nl2br("stats:// \n");
      echo nl2br("FramesPerSecond: ".$fps."\n");
      echo nl2br("frameTime:".$frameTime."\n");
      echo nl2br("resolution: ".$this->w."x".$this->h."CPX \n");
    }
  }
  public function renderBuffer(){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        if($this->buffer[$h][$w][0] != "empty"){
          echo $this->buffer[$h][$w][0];
          echo $this->buffer[$h][$w][0];
        }else{
          echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
      }
      echo nl2br("\n");
    }
  }
}
class Face{
  public function __construct($v0index, $v1index, $v2index, $colorChar){
    $this->v0index = $v0index;
    $this->v1index = $v1index;
    $this->v2index = $v2index;
    $this->colorChar = $colorChar;
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
?>
