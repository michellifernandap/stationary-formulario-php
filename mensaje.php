<?php

class Mensaje {
    protected $id;
    protected $nombre;
    protected $telefono;
    protected $email;
    protected $mensaje;


    public function __construct($id, $nombre, $telefono, $email, $mensaje) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->mensaje = $mensaje;

    }

    // Getters

    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }


    public function getTelefono() {
        return $this->telefono;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }


    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }
}

?>
