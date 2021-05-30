<?php

$settings = array(
    array(
        'name' => __( 'Allmän Konfiguration', 'clmte' ),
        'type' => 'title',
        'id'   => $prefix . 'general_config_settings',
    ),
    array(
        'id'       => $prefix . 'api_key',
        'name'     => __( 'API-Nyckel', 'clmte' ),
        'type'     => 'text',
        'desc_tip' => __( ' Din organisations API-nyckel, som kan fås genom att skapa ett konto och en organisation hos clmte.com', 'clmte' ),
    ),
    array(
        'id'       => $prefix . 'organisation_id',
        'name'     => __( 'Organisations-ID', 'clmte' ),
        'type'     => 'text',
        'desc_tip' => __( ' Din organisations Organisations-ID, som kan fås genom att skapa ett konto och en organisation hos clmte.com', 'clmte' ),
    ),
    array(
        'id'      => $prefix . 'production_mode',
        'name'    => __( 'Produktionsläge?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Klicka i rutan för att börja använda pluginet med riktiga API-anrop.', 'clmte' ),
        'default' => 'no',
    ),
    array(
        'id'      => $prefix . 'reload_cart_on_update',
        'name'    => __( 'Ladda om kundvagnen?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Klicka i rutan om det uppstår svårigheter med att lägga till klimatkompensationen i varukorgen.', 'clmte' ),
        'default' => 'no',
    ),  
    array(
        'id'      => $prefix . 'custom_offset_placement',
        'name'    => __( 'Egen placering av kompensations-ruta?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Klicka i rutan för att använda egen placering för klimatkompensations-rutan med en shortcode nedan.', 'clmte' ),
        'default' => 'no',
    ),
    array(
        'id'      => $prefix . 'custom_receipt_placement',
        'name'    => __( 'Egen placering av kompensations-kvitto?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Klicka I rutan för att använda egen placering för kompensations-kvittot med en shortcode nedan.', 'clmte' ),
        'default' => 'no',
    ),
    array(
        'id'   => '',
        'name' => __( 'Allmän Konfiguration', 'clmte' ),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'general_config_settings',
    ),
);