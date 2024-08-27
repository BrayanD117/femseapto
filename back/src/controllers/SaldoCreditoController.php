<?php

require_once __DIR__ . '/../models/SaldoCreditoModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class SaldoCreditoController {

    /**
     * Crea un nuevo saldo de crédito.
     * @param array $datos Datos del saldo de crédito a crear.
     * @return int|null ID del saldo de crédito creado.
     */
    public function crear($datos) {
        $saldoCredito = new SaldoCredito(
            null, // El id se genera automáticamente al guardar
            $datos['idUsuario'],
            $datos['idLineaCredito'],
            $datos['cuotaActual'],
            $datos['cuotasTotales'],
            $datos['valorSolicitado'],
            $datos['cuotaQuincenal'],
            $datos['valorPagado'],
            $datos['valorSaldo'],
            $datos['fechaCorte']
        );

        $saldoCredito->guardar();
        
        return $saldoCredito->id;
    }

    /**
     * Actualiza un saldo de crédito existente.
     * @param int $id ID del saldo de crédito a actualizar.
     * @param array $datos Nuevos datos del saldo de crédito.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró el saldo de crédito.
     */
    public function actualizar($id, $datos) {
        $saldoCredito = SaldoCredito::obtenerPorId($id);
        if (!$saldoCredito) {
            return false;
        }

        $saldoCredito->idLineaCredito = $datos['idLineaCredito'];
        $saldoCredito->cuotaActual = $datos['cuotaActual'];
        $saldoCredito->cuotasTotales = $datos['cuotasTotales'];
        $saldoCredito->valorSolicitado = $datos['valorSolicitado'];
        $saldoCredito->cuotaQuincenal = $datos['cuotaQuincenal'];
        $saldoCredito->valorPagado = $datos['valorPagado'];
        $saldoCredito->valorSaldo = $datos['valorSaldo'];
        $saldoCredito->fechaCorte = $datos['fechaCorte'];

        $saldoCredito->guardar();

        return true;
    }

    public function crearOActualizar($datos) {
        foreach ($datos as $dato) {
            $numeroDocumento = $dato['numeroDocumento'];
            $usuario = Usuario::obtenerPorNumeroDocumento($numeroDocumento);
            
            if ($usuario) {
                $dato['idUsuario'] = $usuario->id;
                unset($dato['numeroDocumento']);
                
                $idUsuario = $dato['idUsuario'];
                $idLineaCredito = $dato['idLineaCredito'];
                
                $saldoExistente = SaldoCredito::obtenerPorIdUsuarioYLineaCredito($idUsuario, $idLineaCredito);
                
                if ($saldoExistente) {
                    $this->actualizar($saldoExistente->id, $dato);
                } else {
                    $this->crear($dato);
                }
            }
        }
    }

    /**
     * Obtiene un saldo de crédito por su ID.
     * @param int $id ID del saldo de crédito a obtener.
     * @return SaldoCredito|array El saldo de crédito encontrado o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $saldoCredito = SaldoCredito::obtenerPorId($id);
        if ($saldoCredito) {
            return $saldoCredito;
        } else {
            http_response_code(404);
            return array("message" => "Saldo de crédito no encontrado.");
        }
    }

    /**
     * Obtiene los saldos de crédito por ID de usuario.
     * @param int $idUsuario ID del usuario a obtener.
     * @return SaldoCredito|array Los saldos de crédito encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $saldos = SaldoCredito::obtenerPorIdUsuario($idUsuario);
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "Saldos de crédito no encontrados.");
        }
    }

    /**
     * Obtiene todos los saldos de crédito disponibles.
     * @return array|array[] Todos los saldos de crédito encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $saldos = SaldoCredito::obtenerTodos();
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron saldos de crédito.");
        }
    }

    /**
     * Elimina un saldo de crédito por su ID.
     * @param int $id ID del saldo de crédito a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró el saldo de crédito.
     */
    public function eliminar($id) {
        $saldoCredito = SaldoCredito::obtenerPorId($id);
        if (!$saldoCredito) {
            return false;
        }

        $saldoCredito->eliminar();

        return true;
    }

    public function upload() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['data'])) {
                $this->crearOActualizar($data['data']);
                http_response_code(200);
                echo json_encode(array("message" => "Datos procesados exitosamente."));
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Datos no válidos."));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Server error: " . $e->getMessage()));
        }
    }
}
?>