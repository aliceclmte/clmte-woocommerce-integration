<?php

$placement_types = array(
    'cart' => __( 'In cart', 'clmte'),
    'custom' => __( 'Custom', 'clmte'),
);

$settings = array(
        array(
            'name' => __( 'General Configuration', 'clmte' ),
            'type' => 'title',
            'id'   => $prefix . 'general_config_settings'
        ),
        array(
            'id'        => $prefix . 'api_key',
            'name'      => __( 'Api Key', 'clmte' ), 
            'type'      => 'text',
            'desc_tip'  => __( ' Your organisation\'s Api Key which can be found by creating an account and organisation at clmte.com', 'clmte')
        ),
        array(
            'id'        => $prefix . 'organisation_id',
            'name'      => __( 'Organisation ID', 'clmte' ), 
            'type'      => 'text',
            'desc_tip'  => __( ' Your organisation\'s Organisation ID which can be found by creating an account and organisation at clmte.com', 'clmte')
        ),
        array(
            'id'        => $prefix . 'custom_placement',
            'name'      => __( 'Custom Placement?', 'clmte' ),
            'type'      => 'checkbox',
            'desc'  => __( 'Check this box to use custom placement with shortcode.', 'clmte' ),
            'default'   => 'no'
        ),
        array(
            'id'        => $prefix . 'production_mode',
            'name'      => __( 'Production Mode?', 'clmte' ),
            'type'      => 'checkbox',
            'desc'  => __( 'Check this box to start using the plugin with real API calls.', 'clmte' ),
            'default'   => 'no'
        ),      
        array(
            'id'        => '',
            'name'      => __( 'General Configuration', 'clmte' ),
            'type'      => 'sectionend',
            'desc'      => '',
            'id'        => $prefix . 'general_config_settings'
        ),                      
    );
?>