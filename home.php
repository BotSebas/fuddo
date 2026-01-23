<?php
include 'includes/auth.php';
include 'includes/url.php';
include_once 'lang/idiomas.php';
include 'includes/menu.php';
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo $home_titulo; ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo $home_titulo; ?></a></li>
              <li class="breadcrumb-item active"><?php echo $home_titulo; ?></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Welcome Message -->
        <div class="row">
          <div class="col-12">
            <div class="card card-fuddo">
              <div class="card-body">
                <h3 style="color: #27ae60;">
                  <i class="fas fa-utensils mr-2"></i>
                  <?php echo $home_bienvenida; ?>, <?php echo $_SESSION['nombre']; ?>! 👋
                </h3>
                <p class="lead mb-3"><?php echo $home_subtitulo; ?></p>
                
                <div class="row">
                  <div class="col-md-8">
                    <p style="font-size: 1.05em; line-height: 1.8;">
                      <?php echo $home_descripcion; ?>
                    </p>
                    
                    <div class="row mt-4">
                      <div class="col-md-6">
                        <div class="info-box info-box-fuddo">
                          <span class="info-box-icon">
                            <i class="fas fa-check-circle fuddo-icon" style="font-size: 2em;"></i>
                          </span>
                          <div class="info-box-content">
                            <span class="info-box-text"><strong><?php echo $home_control_total; ?></strong></span>
                            <span class="info-box-number" style="font-size: 0.9em;"><?php echo $home_control_desc; ?></span>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="info-box info-box-fuddo">
                          <span class="info-box-icon">
                            <i class="fas fa-bolt fuddo-icon" style="font-size: 2em;"></i>
                          </span>
                          <div class="info-box-content">
                            <span class="info-box-text"><strong><?php echo $home_rapido_simple; ?></strong></span>
                            <span class="info-box-number" style="font-size: 0.9em;"><?php echo $home_rapido_desc; ?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <p class="mt-3 mb-0 text-muted-fuddo">
                      <i class="fas fa-lightbulb mr-1 fuddo-icon"></i>
                      <em><?php echo $home_creado_para; ?></em>
                    </p>
                  </div>
                  
                  <div class="col-md-4 text-center">
                    <img src="assets/img/logo-fuddohorizontal.png" alt="FUDDO" class="dashboard-image">
                    
                    <div class="mt-4">
                      <a href="mesas/mesas.php" class="btn btn-fuddo btn-lg btn-block">
                        <i class="fas fa-chair mr-2"></i> <?php echo $home_ir_mesas; ?>
                      </a>
                      <a href="productos/productos.php" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-box mr-2"></i> <?php echo $home_gestionar_productos; ?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->

          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

<!-- Overlay oscuro (solo visible cuando el chat está abierto en móviles) -->
<div id="chatOverlay" onclick="toggleChat()"></div>

<!-- Chat Widget -->
<div id="chatWidget">
  
  <!-- Header del chat -->
  <div class="chat-header">
    <div class="chat-header-content">
      <div class="chat-avatar">
        <img src="assets/img/chatbot-fuddo.png" alt="FUDDO Bot" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div>
        <div class="chat-title"><?php echo $chat_titulo; ?></div>
        <div class="chat-subtitle"><?php echo $chat_subtitulo; ?></div>
      </div>
    </div>
    <button onclick="toggleChat()" class="chat-close-btn">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <!-- Mensajes del chat -->
  <div id="chatMessages">
    <!-- Los mensajes se agregarán aquí dinámicamente -->
  </div>

  <!-- Footer con powered by -->
  <div class="chat-footer">
    <i class="fas fa-utensils fuddo-icon"></i> <?php echo $chat_powered; ?>
  </div>
</div>

<!-- Botón flotante de ayuda -->
<button id="helpButton" onclick="toggleChat()" class="btn btn-lg">
  <img src="assets/img/chatbot-fuddo.png" alt="Chat" style="width: 100%; height: 100%; object-fit: cover;">
</button>

