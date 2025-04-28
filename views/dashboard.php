<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Taquería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }

        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
        }

        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
        }

        /* Loader styles */
        #loader {
            display: none;
            text-align: center;
            margin-top: 50px;
        }

        #loader img {
            width: 50px;
            height: 50px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-flex flex-column p-3">
                <h4 class="mb-4">🌮 Taquería</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#" class="nav-link"><i
                                class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li class="nav-item mb-2"><a href="pedidos.php" class="nav-link"><i class="bi bi-receipt me-2"></i>Pedidos</a>
                    </li>
                    <li class="nav-item mb-2"><a href="clientes.php" class="nav-link"><i class="bi bi-people me-2"></i>Clientes</a>
                    </li>
                    <li class="nav-item mb-2"><a href="productos.php" class="nav-link"><i
                                class="bi bi-box-seam me-2"></i>Productos</a></li>
                </ul>
                <hr class="text-light">
                <a href="#" class="btn btn-outline-light btn-sm mt-auto">Cerrar sesión</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4" data-aos="fade-right">¡Bienvenido al Panel!</h2>

                <!-- Loader -->
                <div id="loader">
                    <img src="https://cdn.jsdelivr.net/npm/spinkit@1.2.5/css/spinkit.min.css" alt="Cargando...">
                    <p>Cargando datos...</p>
                </div>

                <!-- Estadísticas -->
                <div class="d-flex justify-content-between gap-3 mb-4" id="stats-container" style="display: none;">
                    <div class="flex-fill" data-aos="fade-up" data-aos-delay="100">
                        <div class="card card-custom p-4 h-100">
                            <h5>Pedidos de hoy</h5>
                            <h2 id="pedidos-del-dia">0</h2>
                        </div>
                    </div>
                    <div class="flex-fill" data-aos="fade-up" data-aos-delay="200">
                        <div class="card card-custom p-4 h-100">
                            <h5>Ventas del día</h5>
                            <h2 id="ventas-del-dia">$0</h2>
                        </div>
                    </div>
                    <div class="flex-fill" data-aos="fade-up" data-aos-delay="300">
                        <div class="card card-custom p-4 h-100">
                            <h5>Tacos más vendidos</h5>
                            <h2 id="tacos-mas-vendidos">Ninguno</h2>
                        </div>
                    </div>
                </div>

                <!-- Tabla de pedidos recientes -->
                <div class="card card-custom p-4" data-aos="fade-up" id="recent-orders-container"
                    style="display: none;">
                    <h5 class="mb-3">Pedidos recientes</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="recent-orders-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los pedidos recientes se llenarán por AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        // Función para cargar los datos del Dashboard
        function cargarDashboard() {
            // Mostrar el loader mientras se cargan los datos
            document.getElementById('loader').style.display = 'block';

            // Hacer la solicitud AJAX para obtener los datos del dashboard
            fetch('../api/dashboard.php') // Asegúrate de que la ruta sea correcta
                .then(response => response.json())
                .then(data => {
                    // Esconder el loader
                    document.getElementById('loader').style.display = 'none';

                    // Mostrar los datos en el dashboard
                    document.getElementById('pedidos-del-dia').textContent = data.pedidosDelDia;
                    document.getElementById('ventas-del-dia').textContent = '$' + data.ventasDelDia;
                    document.getElementById('tacos-mas-vendidos').textContent = data.tacosMasVendidos;

                    // Llenar la tabla de pedidos recientes
                    const ordersTable = document.getElementById('recent-orders-table').getElementsByTagName('tbody')[0];
                    data.pedidosRecientes.forEach(order => {
                        const row = ordersTable.insertRow();
                        row.innerHTML = `
                            <td>${order.id}</td>
                            <td>${order.cliente}</td>
                            <td>${order.productos}</td>
                            <td>$${order.total}</td>
                            <td>${order.hora}</td>
                        `;
                    });

                    // Mostrar los contenedores de estadísticas y pedidos recientes
                    document.getElementById('stats-container').style.display = 'block';
                    document.getElementById('recent-orders-container').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error al cargar los datos:', error);
                    document.getElementById('loader').innerHTML = 'Error al cargar los datos.';
                });
        }

        // Cargar los datos cuando la página cargue
        window.onload = cargarDashboard;
    </script>
</body>

</html>