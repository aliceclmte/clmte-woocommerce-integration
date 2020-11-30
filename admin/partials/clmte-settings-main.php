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
        // array(
        //     'id'        => $prefix . 'placement',
        //     'name'      => __( 'Placement', 'clmte' ), 
        //     'type'      => 'select',
        //     'class'     => 'wc-enhanced-select',
        //     'options'   => $placement_types,
        //     'desc_tip'  => __( ' The placement of the clmte offset box. Use the clmte-offset shortcode for custom placement.', 'clmte')
        // ),
        array(
            'id'        => $prefix . 'custom_placement',
            'name'      => __( 'Custom Placement?', 'clmte' ),
            'type'      => 'checkbox',
            'desc'  => __( 'Ceck this box to use custom placement with shortcode.', 'clmte' ),
            'default'   => 'no'
        ),   
        // array(
        //     'id'        => $prefix . 'compensation_price',
        //     'name'      => __( 'Compensation Price', 'clmte' ), 
        //     'type'      => 'number',
        //     'desc_tip'  => __( ' The price of the compensation. Leave blank to automatically get the compensation price.', 'clmte')
        // ), 
        // array(
        //     'id'        => $prefix . 'compensation_product_id',
        //     'name'      => __( 'Compensation Product Id', 'clmte' ), 
        //     'type'      => 'number',
        //     'desc_tip'  => __( ' The id of the compensation product.', 'clmte')
        // ), 
        array(
            'id'        => '',
            'name'      => __( 'General Configuration', 'clmte' ),
            'type'      => 'sectionend',
            'desc'      => '',
            'id'        => $prefix . 'general_config_settings'
        ),                      
    );
?>