<script>
let chatOpen = false;
const chatData = {
  welcome: {
    message: <?php echo json_encode($chat_bienvenida); ?>,
    options: [
      { text: <?php echo json_encode($chat_gestion_mesas); ?>, next: "mesas" },
      { text: <?php echo json_encode($chat_manejo_pedidos); ?>, next: "pedidos" },
      { text: <?php echo json_encode($chat_productos_inventario); ?>, next: "productos" },
      { text: <?php echo json_encode($chat_cerrar_cuentas); ?>, next: "cuentas" }
    ]
  },
  mesas: {
    message: <?php echo json_encode($chat_mesas_pregunta); ?>,
    options: [
      { text: <?php echo json_encode($chat_crear_mesa); ?>, next: "crear_mesa" },
      { text: <?php echo json_encode($chat_ver_estado); ?>, next: "estado_mesas" },
      { text: <?php echo json_encode($chat_eliminar_mesa); ?>, next: "eliminar_mesa" },
      { text: "<?php echo $home_volver_inicio; ?>", next: "welcome" }
    ]
  },
  crear_mesa: {
    message: <?php echo json_encode($chat_crear_mesa_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_mesas); ?>, next: "mesas" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  estado_mesas: {
    message: <?php echo json_encode($chat_estado_mesas_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_mesas); ?>, next: "mesas" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  eliminar_mesa: {
    message: <?php echo json_encode($chat_eliminar_mesa_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_mesas); ?>, next: "mesas" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  pedidos: {
    message: <?php echo json_encode($chat_pedidos_pregunta); ?>,
    options: [
      { text: <?php echo json_encode($chat_agregar_productos); ?>, next: "agregar_productos" },
      { text: <?php echo json_encode($chat_eliminar_productos); ?>, next: "eliminar_productos" },
      { text: <?php echo json_encode($chat_cancelar_servicio); ?>, next: "cancelar_servicio" },
      { text: "<?php echo $home_volver_inicio; ?>", next: "welcome" }
    ]
  },
  agregar_productos: {
    message: <?php echo json_encode($chat_agregar_productos_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_pedidos); ?>, next: "pedidos" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  eliminar_productos: {
    message: <?php echo json_encode($chat_eliminar_productos_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_pedidos); ?>, next: "pedidos" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  cancelar_servicio: {
    message: <?php echo json_encode($chat_cancelar_servicio_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_pedidos); ?>, next: "pedidos" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  productos: {
    message: <?php echo json_encode($chat_productos_pregunta); ?>,
    options: [
      { text: <?php echo json_encode($chat_crear_producto); ?>, next: "crear_producto" },
      { text: <?php echo json_encode($chat_editar_producto); ?>, next: "editar_producto" },
      { text: <?php echo json_encode($chat_toggle_producto); ?>, next: "toggle_producto" },
      { text: "<?php echo $home_volver_inicio; ?>", next: "welcome" }
    ]
  },
  crear_producto: {
    message: <?php echo json_encode($chat_crear_producto_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_productos); ?>, next: "productos" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  editar_producto: {
    message: <?php echo json_encode($chat_editar_producto_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_productos); ?>, next: "productos" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  toggle_producto: {
    message: <?php echo json_encode($chat_toggle_producto_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_productos); ?>, next: "productos" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  cuentas: {
    message: <?php echo json_encode($chat_cuentas_pregunta); ?>,
    options: [
      { text: <?php echo json_encode($chat_proceso_cierre); ?>, next: "proceso_cierre" },
      { text: <?php echo json_encode($chat_calcular_cambio); ?>, next: "calcular_cambio" },
      { text: <?php echo json_encode($chat_historial); ?>, next: "historial" },
      { text: "<?php echo $home_volver_inicio; ?>", next: "welcome" }
    ]
  },
  proceso_cierre: {
    message: <?php echo json_encode($chat_proceso_cierre_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_cuentas); ?>, next: "cuentas" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  calcular_cambio: {
    message: <?php echo json_encode($chat_calcular_cambio_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_cuentas); ?>, next: "cuentas" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  },
  historial: {
    message: <?php echo json_encode($chat_historial_inst); ?>,
    options: [
      { text: <?php echo json_encode($chat_volver_cuentas); ?>, next: "cuentas" },
      { text: <?php echo json_encode($chat_menu_principal); ?>, next: "welcome" }
    ]
  }
};

function toggleChat() {
  chatOpen = !chatOpen;
  const widget = document.getElementById('chatWidget');
  const button = document.getElementById('helpButton');
  const overlay = document.getElementById('chatOverlay');
  
  if (chatOpen) {
    widget.style.display = 'flex';
    overlay.style.display = 'block';
    button.innerHTML = '<i class="fas fa-times" style="font-size: 24px;"></i>';
    button.style.padding = '0';
    if (document.getElementById('chatMessages').children.length === 0) {
      showMessage('welcome');
    }
  } else {
    widget.style.display = 'none';
    overlay.style.display = 'none';
    button.innerHTML = '<img src="assets/img/chatbot-fuddo.png" alt="Chat" style="width: 100%; height: 100%; object-fit: cover;">';
    button.style.padding = '0';
  }
}

function showMessage(key) {
  const messagesContainer = document.getElementById('chatMessages');
  const data = chatData[key];
  
  // Mostrar indicador de escritura
  const typingDiv = document.createElement('div');
  typingDiv.className = 'typing-indicator';
  typingDiv.innerHTML = '<span></span><span></span><span></span>';
  messagesContainer.appendChild(typingDiv);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
  
  setTimeout(() => {
    // Remover indicador
    typingDiv.remove();
    
    // Agregar mensaje del bot
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-bubble';
    messageDiv.innerHTML = data.message.replace(/\n/g, '<br>');
    messagesContainer.appendChild(messageDiv);
    
    // Agregar opciones
    if (data.options) {
      const optionsDiv = document.createElement('div');
      optionsDiv.style.marginTop = '10px';
      data.options.forEach(option => {
        const btn = document.createElement('button');
        btn.className = 'chat-option';
        btn.textContent = option.text;
        btn.onclick = () => handleOptionClick(option.next);
        optionsDiv.appendChild(btn);
      });
      messagesContainer.appendChild(optionsDiv);
    }
    
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }, 800);
}

function handleOptionClick(next) {
  // Si vuelve al inicio, limpiar todo el historial
  if (next === 'welcome') {
    document.getElementById('chatMessages').innerHTML = '';
  }
  showMessage(next);
}

// Animación del botón
setInterval(function() {
  if (!chatOpen) {
    const btn = document.getElementById('helpButton');
    btn.style.transform = 'scale(1.1)';
    setTimeout(() => btn.style.transform = 'scale(1)', 200);
  }
}, 4000);
</script>

<?php
include 'includes/footer.php';
?>
