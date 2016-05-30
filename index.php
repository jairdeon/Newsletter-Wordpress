<?php
/**
 * Plugin Name: Sakey - Newsletter
 * Plugin URI: http://www.sakey.com.br/
 * Description: Plugin simples para Newsletter.
 * Version: 1.0.0
 * Author: Jair Deon
 * Author URI: http://www.sakey.com.br/
 * License: GPL2
 */

// Cria o Custom Post Type - Newsletter
define('deonletter', plugin_dir_url(__FILE__));
function deon_newsletter()
{
    $labels = array(
        'name' => _x('Newsletter', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Newsletter', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Newsletter', 'text_domain'),
        'parent_item_colon' => __('Newsletter Semelhantes', 'text_domain'),
        'all_items' => __('Exibir Newsletter', 'text_domain'),
        'view_item' => __('Ver Newsletter', 'text_domain'),
        'add_new_item' => __('Criar Cadastro', 'text_domain'),
        'add_new' => __('Novo Cadastro', 'text_domain'),
        'edit_item' => __('Editar Cadastro', 'text_domain'),
        'update_item' => __('Atualizar Cadastro', 'text_domain'),
        'search_items' => __('Procurar Newsletter', 'text_domain'),
        'not_found' => __('Nenhuma postagem existente', 'text_domain'),
        'not_found_in_trash' => __('Nenhum registro de postagem encontrado na lixeira', 'text_domain')
    );
    $args   = array(
        'label' => __('Newsletter', 'text_domain'),
        'description' => __('Conteúdo dos Newsletter', 'text_domain'),
        'labels' => $labels,
        'supports' => array(
            'title'
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'deon-newsletter-page', // O menu aparecerá junto com os sobmenus (veja a linha 106)
        'taxonomies' => array(
            'email'
        ),
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => '',
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'menu_icon' => 'dashicons-format-chat',
        'capability_type' => 'post'
    );
    
    register_post_type('newsletter', $args);
}
add_action('init', 'deon_newsletter', 0);
add_filter('enter_title_here', 'deon_newsletter_titulo');
function deon_newsletter_titulo($input)
{
    global $post_type;
    if (is_admin() && 'Newsletter' == $post_type)
        return __('Digite o titulo', 'your_textdomain');
    return $input;
}
// - Finaliza as configurações da adição do Newsletter - //

// Cria a Custom Taxonomy - Email
function thenule_email()
{
    $labels = array(
        'name' => _x('Email', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x('Email', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name' => __('Gerenciar Email', 'text_domain'),
        'all_items' => __('Email', 'text_domain'),
        'parent_item' => __('Email Semelhantes', 'text_domain'),
        'parent_item_colon' => __('Email Semelhantes', 'text_domain'),
        'new_item_name' => __('Adicionar Email', 'text_domain'),
        'add_new_item' => __('Adicionar Email', 'text_domain'),
        'edit_item' => __('Editar Email', 'text_domain'),
        'update_item' => __('Atualizar Email', 'text_domain'),
        'separate_items_with_commas' => __('Email separados em virgula', 'text_domain'),
        'search_items' => __('Procurar Email', 'text_domain'),
        'add_or_remove_items' => __('Adicionar ou remover ítens das Taxonomy', 'text_domain'),
        'choose_from_most_used' => __('Veja as Email mais usadas', 'text_domain')
    );
    $args   = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true
    );
    
    register_taxonomy('email', 'newsletter', $args);
}
add_action('init', 'thenule_email', 0);


// Shortcode do formulário | adicione deon_newsletter_form() - onde deseja exibir o formulário.
function deon_newsletter_form()
{
    $form = '<form class="form-inline col-md-12 no-margin" id="ajax_form" method="post">
                            <div class="form-group col-md-12 no-margin">
                                <div class="input-group col-md-11 no-margin">
                                    <input name="nome" class="form-control col-md-12" placeholder="Digite seu nome:" >
                                    <input name="email" class="form-control col-md-12" placeholder="Digite seu email:" >
                                    <span class="input-group-addon col-md-12"><input type="submit" value="Enviar" class="btn"></span>
                                </div>
                            </div>
                        </form>';
    $form .= '<div class="resultado_newsletter"></div>';
    return $form;
}


// Adiciona um menu às colunas da administração
add_action('admin_menu', 'deon_newsletter_page');
function deon_newsletter_page()
{
    // Menu Principal
    add_menu_page('Newsletter', 'Newsletter', 'manage_options', 'deon-newsletter-page', '', 'dashicons-format-chat', 7);
    
    // Submenu para exportar os emails (veja a linha 120)
    add_submenu_page('deon-newsletter-page', 'Exportar Dados', 'Exportar Dados', 'manage_options', 'deon-newsletter-page-export', 'deon_newsletter_page_export');
    
    // Submenu com a taxonomy dos emails cadastrados
    add_submenu_page('deon-newsletter-page', 'Gerenciar Emails', 'Gerenciar Emails', 'manage_options', 'edit-tags.php?taxonomy=email&post_type=newsletter');
}


// Conteúdo da página Exportar Dados
function deon_newsletter_page_export()
{
?>
<div class="wrap">
<h2>Exportar Dados</h2><br>

<table class="wp-list-table widefat fixed striped pages">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>

<?php
    global $post;
    $args        = array(
        'post_type' => 'newsletter',
        'posts_per_page' => -1
    );
    $posts_total = new WP_Query($args);
    if ($posts_total->have_posts()) {
        while ($posts_total->have_posts()) {
            $posts_total->the_post();
            $email = wp_get_post_terms($post->ID, 'email');
?>

<tr>
    <td><?php echo get_the_title();?></td>
    <td><?php foreach ($email as $dados) {echo $dados->name;} ?></td>
    <td><?php echo get_the_date('d/m/Y'); ?></td>
</tr>

<?php
        }
    }
    wp_reset_postdata();
?>  

    </tbody>
  </table>

</div>
<?php
}