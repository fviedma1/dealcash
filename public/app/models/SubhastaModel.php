<?php

class SubhastaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para obtener subhastes con filtros y ordenamiento
    public function obtenirSubhastes($status = 'totes', $fechaInicio = null, $fechaFin = null) {
        $query = "SELECT * FROM subhasta WHERE 1=1";

        if ($status === 'obertes') {
            $query .= " AND estat = 'oberta'";
        } else if ($status === 'tancades') {
            $query .= " AND estat = 'tancada'";
        }

        if ($fechaInicio && $fechaFin) {
            $query .= " AND data_hora BETWEEN :fechaInicio AND :fechaFin";
        }

        $query .= " ORDER BY data_hora DESC";
        $stmt = $this->conn->prepare($query);

        if ($fechaInicio && $fechaFin) {
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener detalles de una subhasta
    public function obtenirSubhasta($id) {
        $query = "SELECT * FROM subhasta WHERE id_subhasta = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para obtener subhasta y sus productos
    public function obtenirSubhastaAmbProductes($id) {
        $query = "SELECT * FROM subhasta WHERE id_subhasta = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $subhasta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($subhasta) {
            $query = "SELECT * FROM producte WHERE subhasta_id = :id_subhasta";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_subhasta', $id);
            $stmt->execute();
            $subhasta['productes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $subhasta;
    }

    // Otros métodos...
}
?>