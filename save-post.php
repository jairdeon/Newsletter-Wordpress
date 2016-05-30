<?php
require_once('../../../wp-config.php');

// Cria Variavel Email
$email = $_POST['email'];

// Cria o atalho para verificar se o email digitado já existe no banco de dados
$term = term_exists($email, 'email');

// Se o email existir, exibe a mensagem de erro
if ($term !== 0 && $term !== null) {
    echo '<div class="alert alert-danger col-md-11">Este email já está cadastrado.</div>';
}

// Se o email não for preencido, exibe a mensagem de erro.
elseif ($email == '') {
    echo '<div class="alert alert-danger col-md-11">Preencha todos os campos.</div>';
}

// Caso contrário, continua o código
else {
    
    // Cria as propriedades da publicação
    $create_post = array(
        'post_title' => $_POST['nome'], // Título do Post
        'post_status' => 'publish', // Será publicado automaticamente
        'post_author' => 1, // Dará que quem postou foi o administrador
        'post_type' => 'newsletter' // Custom Post Type que será inserido
    );
    
    // Detecta o id da publicação
    $post_id = wp_insert_post($create_post);
    //add_post_meta($post_id, 'my_meta_box_text', 'testeee4', true);
    
    // Insere o email digitado dentro da publicação, na taxonomy email
    wp_set_object_terms($post_id, $_POST['email'], 'email', true);
    
    // Cria a metabox (Embora não seja utilizada no back-end, nas próximas atualizações cuidarei disto.)
    add_action('add_meta_boxes', 'deon_newsletter_metabox');
    function deon_newsletter_metabox()
    {
        add_meta_box('deon_newsletter_id', 'Newsletter', 'deon_newsletter_fields', 'newsletter', 'normal', 'high');
    }
    
    
    function deon_newsletter_fields()
    {
        global $post;
        $values = get_post_custom($post->ID);
        
        // Aqui verifica os campos que estão no back end
        // Exemplo 1 -> $text = isset( $values['my_meta_box_text'] ) ? $values['my_meta_box_text'] : '';
        
        wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
?>

<!-- Aqui são as metaboxs que apareceriam no back end e os exibiriam. -->
<!-- Exemplo 1 -> <p><label for="my_meta_box_text">Text Label</label><input type="text" name="my_meta_box_text" id="my_meta_box_text" value="<?php
        echo $text[0];
?>" /></p> -->

         <?php
    }
    
    // Configurações para salvar a publicação
    add_action('save_post', 'deon_newsletter_save_fields');
    function deon_newsletter_save_fields($post_id)
    {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        
        // if our nonce isn't there, or we can't verify it, bail
        if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'my_meta_box_nonce'))
            return;
        
        // Verifica qual a capacitação de quem está publicando
        if (!current_user_can('edit_post'))
            return;
        
        // Abaixo seria para validar o campo da metabox no back end
        // Exemplo 1 -> // if(isset($_POST['my_meta_box_text'])) update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed));
    }
    
    // Exibe a mensagem cadastrado com sucesso abaixo da newsletter.
    echo '<div class="alert alert-success col-md-11">Email cadastrado com sucesso.</div>';
}
?>