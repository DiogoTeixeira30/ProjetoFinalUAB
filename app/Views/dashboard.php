<div class="container">
  <div class="row">
    <div class="col-12">
      <h2>Ficheiros Disponíveis</h2>
      <ul>
        <?php if (!empty($files)): ?>
          <?php foreach ($files as $file): ?>
            <?php
            $filePath = base_url('uploads/' . $file);
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
            ?>
            <li>
              <!-- Link para abrir o modal com a pré-visualização -->
              <a href="#" class="file-link" onclick="openModal('<?= esc($file) ?>', '<?= esc($fileExtension) ?>'); return false;">
                <?= esc($file) ?>
              </a>
              <button class="btn btn-primary btn-sign" onclick="assinarDocumento('<?= esc($file) ?>'); return false;">
                Assinar Digitalmente
              </button>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Não há ficheiros na pasta.</p>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>



<!-- Script para enviar dados para o controller -->
<script>
  function assinarDocumento(file) {
    // Submeter os dados via AJAX
    const formData = new FormData();
    formData.append('file', file);

    fetch('/assinatura/processarAssinatura', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      alert('Assinatura digital concluída: ' + data);
    })
    .catch(error => {
      alert('Erro ao assinar o documento: ' + error);
    });
  }
</script>


<!-- Modal para a pré-visualização -->
<div id="filePreviewModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <div id="modalPreviewContent"></div>
  </div>
</div>

<!-- JavaScript para controlar o modal -->
<script>
  function openModal(file, extension) {
    var filePath = '<?= base_url('uploads') ?>/' + file;
    var modal = document.getElementById('filePreviewModal');
    var modalContent = document.getElementById('modalPreviewContent');

    // Limpa o conteúdo anterior
    modalContent.innerHTML = '';

    // Exibe o conteúdo conforme o tipo de ficheiro
    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
      var img = document.createElement('img');
      img.src = filePath;
      img.style.maxWidth = '100%'; // Ajusta o tamanho máximo da imagem
      img.style.maxHeight = '500px'; // Ajusta a altura máxima da imagem
      modalContent.appendChild(img);
    } else if (extension === 'pdf') {
      var iframe = document.createElement('iframe');
      iframe.src = filePath;
      iframe.style.width = '100%'; // Ajusta a largura do iframe
      iframe.style.height = '500px'; // Ajusta a altura do iframe
      iframe.frameBorder = '0';
      modalContent.appendChild(iframe);
    } else {
      var link = document.createElement('a');
      link.href = filePath;
      link.target = '_blank';
      link.innerText = 'Abrir ' + file;
      modalContent.appendChild(link);
    }

    // Abre o modal
    modal.style.display = 'block';
  }

  function closeModal() {
    var modal = document.getElementById('filePreviewModal');
    modal.style.display = 'none';
  }

  // Fechar o modal quando o utilizador clicar fora do conteúdo
  window.onclick = function(event) {
    var modal = document.getElementById('filePreviewModal');
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  }
</script>