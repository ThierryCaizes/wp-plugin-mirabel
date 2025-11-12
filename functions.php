<?php
//Fiche de style de l'extension
function style_api(){
    wp_enqueue_style ('style_filtre', plugin_dir_url(__FILE__).'asset/style.css');
}
add_action('wp_enqueue_scripts','style_api');