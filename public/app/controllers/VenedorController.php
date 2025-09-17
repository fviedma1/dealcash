<?php
require_once __DIR__ . '/../models/VenedorModel.php';

class VenedorController {
    private $venedorModel;

    public function __construct() {
        $this->venedorModel = new VenedorModel();
    }

    // Método para obtener los productos del vendedor
    public function getProductsByVenedor($venedorId) {
        return $this->venedorModel->getProductsByVenedor($venedorId);
    }

    // Método para obtener los mensajes de los productos del vendedor
    public function getMessagesByVenedor($venedorId) {
        return $this->venedorModel->getMessagesByVenedor($venedorId);
    }

    // Método para eliminar un mensaje por su ID
    public function deleteMessageById($messageId) {
        return $this->venedorModel->deleteMessageById($messageId);
    }
}
?>