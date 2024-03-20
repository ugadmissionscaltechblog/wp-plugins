<?php

namespace Molongui\Authorship\Pro\Includes\Update;
\defined( 'ABSPATH' ) or exit;
if ( !\trait_exists( 'Molongui\Authorship\Pro\Includes\Update\Key' ) )
{
	trait Key
	{
		public function activate( $args )
		{
            if ( empty( $args ) )
            {
                \add_settings_error( 'not_activated_text', 'not_activated_error', \esc_html__( 'The license key is missing from the deactivation request.', 'molongui-authorship-pro' ), 'updated' );
                return false;
            }

			$defaults = array
            (
				'wc_am_action'     => 'activate',
				'product_id'       => $this->product_id,
				'instance'         => $this->wc_am_instance_id,
				'object'           => $this->wc_am_domain,
				'software_version' => $this->software_version,
			);

			$response = $this->send_query( $args, $defaults );

			return $response;
		}
		public function deactivate( $args )
		{
            if ( empty( $args ) )
            {
                \add_settings_error( 'not_deactivated_text', 'not_deactivated_error', \esc_html__( 'The license key is missing from the deactivation request.', 'molongui-authorship-pro' ), 'updated' );
                return false;
            }

			$defaults = array
            (
				'wc_am_action' => 'deactivate',
				'product_id'   => $this->product_id,
				'instance'     => $this->wc_am_instance_id,
				'object'       => $this->wc_am_domain
			);

            $response = $this->send_query( $args, $defaults );

			return $response;
		}
		public function status( $args )
		{
            if ( empty( $this->data[$this->wc_am_api_key_key] ) )
            {
                return false;
            }

			$defaults = array
            (
				'wc_am_action' => 'status',
                'api_key'      => $this->data[$this->wc_am_api_key_key],
				'product_id'   => $this->product_id,
				'instance'     => $this->wc_am_instance_id,
				'object'       => $this->wc_am_domain
			);

            $response = $this->send_query( $args, $defaults );
            $response = \json_decode( $response, true );

			return $response;
		}

	} // trait
} // trait_exists