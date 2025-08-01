<?php
//YITH WooCommerce Affiliates – UPI Payment Gateway Extension
if (defined( 'YITH_WCAF' ) ) {
function add_upi_payment_method($gateways) {
    $gateways[] = 'upi'; // Adding UPI payment method
    return $gateways;
}
add_filter('yith_wcaf_payment_gateways', 'add_upi_payment_method');

if ( ! class_exists( 'YITH_WCAF_UPI_Gateway' ) ) {
    /**
     * UPI Gateway
     *
     * @since 1.0.0
     */
    if ( ! class_exists( 'YITH_WCAF_Abstract_Gateway' ) ) {
        require_once YITH_WCAF_INC . 'abstracts/class-yith-wcaf-abstract-gateway.php';
    }


    class YITH_WCAF_UPI_Gateway extends YITH_WCAF_Abstract_Gateway {

        /**
         * Constructor method
         *
         * @since 1.0.0
         */
        public function __construct() {
            $this->id   = 'upi';
            $this->name = apply_filters( 'yith_wcaf_upi_payment_label', _x( 'UPI Payment', '[ADMIN] Gateway name', 'yith-woocommerce-affiliates' ) );

            parent::__construct();
        }

        /**
         * Init fields for the gateway
         *
         * @return void
         */
        protected function init_fields() {
            $this->fields = array(
                'upi_provider' => array(
                    'label'   => _x( 'UPI Provider', '[ADMIN] UPI gateway settings.', 'yith-woocommerce-affiliates' ),
                    'type'    => 'select',
                    'options' => array(
                        'gpay'    => 'Google Pay',
                        'phonepe' => 'PhonePe',
                        'amazon'  => 'Amazon Pay',
                        'paytm'   => 'Paytm',
                        'bhim'   => 'BHIM UPI',
                        'upi'   => 'Other UPI ID',
                    ),
                    'placeholder' => _x( 'Select UPI Provider', '[ADMIN] UPI gateway settings.', 'yith-woocommerce-affiliates' ),
                ),
                'upi_id' => array(
                    'label'   => apply_filters( 'yith_wcaf_upi_id_label', _x( 'UPI ID', '[ADMIN] UPI gateway settings.', 'yith-woocommerce-affiliates' ) ),
                    'type'    => 'text',
                    'desc'    => _x( 'Enter the UPI ID for the receiver\'s account.', '[ADMIN] UPI gateway settings.', 'yith-woocommerce-affiliates' ),
                    'default' => '',
                ),
            );
        }

        /**
         * Execute a UPI payment
         *
         * @param int|int[] $payment_ids Array of registered payments.
         *
         * @return mixed Array with operation status and messages
         * @since 1.0.0
         */
        public function process_payment( $payment_ids ) {
            if ( is_array( $payment_ids ) ) {
                $payment_id = array_shift( $payment_ids );
            } else {
                $payment_id = (int) $payment_ids;
            }

            $this->log( sprintf( _x( 'Trying to pay %s with UPI', '[ADMIN] Gateway logs.', 'yith-woocommerce-affiliates' ), $payment_id ) );

            $payment = YITH_WCAF_Payment_Factory::get_payment( (int) $payment_id );

            if ( ! $payment ) {
                $this->log( sprintf( _x( 'Unable to find payment object (#%s)', '[ADMIN] Gateway logs.', 'yith-woocommerce-affiliates' ), $payment_id ), 'error' );

                return array(
                    'status'   => false,
                    'messages' => _x( 'Payments failed', '[ADMIN] Gateway messages.', 'yith-woocommerce-affiliates' ),
                );
            }

            $affiliate = $payment->get_affiliate();

            if ( ! $affiliate ) {
                $message = sprintf( _x( 'Unable to find affiliate for payment (#%s)', '[ADMIN] Gateway logs.', 'yith-woocommerce-affiliates' ), $payment_id );

                $this->log( $message, 'warning' );
                $payment->add_note( $message );

                return array(
                    'status'   => false,
                    'messages' => _x( 'Payments failed', '[ADMIN] Gateway messages.', 'yith-woocommerce-affiliates' ),
                );
            }

            if ( ! $this->can_pay_affiliate( $affiliate ) ) {
                $message = sprintf( _x( 'Cannot pay affiliate #%s with this gateway', '[ADMIN] Gateway logs.', 'yith-woocommerce-affiliates' ), $affiliate->get_id() );

                $this->log( $message, 'warning' );
                $payment->add_note( $message );

                return array(
                    'status'   => false,
                    'messages' => _x( 'Payments failed', '[ADMIN] Gateway messages.', 'yith-woocommerce-affiliates' ),
                );
            }

            $payment_gateway_details = $payment->get_gateway_details();
            $payment_details         = $payment_gateway_details;

            if ( ! $payment_details ) {
                $payment_details = $affiliate->get_gateway_preferences( $this->id );
            }

            $upi_id = isset( $payment_details['upi_id'] ) ? $payment_details['upi_id'] : '';

            if ( ! $upi_id ) {
                $message = sprintf( _x( 'Missing required information for payment #%1$s (UPI ID -> %2$s)', '[ADMIN] Gateway logs.', 'yith-woocommerce-affiliates' ), $payment_id, $upi_id );

                $this->log( $message, 'warning' );
                $payment->add_note( $message );

                return array(
                    'status'   => false,
                    'messages' => _x( 'Missing required payment information', '[ADMIN] Gateway messages.', 'yith-woocommerce-affiliates' ),
                );
            }

            $payment->add_note( _x( 'Payment marked as paid via UPI.', '[ADMIN] Payment notes.', 'yith-woocommerce-affiliates' ) );
            $payment->set_status( 'completed' );
            $payment->set_gateway_details(
                array(
                    'upi_id' => $upi_id,
                )
            );
            $payment->save();

            do_action( 'yith_wcaf_payment_sent', $payment );

            $this->log( sprintf( _x( 'Payment %s processed successfully with UPI', '[ADMIN] Gateway logs.', 'yith-woocommerce-affiliates' ), $payment_id ) );

            return array(
                'status'   => true,
                'messages' => _x( 'Payments sent', '[ADMIN] Gateway messages.', 'yith-woocommerce-affiliates' ),
            );
        }
    }
}

}
