<?php

include('conexion.php');

$mensaje = '';
$error = '';
$productos = [];
$categorias = [];
$gastos = [];

// OBTENER PRODUCTOS Y CATEGORÍAS
$queryProductos = "SELECT nombre, categoria FROM productos ORDER BY nombre ASC";
$resultadoProductos = pg_query($conn, $queryProductos);

if ($resultadoProductos) {

    while ($row = pg_fetch_assoc($resultadoProductos)) {

        $productos[] = $row;

        $categoria = trim($row['categoria']);

        if ($categoria !== '' && !in_array($categoria, $categorias)) {
            $categorias[] = $categoria;
        }
    }

    sort($categorias);
}

// REGISTRAR GASTO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $producto = trim($_POST['producto'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $monto = trim($_POST['monto'] ?? '');

    // VALIDACIONES

    if (
        $usuario === '' ||
        $producto === '' ||
        $categoria === '' ||
        $monto === ''
    ) {

        $error = "Todos los campos son obligatorios.";

    } elseif (!is_numeric($monto) || floatval($monto) <= 0) {

        $error = "El monto debe ser un número válido mayor que 0.";

    } else {

        // CALCULAR SALDO DISPONIBLE

        $saldoInicial = 1000;

        $totalGastado = 0;

        foreach ($gastos as $gasto) {
            $totalGastado += (float)$gasto['monto'];
        }

        $saldoDisponible = $saldoInicial - $totalGastado;

        // VALIDAR SALDO

        if ((float)$monto > $saldoDisponible) {

            $error = "Saldo insuficiente para realizar este gasto.";

        } else {

            // LIMPIAR DATOS
            $usuario = pg_escape_string($conn, $usuario);
            $producto = pg_escape_string($conn, $producto);
            $categoria = pg_escape_string($conn, $categoria);

            $monto = number_format((float)$monto, 2, '.', '');

            // INSERTAR GASTO
            $queryInsert = "
                INSERT INTO gastos (
                    usuario,
                    producto,
                    categoria,
                    monto
                )
                VALUES (
                    '$usuario',
                    '$producto',
                    '$categoria',
                    $monto
                )
            ";

            $resultadoInsert = pg_query($conn, $queryInsert);

            if ($resultadoInsert) {

                $mensaje = "Gasto registrado correctamente.";

            } else {

                $error = "Error al registrar el gasto.";
            }
        }
    }
}

// CONSULTAR GASTOS
$queryGastos = "
    SELECT
        usuario,
        producto,
        categoria,
        monto,
        fecha
    FROM gastos
    ORDER BY fecha DESC
";

$resultadoGastos = pg_query($conn, $queryGastos);

if ($resultadoGastos) {

    while ($row = pg_fetch_assoc($resultadoGastos)) {
        $gastos[] = $row;
    }
}

// FUNCIÓN DE SEGURIDAD
function escape($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TamalShop</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <header>

        <h1>TamalShop</h1>
        <p>Control inteligente de gastos</p>

    </header>

    <main>

        <!-- DASHBOARD -->

        <?php

        // SALDO INICIAL SIMULADO
        // Luego esto debe venir desde la API GET

        $saldoInicial = 1000;

        // TOTAL GASTADO
        $totalGastado = 0;

        // TOTAL TAMALBITS
        $totalTamalbits = 0;

        foreach ($gastos as $gasto) {

            $monto = (float)$gasto['monto'];

            $totalGastado += $monto;

            // REGLA TAMALBITS
            if (
                strtolower(trim($gasto['producto'])) === 'orejas de pollo'
            ) {

                $totalTamalbits += floor($monto / 10);
            }
        }

        // SALDO DISPONIBLE
        $saldoDisponible = $saldoInicial - $totalGastado;

        // EVITAR NEGATIVOS
        if ($saldoDisponible < 0) {
            $saldoDisponible = 0;
        }

        ?>

        <section class="dashboard">

            <!-- SALDO DISPONIBLE -->

            <div class="card">

                <h2>Saldo disponible</h2>

                <h3>

                    $
                    <?php echo number_format($saldoDisponible, 2); ?>

                </h3>

            </div>

            <!-- TOTAL GASTADO -->

            <div class="card">

                <h2>Total gastado</h2>

                <h3>

                    $
                    <?php echo number_format($totalGastado, 2); ?>

                </h3>

            </div>

            <!-- TAMALBITS -->

            <div class="card">

                <h2>Total Tamalbits</h2>

                <h3>

                    <?php echo $totalTamalbits; ?>

                </h3>

            </div>

        </section>

        <!-- FORMULARIO -->

        <section class="formulario">

            <h2>Registrar gasto</h2>

            <?php if ($mensaje !== ''): ?>

                <div class="mensaje-exito">
                    <?php echo escape($mensaje); ?>
                </div>

            <?php endif; ?>

            <?php if ($error !== ''): ?>

                <div class="mensaje-error">
                    <?php echo escape($error); ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-grid">

                    <!-- USUARIO -->

                    <input
                        type="text"
                        name="usuario"
                        placeholder="Nombre del usuario"
                        required
                    >

                    <!-- PRODUCTOS -->

                    <select name="producto" required>

                        <option value="">
                            Seleccione un producto
                        </option>

                        <?php foreach ($productos as $producto): ?>

                            <option value="<?php echo escape($producto['nombre']); ?>">

                                <?php echo escape($producto['nombre']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <!-- MONTO -->

                    <input
                        type="number"
                        name="monto"
                        placeholder="Monto"
                        step="0.01"
                        min="1"
                        required
                    >

                    <!-- CATEGORÍA -->

                    <select name="categoria" required>

                        <option value="">
                            Seleccione una categoría
                        </option>

                        <?php foreach ($categorias as $categoria): ?>

                            <option value="<?php echo escape($categoria); ?>">

                                <?php echo escape($categoria); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <button type="submit">
                    Guardar gasto
                </button>

            </form>

        </section>

        <!-- TABLA -->

        <section class="tabla-section">

            <h2>Gastos registrados</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Monto</th>
                            <th>Fecha</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($gastos) === 0): ?>

                            <tr>

                                <td colspan="5">
                                    No hay gastos registrados.
                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($gastos as $gasto): ?>

                                <tr>

                                    <td>
                                        <?php echo escape($gasto['usuario']); ?>
                                    </td>

                                    <td>
                                        <?php echo escape($gasto['producto']); ?>
                                    </td>

                                    <td>
                                        <?php echo escape($gasto['categoria']); ?>
                                    </td>

                                    <td>

                                        $
                                        <?php echo number_format((float)$gasto['monto'], 2); ?>

                                    </td>

                                    <td>
                                        <?php echo escape($gasto['fecha']); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>