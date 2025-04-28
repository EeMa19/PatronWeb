<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Pedidos | Taquería</title>
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

    .status-badge {
      padding: 0.4rem 0.7rem;
      border-radius: 1rem;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-pendiente {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-preparacion {
      background-color: #cce5ff;
      color: #004085;
    }

    .status-entregado {
      background-color: #d4edda;
      color: #155724;
    }
  </style>
</head>

<body>

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0" data-aos="fade-right">Pedidos</h2>
      <button class="btn btn-outline-primary" data-aos="fade-left" id="btn-actualizar"><i
          class="bi bi-arrow-clockwise"></i> Actualizar</button>
    </div>

    <!-- Tabla de pedidos -->
    <div class="card card-custom p-4" data-aos="fade-up">
      <div class="table-responsive">
        <table class="table align-middle" id="tabla-pedidos">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Hora</th>
              <th>Productos</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Los pedidos se cargarán aquí con AJAX -->
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

    // Función para cargar los pedidos con AJAX (ahora usa POST)
    function cargarPedidos() {
      $.ajax({
        url: '../Controllers/PedidosController.php',
        method: 'POST',
        data: {
          accion: 'index' // Aseguramos que se ejecute la acción correcta
        },
        success: function (response) {
          const pedidos = JSON.parse(response);
          console.log(pedidos);

          let filas = '';
          pedidos.forEach(pedido => {
            filas += `
            <tr>
              <td>#${pedido.id}</td>
              <td>${pedido.cliente}</td>
              <td>${pedido.hora}</td>
              <td>${pedido.productos}</td>
              <td>$${pedido.total}</td>
              <td><span class="status-badge status-${pedido.estado.toLowerCase()}">${pedido.estado}</span></td>
              <td>
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                ${pedido.estado === 'Pendiente' ?
                `<button class="btn btn-sm btn-outline-success cambiar-estado" data-id="${pedido.id}" data-estado="Entregado"><i class="bi bi-check-lg"></i></button>` :
                ''}
              </td>
            </tr>
          `;
          });
          $('#tabla-pedidos tbody').html(filas);
        },
        error: function () {
          alert('Hubo un error al cargar los pedidos.');
        }
      });
    }

    // Cargar los pedidos al cargar la página
    $(document).ready(function () {
      cargarPedidos();

      // Actualizar los pedidos manualmente
      $('#btn-actualizar').click(function () {
        cargarPedidos();
      });

      // Cambiar el estado de un pedido
      $(document).on('click', '.cambiar-estado', function () {
        const id = $(this).data('id');
        const estado = $(this).data('estado');

        $.ajax({
          url: '../Controllers/PedidosController.php',
          method: 'POST',
          data: {
            accion: 'cambiarEstado', // Aseguramos que se ejecute la acción correcta
            id: id,
            estado: estado
          },
          success: function (response) {
            const resultado = JSON.parse(response);
            if (resultado.success) {
              alert('Estado cambiado');
              cargarPedidos();  // Recargar la tabla
            } else {
              alert('Hubo un error al cambiar el estado');
            }
          },
          error: function () {
            alert('Hubo un error al cambiar el estado');
          }
        });
      });
    });
  </script>

</body>

</html>
