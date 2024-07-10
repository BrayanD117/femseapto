<?php

require_once __DIR__ . '/../models/SaldoAhorroModel.php';

class SaldoAhorroController {

    /**
     * Crea un nuevo saldo de ahorro.
     * @param array $datos Datos del saldo de ahorro a crear.
     * @return int|null ID del saldo de ahorro creado.
     */
    public function crear($datos) {
        $saldoAhorro = new SaldoAhorro(
            null, // El id se genera automáticamente al guardar
            $datos['idUsuario'],
            $datos['idLineaAhorro'],
            $datos['valorSaldo']
        );

        $saldoAhorro->guardar();
        
        return $saldoAhorro->id;
    }

    /**
     * Actualiza un saldo de ahorro existente.
     * @param int $id ID del saldo de ahorro a actualizar.
     * @param array $datos Nuevos datos del saldo de ahorro.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró el saldo de ahorro.
     */
    public function actualizar($id, $datos) {
        $saldoAhorro = SaldoAhorro::obtenerPorId($id);
        if (!$saldoAhorro) {
            return false;
        }

        $saldoAhorro->idLineaAhorro = $datos['idLineaAhorro'];
        $saldoAhorro->valorSaldo = $datos['valorSaldo'];

        $saldoAhorro->guardar();

        return true;
    }

    /**
     * Obtiene un saldo de ahorro por su ID.
     * @param int $id ID del saldo de ahorro a obtener.
     * @return SaldoAhorro|array El saldo de ahorro encontrado o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $saldoAhorro = SaldoAhorro::obtenerPorId($id);
        if ($saldoAhorro) {
            return $saldoAhorro;
        } else {
            http_response_code(404);
            return array("message" => "Saldo de ahorro no encontrado.");
        }
    }

    /**
     * Obtiene los saldos de ahorro por ID de usuario.
     * @param int $idUsuario ID del usuario a obtener.
     * @return array|array[] Los saldos de ahorro encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $saldos = SaldoAhorro::obtenerPorIdUsuario($idUsuario);
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "Saldos de ahorro no encontrados.");
        }
    }

    /**
     * Obtiene todos los saldos de ahorro disponibles.
     * @return array|array[] Todos los saldos de ahorro encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $saldos = SaldoAhorro::obtenerTodos();
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron saldos de ahorro.");
        }
    }

    /**
     * Elimina un saldo de ahorro por su ID.
     * @param int $id ID del saldo de ahorro a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró el saldo de ahorro.
     */
    public function eliminar($id) {
        $saldoAhorro = SaldoAhorro::obtenerPorId($id);
        if (!$saldoAhorro) {
            return false;
        }

        $saldoAhorro->eliminar();

        return true;
    }
}
?>