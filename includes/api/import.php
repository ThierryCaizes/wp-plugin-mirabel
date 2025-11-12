<?php

require_once __DIR__.'/langues-mapping.php';

function mirabel_import_api(){
    global $langues_iso;

    //Evite l'enregistrement automatique de WP
    if ( defined ('DOING_AUTOSAVE') and DOING_AUTOSAVE ){
        return;
    }

    // Evite les révisions
    if ( wp_is_post_revision ( get_the_ID() ) ){
        return;
    }

    $request_meta = wp_safe_remote_get ('https://reseau-mirabel.info/api/titres?grappeid=2');

    if ( is_wp_error ($request_meta) ){
        return false;
    }

    $body_mirabel = wp_remote_retrieve_body ($request_meta);
    $data_mirabel = json_decode ($body_mirabel, true);

    foreach ($data_mirabel as $meta){
        // Initialisation
        $current_post_id = null;

        // Ignorer les revues obsolètes
        if (!empty($meta['obsoletepar'])){
            // Passe à la revue suivante
            continue;
        }

        //Titre
        $title_journal = sanitize_text_field ( $meta ['titre'] );
        //Revue id
        $id_journal = sanitize_text_field ( $meta ['revueid'] );
        //Issn tableau 0
        $issn_key_zero = isset($meta['issns'][0]['issn']) ? sanitize_text_field($meta['issns'][0]['issn']) : '';
        $issn_support_zero = isset($meta['issns'][0]['support']) ? sanitize_text_field($meta['issns'][0]['support']) : '';
        //Issn tableau 1
        $issn_key_one = isset($meta['issns'][1]['issn']) ? sanitize_text_field($meta['issns'][1]['issn']) : '';
        $issn_support_one = isset($meta['issns'][1]['support']) ? sanitize_text_field($meta['issns'][1]['support']) : '';
        // Périodicité
        $period_journal = sanitize_text_field ( $meta ['periodicite'] );
        // Langues
        $langs_journal = array();
        $meta_lang = $meta ['langues'];
        foreach ($meta_lang as $code_iso){
            $code_iso = sanitize_key ($code_iso);
            if (isset ($langues_iso[$code_iso]) ){
                $langs_journal[] = $langues_iso[$code_iso];
            } else{
                $langs_journal[] = $code_iso;
            }
        }
        //Urls
        $url_mirabel_journal = sanitize_text_field ( $meta ['url_revue_mirabel'] );
        $url_journal = sanitize_text_field ( $meta ['url'] );
        //Revue obsolete
        $obsolete_journal = sanitize_text_field ( $meta ['obsoletepar'] );

        // Recherche les posts
        $post_exist = get_posts(array(
            'post_type' => 'catalog',
            'meta_query' => array(
                array(
                    'key' => 'id_journal',
                    'value' => $id_journal,
                    'compare' => '='
                ),
            ),
            'posts_per_page' => 1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        // Vérifie si un post correspondant a été trouvé
        if (!empty($post_exist)){
            //Récupère l'ID du post existant
            $post_id_exist = $post_exist[0]->ID;
            $current_post_id = $post_id_exist;
            // Met à jour les posts
            $update_post = array(
                'ID' => $post_id_exist,
                'post_title' => $title_journal,
                'post_status' => 'publish',
            );
            wp_update_post ($update_post);

            //Supprime les données dans les champs qui ne doivent plus apparaitre
            delete_post_meta($post_id_exist, 'issn_e_journal');
            delete_post_meta($post_id_exist, 'issn_journal');
            delete_post_meta($post_id_exist, 'periodicite_journal');
            delete_post_meta($post_id_exist, 'lang_journal_repeater'); 
            delete_post_meta($post_id_exist, 'link_notice_mirabel');
            delete_post_meta($post_id_exist, 'link_website_journal');
            delete_post_meta($post_id_exist, 'id_journal');

            //Issns Niveau 0
             if ($issn_support_zero === 'electronique'){
                update_post_meta($post_id_exist, 'issn_e_journal', $issn_key_zero);
            }elseif ($issn_support_zero === 'papier'){
                update_post_meta($post_id_exist, 'issn_journal', $issn_key_zero);
            }

            //Issn Niveau 1
             if ($issn_support_one === 'electronique'){
                update_post_meta($post_id_exist, 'issn_e_journal', $issn_key_one);
            } elseif ($issn_support_one === 'papier'){
                update_post_meta($post_id_exist, 'issn_journal', $issn_key_one);
            }

            //Periodicité
            update_post_meta($post_id_exist, 'periodicite_journal', $period_journal);

            // Langues
            update_post_meta($post_id_exist, 'lang_journals', $langs_journal);

            //Url Notice Mirabel
            update_post_meta($post_id_exist, 'link_notice_mirabel', $url_mirabel_journal);

            //Url revue
            update_post_meta($post_id_exist, 'link_website_journal', $url_journal);

            // ID
            update_post_meta($post_id_exist, 'id_journal', $id_journal);

        } else {

        //Relie l'api au CPT
        // Si revue non obsolete il s'affiche
        if ($obsolete_journal == null){
            $no_obsolete_journal = wp_insert_post(array(
                'post_type' => 'catalog',
                'post_title' => $title_journal,
                'post_status' => 'publish',
                'meta_input' => array(
                    'id_journal' => $id_journal
                ),
            ));

            // Champs
            if ($no_obsolete_journal){
                $current_post_id = $no_obsolete_journal;
                //Issns Niveau 0
                if ($issn_support_zero === 'electronique'){
                    update_post_meta($no_obsolete_journal, 'issn_e_journal', $issn_key_zero);
                }elseif ($issn_support_zero === 'papier'){
                    update_post_meta($no_obsolete_journal, 'issn_journal', $issn_key_zero);
                }

                //Issn Niveau 1
                if ($issn_support_one === 'electronique'){
                    update_post_meta($no_obsolete_journal, 'issn_e_journal', $issn_key_one);
                }elseif ($issn_support_one === 'papier'){
                    update_post_meta($no_obsolete_journal, 'issn_journal', $issn_key_one);
                }

                //Periodicité
                update_post_meta($no_obsolete_journal, 'periodicite_journal', $period_journal);

                //langues
                update_post_meta($no_obsolete_journal, 'lang_journals', $langs_journal);

                 //Url Notice Mirabel
                update_post_meta($no_obsolete_journal, 'link_notice_mirabel', $url_mirabel_journal);

                //Url revue
                update_post_meta($no_obsolete_journal, 'link_website_journal', $url_journal);

                // ID
                update_post_meta($no_obsolete_journal, 'id_journal', $id_journal);
            }
        }
    }

    //Thématiques
    $request_meta_cat = wp_safe_remote_get ("https://reseau-mirabel.info/api/themes/revue/$id_journal");
    if ( is_wp_error ($request_meta_cat) ){
        continue;
    }

    $body_mirabel_cat = wp_remote_retrieve_body ($request_meta_cat);
    $data_mirabel_cat = json_decode ($body_mirabel_cat, true);

    if ($current_post_id) {
        //Supprime les données dans les champs qui ne doivent plus apparaitre
        delete_post_meta($current_post_id, 'domaine_journal_repeater');

        $domains = [];
        foreach ($data_mirabel_cat as $meta_cat){
            if (isset($meta_cat['nom'])){
                $domains[] = sanitize_text_field($meta_cat['nom']);
            }
        }
        // Champs
        if (!empty($domains)) {
            update_post_meta($current_post_id, 'domain_journals', $domains);
        }
    }

    // Plateformes
    $request_meta_plateform = wp_safe_remote_get ("https://reseau-mirabel.info/api/acces/titres?revueid=$id_journal");
    if ( is_wp_error ($request_meta_plateform) ){
        continue;
    }

    $body_mirabel_plateform = wp_remote_retrieve_body ($request_meta_plateform);
    $data_mirabel_plateform = json_decode ($body_mirabel_plateform, true);

    if ($current_post_id) {
        //Supprime les données dans les champs qui ne doivent plus apparaitre
        delete_post_meta($current_post_id,'plateform_journal_repeater');

            $plateform = [];
            foreach ($data_mirabel_plateform as $meta_plateform){
                if (isset($meta_plateform['ressourceid'])){
                    $plateform[] = sanitize_text_field($meta_plateform['ressourceid']);
                }
            }

            // Champs
            if (!empty($plateform)) {
                update_post_meta($current_post_id, 'plateform_journals', $domains);
            }
    }
    }
}