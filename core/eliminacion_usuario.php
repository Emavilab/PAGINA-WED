<?php

require_once __DIR__ . '/conexion.php';

if (!function_exists('tablaExiste')) {
    function tablaExiste(mysqli $conexion, string $tabla): bool
    {
        $tablaEscapada = $conexion->real_escape_string($tabla);
        $resultado = $conexion->query("SHOW TABLES LIKE '{$tablaEscapada}'");

        return $resultado instanceof mysqli_result && $resultado->num_rows > 0;
    }
}

if (!function_exists('columnaExiste')) {
    function columnaExiste(mysqli $conexion, string $tabla, string $columna): bool
    {
        if (!tablaExiste($conexion, $tabla)) {
            return false;
        }

        $tablaEscapada = $conexion->real_escape_string($tabla);
        $columnaEscapada = $conexion->real_escape_string($columna);
        $resultado = $conexion->query("SHOW COLUMNS FROM `{$tablaEscapada}` LIKE '{$columnaEscapada}'");

        return $resultado instanceof mysqli_result && $resultado->num_rows > 0;
    }
}

if (!function_exists('ejecutarDelete')) {
    function ejecutarDelete(mysqli $conexion, string $sql, string $types = '', array $params = []): int
    {
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('No se pudo preparar la consulta de eliminación.');
        }

        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new RuntimeException('Error al ejecutar eliminación: ' . $error);
        }

        $filas = $stmt->affected_rows;
        $stmt->close();

        return $filas;
    }
}

if (!function_exists('obtenerUsuarioObjetivoPorCliente')) {
    function obtenerUsuarioObjetivoPorCliente(mysqli $conexion, int $idCliente): ?int
    {
        if ($idCliente <= 0 || !tablaExiste($conexion, 'clientes')) {
            return null;
        }

        $stmt = $conexion->prepare('SELECT id_usuario FROM clientes WHERE id_cliente = ? LIMIT 1');
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $idCliente);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();

        return $fila ? (int)$fila['id_usuario'] : null;
    }
}

if (!function_exists('eliminarUsuarioEnCascada')) {
    function eliminarUsuarioEnCascada(mysqli $conexion, int $idUsuario): array
    {
        if ($idUsuario <= 0) {
            throw new InvalidArgumentException('ID de usuario inválido.');
        }

        $stmtUsuario = $conexion->prepare('SELECT id_usuario, nombre, correo FROM usuarios WHERE id_usuario = ? LIMIT 1');
        if (!$stmtUsuario) {
            throw new RuntimeException('No se pudo validar el usuario objetivo.');
        }

        $stmtUsuario->bind_param('i', $idUsuario);
        $stmtUsuario->execute();
        $resUsuario = $stmtUsuario->get_result();
        $usuario = $resUsuario ? $resUsuario->fetch_assoc() : null;
        $stmtUsuario->close();

        if (!$usuario) {
            throw new RuntimeException('El usuario no existe.');
        }

        $idCliente = null;
        if (tablaExiste($conexion, 'clientes')) {
            $stmtCliente = $conexion->prepare('SELECT id_cliente FROM clientes WHERE id_usuario = ? LIMIT 1');
            if (!$stmtCliente) {
                throw new RuntimeException('No se pudo obtener el cliente asociado.');
            }

            $stmtCliente->bind_param('i', $idUsuario);
            $stmtCliente->execute();
            $resCliente = $stmtCliente->get_result();
            $filaCliente = $resCliente ? $resCliente->fetch_assoc() : null;
            $stmtCliente->close();

            if ($filaCliente) {
                $idCliente = (int)$filaCliente['id_cliente'];
            }
        }

        $conexion->begin_transaction();

        try {
            if ($idCliente !== null) {
                if (tablaExiste($conexion, 'lista_deseos') && columnaExiste($conexion, 'lista_deseos', 'id_cliente')) {
                    ejecutarDelete($conexion, 'DELETE FROM lista_deseos WHERE id_cliente = ?', 'i', [$idCliente]);
                }

                if (tablaExiste($conexion, 'carrito_detalle') && tablaExiste($conexion, 'carritos')) {
                    ejecutarDelete(
                        $conexion,
                        'DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carritos WHERE id_cliente = ?)',
                        'i',
                        [$idCliente]
                    );
                }

                if (tablaExiste($conexion, 'historial_pedido') && tablaExiste($conexion, 'pedidos')) {
                    ejecutarDelete(
                        $conexion,
                        'DELETE FROM historial_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = ?)',
                        'i',
                        [$idCliente]
                    );
                }

                if (tablaExiste($conexion, 'detalle_pedido') && tablaExiste($conexion, 'pedidos')) {
                    ejecutarDelete(
                        $conexion,
                        'DELETE FROM detalle_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = ?)',
                        'i',
                        [$idCliente]
                    );
                }

                if (tablaExiste($conexion, 'pedidos')) {
                    ejecutarDelete($conexion, 'DELETE FROM pedidos WHERE id_cliente = ?', 'i', [$idCliente]);
                }

                if (tablaExiste($conexion, 'direcciones_cliente')) {
                    ejecutarDelete($conexion, 'DELETE FROM direcciones_cliente WHERE id_cliente = ?', 'i', [$idCliente]);
                }

                if (tablaExiste($conexion, 'carritos')) {
                    ejecutarDelete($conexion, 'DELETE FROM carritos WHERE id_cliente = ?', 'i', [$idCliente]);
                }

                if (tablaExiste($conexion, 'clientes')) {
                    ejecutarDelete($conexion, 'DELETE FROM clientes WHERE id_cliente = ?', 'i', [$idCliente]);
                }
            }

            if (tablaExiste($conexion, 'historial_pedido') && columnaExiste($conexion, 'historial_pedido', 'id_usuario')) {
                ejecutarDelete($conexion, 'DELETE FROM historial_pedido WHERE id_usuario = ?', 'i', [$idUsuario]);
            }

            if (tablaExiste($conexion, 'mensajes')) {
                $tieneIdUsuario = columnaExiste($conexion, 'mensajes', 'id_usuario');
                $tieneDestinatario = columnaExiste($conexion, 'mensajes', 'destinatario_id');

                if ($tieneIdUsuario && $tieneDestinatario) {
                    ejecutarDelete($conexion, 'DELETE FROM mensajes WHERE id_usuario = ? OR destinatario_id = ?', 'ii', [$idUsuario, $idUsuario]);
                } elseif ($tieneIdUsuario) {
                    ejecutarDelete($conexion, 'DELETE FROM mensajes WHERE id_usuario = ?', 'i', [$idUsuario]);
                } elseif ($tieneDestinatario) {
                    ejecutarDelete($conexion, 'DELETE FROM mensajes WHERE destinatario_id = ?', 'i', [$idUsuario]);
                }
            }

            $filasUsuarios = ejecutarDelete($conexion, 'DELETE FROM usuarios WHERE id_usuario = ?', 'i', [$idUsuario]);
            if ($filasUsuarios === 0) {
                throw new RuntimeException('No se pudo eliminar el usuario objetivo.');
            }

            $conexion->commit();

            return [
                'id_usuario' => (int)$usuario['id_usuario'],
                'id_cliente' => $idCliente,
                'nombre' => (string)$usuario['nombre'],
                'correo' => (string)$usuario['correo']
            ];
        } catch (Throwable $e) {
            $conexion->rollback();
            throw $e;
        }
    }
}
