// Adicione este código acima da tag </body>
<script type="text/javascript">
  jQuery(document).ready(function($) {
        // Inicia o ajax, carregando o cadastra.php
        var url = '<?php echo deonletter; ?>save-post.php';

        // Insere uma função no formulário, para quando ele é submetido
        $('#ajax_form').submit(function(e){
        var dados = $(this).serialize();
        $.ajax({
          type: "POST",
          url: url,
          data: dados,
          success: function(data)
            {             
              $(".resultado_newsletter").html(data);
            }
          });

        // Não permite que a página seja carregada novamente.
        return false;
        });

  });
</script>
