<?php
/*
Esta clase crea la conexion recibiendo por parametro los datos para establecerla a la base de datos mysql
se uso PDO ya que da mas beneficios
*/
class Conexion
{
  private $host;
  private $usuario;
  private $contraseña;
  private $base_de_datos;
  private $conexion;

  public function __construct($host, $usuario, $contraseña, $base_de_datos)
  {
    $this->host = $host;
    $this->usuario = $usuario;
    $this->contraseña = $contraseña;
    $this->base_de_datos = $base_de_datos;
  }

  public function conectar()
  {
    try {
      $this->conexion = new PDO("mysql:host=$this->host;dbname=$this->base_de_datos", $this->usuario, $this->contraseña);
      $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $this->conexion;
    } catch (PDOException $e) {
      die("Error de conexión: " . $e->getMessage());
    }
  }

  public function getConnection(){
    return $this->conexion;
  }

  public function desconectar()
  {
    if ($this->conexion) {
      $this->conexion = null;
    }
  }
}
