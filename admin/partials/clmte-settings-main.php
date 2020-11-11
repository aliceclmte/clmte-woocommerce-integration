<?php

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
            'desc_tip'  => __( ' Your organisations Api Key which can be found at clmte.com', 'clmte')
        ),
        array(
            'id'        => $prefix . 'compensation_price',
            'name'      => __( 'Compensation price', 'clmte' ), 
            'type'      => 'number',
            'desc_tip'  => __( ' Your organisations compensation price which can be found at clmte.com', 'clmte')
        ), 
        array(
            'id'        => $prefix . 'compensation_product_id',
            'name'      => __( 'Compensation Product Id', 'clmte' ), 
            'type'      => 'number',
            'desc_tip'  => __( ' Your organisations compensation price which can be found at clmte.com', 'clmte')
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