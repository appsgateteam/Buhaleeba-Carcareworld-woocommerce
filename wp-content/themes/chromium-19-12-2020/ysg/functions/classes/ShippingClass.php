<?php

if (!function_exists('add_action')) {
    echo 'You cannot access directly.';
    exit;
}

class ShippingClass
{
    public function __construct()
    {
    }
    /**
     * retrieves order and checks if the shipping status has been changed
     * updateShippingStatus
     */
    public function updateShippingStatus()
    {
        //get orders
        $orders = $this->getWoocommerceOrders();
        //get shipping status for each order

        //update the status
        //if status is "collected" change order status to Out for delivery


    }

    /**
     * get Orders from the system -- only processing and ysg-shipped are needed
     * @function getWoocommerceOrder
     * @access private
     * @return void
     */
    private function getWoocommerceOrders()
    {
        $args = array(
            'status' => array('processing', 'wc-ysg-shipped'),
            'limit' => 30
        );

        $orders = wc_get_orders($args);
        foreach ($orders as $item) {
            $order_id = $item->get_id();
            $current_tracking_status = $current_tracking_details = "";
            $shipping_data = (array) $this->getShippingStatusFromSkynet($order_id);
            //echo "<pre>";
            //print_r($orders);
            //exit;

            $tdetails = (array)$shipping_data['TrackingDetail'];
            $ev1 = array(3, 48, 84, 168);
            $ev2 = array(350, 1);
            $ustatus = "";
            $update_order_status = false;


            foreach ($tdetails as $adet) {
                $det = (array) $adet;
                $itd = strtotime($det['TrackingDate']);
                $cdate =  strtotime("-31 days");
                if ($itd > $cdate) {
                    if (!empty($det['TrackingError'])) {
                        $this->write_log(array(
                            'order_id' => $order_id,
                            "description" => "error occured while getting data from skynet",
                            "response" => $shipping_data
                        ));
                        break;
                    }

                    if (in_array($det['TrackingEventCode'], $ev1)) {
                        //update payment tracking status
                        $update_order_status = true;
                        $ustatus = "wc-ysg-shipped";
                    }

                    if (in_array($det['TrackingEventCode'], $ev2)) {
                        //update payment tracking status
                        $update_order_status = true;
                        $ustatus = "completed";
                    }

                    $current_tracking_status = $det['TrackingEventName'];
                    $current_tracking_details = json_encode($shipping_data);
                }
            }


            //update status
            if ($update_order_status == true) {
                //echo $ustatus . "<br/>";
                $item->update_status($ustatus);
            }
            //print_r($current_tracking_status);
            //print_r($order_id);

            update_post_meta($order_id, 'ysg_tracking_status', $current_tracking_status);
            update_post_meta($order_id, 'ysg_tracking_details', $current_tracking_details);
        }
    }

    /**
     * 
     */

    public function getShippingCostFromSkynet()
    {
    }

    private function getShippingStatusFromSkynet($ref_no)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.postshipping.com/api2/tracks?ReferenceNumber=" . $ref_no,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                "token: 7C2B37BD3564BFF3C5A11A89E9FD95CC"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);

        return $data;
    }

    /**
     * add to error log
     * write_log
     */
    private function write_log($log)
    {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}
