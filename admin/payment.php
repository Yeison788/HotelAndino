<?php
    session_start();
    include '../config.php';
    require_once __DIR__ . '/includes/admin_bootstrap.php';

    ensureEmpStructure($conn);
    ensureRoomRates($conn);
    admin_refresh_session($conn, $_SESSION['adminmail'] ?? '');
    admin_require_permission('pagos');

    $totals = [
        'count' => 0,
        'room' => 0,
        'bed' => 0,
        'meal' => 0,
        'final' => 0,
    ];

    $paymanttablesql = "SELECT * FROM payment ORDER BY id DESC";
    $paymantresult = mysqli_query($conn, $paymanttablesql);
    if ($paymantresult) {
        while ($row = mysqli_fetch_assoc($paymantresult)) {
            $totals['count']++;
            $totals['room'] += (float)$row['roomtotal'];
            $totals['bed']  += (float)$row['bedtotal'];
            $totals['meal'] += (float)$row['mealtotal'];
            $totals['final'] += (float)$row['finaltotal'];
        }
        mysqli_data_seek($paymantresult, 0);
    }
    $totals['avg'] = $totals['count'] > 0 ? $totals['final'] / $totals['count'] : 0;
    $hasPayments = $paymantresult && $totals['count'] > 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Andino - Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Pagos y facturación</h1>
                <p class="text-muted mb-0">Control de los cobros confirmados para huéspedes y reservas.</p>
            </div>
            <div class="text-md-end">
                <span class="text-muted small text-uppercase">Total facturado</span>
                <div class="fs-4 fw-semibold">COP <?php echo number_format($totals['final'], 0, ',', '.'); ?></div>
            </div>
        </div>

        <?php if ($hasPayments): ?>
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted text-uppercase small mb-2">Facturas registradas</div>
                            <div class="h4 mb-0"><?php echo $totals['count']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted text-uppercase small mb-2">Ingresos por habitaciones</div>
                            <div class="h5 mb-0">COP <?php echo number_format($totals['room'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted text-uppercase small mb-2">Adicionales (camas y comidas)</div>
                            <div class="h5 mb-0">COP <?php echo number_format($totals['bed'] + $totals['meal'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted text-uppercase small mb-2">Ticket promedio</div>
                            <div class="h5 mb-0">COP <?php echo number_format($totals['avg'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                    <div>
                        <h2 class="h5 mb-1">Historial de pagos</h2>
                        <p class="text-muted mb-0">Últimos movimientos registrados en el sistema.</p>
                    </div>
                    <div class="ms-lg-auto">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control" id="payments-search" placeholder="Buscar por nombre, habitación o correo" onkeyup="filterPayments()">
                        </div>
                    </div>
                </div>

                <?php if ($hasPayments && $paymantresult): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table-data">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Huésped</th>
                                    <th scope="col">Tipo de habitación</th>
                                    <th scope="col">Cama</th>
                                    <th scope="col">Ingreso</th>
                                    <th scope="col">Salida</th>
                                    <th scope="col">Días</th>
                                    <th scope="col">Habitaciones</th>
                                    <th scope="col">Plan de comida</th>
                                    <th scope="col" class="text-end">Hab.</th>
                                    <th scope="col" class="text-end">Cama</th>
                                    <th scope="col" class="text-end">Comidas</th>
                                    <th scope="col" class="text-end">Total</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($res = mysqli_fetch_assoc($paymantresult)): ?>
                                    <tr>
                                        <td><?php echo (int)$res['id']; ?></td>
                                        <td><?php echo htmlspecialchars($res['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($res['RoomType']); ?></td>
                                        <td><?php echo htmlspecialchars($res['Bed']); ?></td>
                                        <td><?php echo htmlspecialchars($res['cin']); ?></td>
                                        <td><?php echo htmlspecialchars($res['cout']); ?></td>
                                        <td><?php echo (int)$res['noofdays']; ?></td>
                                        <td><?php echo (int)$res['NoofRoom']; ?></td>
                                        <td><?php echo htmlspecialchars($res['meal']); ?></td>
                                        <td class="text-end">COP <?php echo number_format($res['roomtotal'], 0, ',', '.'); ?></td>
                                        <td class="text-end">COP <?php echo number_format($res['bedtotal'], 0, ',', '.'); ?></td>
                                        <td class="text-end">COP <?php echo number_format($res['mealtotal'], 0, ',', '.'); ?></td>
                                        <td class="text-end fw-semibold">COP <?php echo number_format($res['finaltotal'], 0, ',', '.'); ?></td>
                                        <td class="text-nowrap">
                                            <a class="btn btn-sm btn-primary" href="invoiceprint.php?id=<?php echo (int)$res['id']; ?>"><i class="fa-solid fa-print"></i> Imprimir</a>
                                            <a class="btn btn-sm btn-outline-danger" href="paymantdelete.php?id=<?php echo (int)$res['id']; ?>">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        No se han registrado pagos todavía.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('payments-search');
        function filterPayments() {
            if (!searchInput) return;
            const filter = searchInput.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#table-data tbody tr');
            rows.forEach((row) => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
