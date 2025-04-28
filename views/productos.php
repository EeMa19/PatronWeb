<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos | Taquería</title>
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
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 data-aos="fade-right">Productos</h2>
            <div>
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="bi bi-plus-circle"></i> Nuevo Producto
                </button>
                <button class="btn btn-outline-primary" id="btn-recargar">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
        </div>

        <!-- Buscador -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-md-6 mx-auto">
                <input type="text" class="form-control" id="buscar-producto" placeholder="Buscar por nombre o tipo...">
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="card card-custom p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Tipo</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-productos">
                        <!-- Se llena dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para agregar producto -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formAgregarProducto">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarLabel">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" name="precio" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <input type="text" class="form-control" name="tipo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        function cargarProductos(busqueda = '') {
            const formData = new FormData();
            formData.append('accion', 'index');
            formData.append('busqueda', busqueda);

            fetch('../Controllers/ProductoController.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(productos => {
                    const tabla = document.getElementById('tabla-productos');
                    tabla.innerHTML = '';

                    if (productos.length > 0) {
                        productos.forEach(p => {
                            tabla.innerHTML += `
                                <tr>
                                    <td>${p.nombre}</td>
                                    <td>$${parseFloat(p.precio).toFixed(2)}</td>
                                    <td>${p.tipo}</td>
                                    <td>${new Date(p.created_at).toLocaleDateString()}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(${p.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        tabla.innerHTML = `<tr><td colspan="5" class="text-center">No hay productos</td></tr>`;
                    }
                });
        }

        function eliminarProducto(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('accion', 'delete');
                    formData.append('id', id);

                    fetch('../Controllers/ProductoController.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(r => {
                            if (r.success) {
                                cargarProductos();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    showConfirmButton: false,
                                    timer: 1200
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: r.message || 'No se pudo eliminar el producto.'
                                });
                            }
                        });
                }
            });
        }


        document.getElementById('buscar-producto').addEventListener('input', (e) => {
            cargarProductos(e.target.value);
        });

        document.getElementById('btn-recargar').addEventListener('click', () => {
            cargarProductos();
        });

        // Guardar producto desde el modal
        document.getElementById('formAgregarProducto').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('accion', 'store');

            fetch('../Controllers/ProductoController.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(r => {
                    if (r.success) {
                        this.reset();
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregar'));
                        modal.hide();
                        cargarProductos();

                        Swal.fire({
                            icon: 'success',
                            title: 'Producto guardado',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: r.message || 'Hubo un error al guardar el producto.'
                        });
                    }
                });

        });

        cargarProductos();
    </script>

</body>

</html>