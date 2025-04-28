<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Clientes | Taquería</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }

        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 data-aos="fade-right">Clientes</h2>
            <button class="btn btn-outline-primary" data-aos="fade-left" id="btn-actualizar"><i
                    class="bi bi-arrow-clockwise"></i> Actualizar</button>
        </div>

        <!-- Buscador -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-md-6 mx-auto">
                <input type="text" class="form-control" placeholder="Buscar por nombre o teléfono..."
                    id="filtro-busqueda">
            </div>
        </div>

        <!-- Tabla de clientes -->
        <div class="card card-custom p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle" id="tabla-clientes">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Pedidos</th>
                            <th>Total gastado</th>
                            <th>Última compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarán los datos con AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        AOS.init();

        // Función para cargar los clientes
        function cargarClientes() {
            const busqueda = $('#filtro-busqueda').val();

            $.ajax({
                url: '../Controllers/ClientesController.php',
                method: 'GET',
                data: {
                    accion: 'index',
                    busqueda: busqueda
                },
                success: function (response) {
                    const clientes = JSON.parse(response);
                    let filas = '';
                    clientes.forEach(cliente => {
                        filas += `
                    <tr>
                        <td><img src="https://randomuser.me/api/portraits/men/32.jpg" class="avatar" alt="${cliente.nombre}"></td>
                        <td>${cliente.nombre}</td>
                        <td>${cliente.telefono}</td>
                        <td>${cliente.pedidos_count}</td>
                        <td>$${cliente.total_gastado}</td>
                        <td>${cliente.ultima_compra}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger eliminar-cliente" data-id="${cliente.id}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
                    });
                    $('#tabla-clientes tbody').html(filas);
                },
                error: function () {
                    alert('Hubo un error al cargar los clientes.');
                }
            });
        }

        $(document).ready(function () {
            cargarClientes();

            $('#btn-actualizar').click(function () {
                cargarClientes();
            });

            $('#filtro-busqueda').on('input', function () {
                cargarClientes();
            });

            // Eliminar cliente
            $('#tabla-clientes').on('click', '.eliminar-cliente', function () {
                const id = $(this).data('id');

                if (confirm('¿Estás seguro de eliminar este cliente?')) {
                    $.ajax({
                        url: '../Controllers/ClientesController.php',
                        method: 'POST',
                        data: {
                            accion: 'eliminar',
                            id: id
                        },
                        success: function (response) {
                            const result = JSON.parse(response);
                            if (result.success) {
                                alert(result.message);
                                cargarClientes();
                            } else {
                                alert(result.message);
                            }
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>