<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_get_user_role_options' ) )
{
    function authorship_pro_get_user_role_options()
    {
        $options = array();
        foreach( \get_editable_roles() as $role => $details )
        {
            $options[$role]['id']       = $role;
            $options[$role]['label']    = \translate_user_role( $details['name'] );
            $options[$role]['disabled'] = false;
        }
        $options['molongui_no_role'] = array( 'id' => 'molongui_no_role', 'label' => __( '&mdash; No role for this site &mdash;' ), 'disabled' => false );

        return $options;
    }
}