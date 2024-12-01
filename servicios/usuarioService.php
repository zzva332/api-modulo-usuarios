<?php
class UsuarioService {
    // inserta a la db un nuevo usuario
    public function insertar_registro($user){
        try{
            // valida que el email no este registrado
            if($this->exist_email($user->email)){
                throw new Exception("Email already exists");
            }
            // encripta la password por seguridad
            $password_encrypt = password_hash($user->password, PASSWORD_DEFAULT);
    
            $conexion = getConexion();
            $sql = "INSERT INTO usuario (nombre, email, password) VALUES (?, ?, ?)";
            $stmt = $conexion->conectar()->prepare($sql);
            $stmt->execute(array($user->nombre, strtolower($user->email), $password_encrypt));
    
            $conexion->desconectar();
            return true;
        } catch (Exception $e) { }
        return false;
    }
    // metodo para validar si existe el email en db
    public function exist_email($email){
        $conexion = getConexion();
        $sql = "SELECT COUNT(*) as count FROM usuario WHERE email = ?";
        $stmt = $conexion->conectar()->prepare($sql);
        $stmt->execute(array(strtolower($email)));
        $item = $stmt->fetch(PDO::FETCH_OBJ);

        return intval($item->count) > 0;
    }

    // obtiene el registro por email y password
    public function get_by_credenciales($email, $password){
        $conexion = getConexion();
        $sql = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $conexion->conectar()->prepare($sql);
        $stmt->execute(array($email));

        $item = $stmt->fetch(PDO::FETCH_OBJ);

        if($item == null) return null;

        // valida la password si es correcta con la que hay en db ya que esta encriptada toca usar password_verify(...)
        if(password_verify($password, $item->password)){
            return $item;
        }
        return null;
    }
    public function get_by_id($id){
        $conexion = getConexion();
        $sql = "SELECT * FROM usuario WHERE id = ?";
        $stmt = $conexion->conectar()->prepare($sql);
        $stmt->execute(array($id));

        $item = $stmt->fetch(PDO::FETCH_OBJ);

        if($item == null) return null;
        return $item;
    }

    public function actualizar($id, $user){
        try{
            // encripta la password por seguridad
            $oldInfo = $this->get_by_id($id);
            if($oldInfo == null) return false;
            $password_encrypt = $oldInfo->password;
            if(!empty($user->newpassword)){
                $password_encrypt = password_hash($user->newpassword, PASSWORD_DEFAULT);
            }
    
            $conexion = getConexion();
            $sql = "UPDATE usuario SET nombre = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conexion->conectar()->prepare($sql);
            $stmt->execute(array(
                $user->nombre, $user->email, $password_encrypt, $id
            ));
    
            $conexion->desconectar();
            return true;
        } catch (Exception $e) { }
        return false;
    }

    public function remove_usuario($id){
        $conexion = getConexion();
        $sql = "DELETE FROM usuario WHERE id = ?";
        $stmt = $conexion->conectar()->prepare($sql);
        $stmt->execute(array($id));
        return true;
    }
}