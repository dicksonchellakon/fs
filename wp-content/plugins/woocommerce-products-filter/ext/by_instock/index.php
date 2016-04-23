<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_INSTOCK extends WOOF_EXT
{

    public $type = 'by_html_type';
    public $html_type = 'by_instock'; //your custom key here
    public $index = 'stock';
    public $html_type_dynamic_recount_behavior = 'none';

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path()
    {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link()
    {
        return plugin_dir_url(__FILE__);
    }

    public function woof_add_items_keys($keys)
    {
        $keys[] = $this->html_type;
        return $keys;
    }

    public function init()
    {
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
        add_action('wp_head', array($this, 'wp_head'), 1);

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_instock';
    }

    public function wp_head()
    {
        global $WOOF;
        ?>      
        <script type="text/javascript">
            if (typeof woof_lang_custom == 'undefined') {
                var woof_lang_custom = {};//!!important
            }
            woof_lang_custom.<?php echo $this->index ?> = "<?php _e('In stock', 'woocommerce-products-filter') ?>";
        </script>
        <?php
    }

    //settings page hook
    public function woof_print_html_type_options()
    {
        global $WOOF;
        echo $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }

    public function assemble_query_params(&$meta_query)
    {
        global $WOOF;
        $request = $WOOF->get_request_data();

        if (isset($request['stock']))
        {
            if ($request['stock'] == 'instock')
            {
                $meta_query[] = array(
                    'key' => '_stock_status',
                    'value' => 'outofstock', //instock,outofstock
                    'compare' => 'NOT IN'
                );
            }

            if ($request['stock'] == 'outofstock')
            {
                $meta_query[] = array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'outofstock', //instock,outofstock
                        'compare' => 'IN'
                    )
                );
            }
        }


        //out of stock products - remove from dyn recount
        //wp-admin/admin.php?page=wc-settings&tab=products&section=inventory
        if (get_option('woocommerce_hide_out_of_stock_items', 'no') == 'yes')
        {
            $meta_query[] = array(
                'key' => '_stock_status',
                'value' => array('instock'),
                'compare' => 'IN'
            );
        }

        //+++

        $use_for = isset($WOOF->settings['by_instock']['use_for']) ? $WOOF->settings['by_instock']['use_for'] : 'simple';
        if ($use_for == 'both')
        {
            add_filter('posts_where', array($this, 'posts_where'), 9999);
        }

        //***

        return $meta_query;
    }

    public function posts_where($where = '')
    {
        global $WOOF, $wpdb;
        $request = $WOOF->get_request_data();
        static $where_instock = "";

        //cache on the fly
        if (!empty($where_instock))
        {
            return $where . $where_instock;
        }

        //+++

        if (isset($request['stock']))
        {
            if ($request['stock'] == 'instock')
            {
                $taxonomies = $WOOF->get_taxonomies();
                $prod_attributes = array();
                foreach ($taxonomies as $key => $value)
                {
                    if (substr($key, 0, 3) == 'pa_')
                    {
                        $prod_attributes[] = $key;
                    }
                }
                $prod_attributes_in_request = array();
                if (!empty($prod_attributes))
                {
                    foreach ($prod_attributes as $value)
                    {
                        if (in_array($value, array_keys($request)))
                        {
                            $prod_attributes_in_request[] = $value;
                        }
                    }

                    //***

                    if (!empty($prod_attributes_in_request))
                    {

                        $var_prod_ids_in_stock = array();
                        $var_prod_ids_in_stock_string = array();

                        foreach ($prod_attributes_in_request as $attr_slug)
                        {

                            $attr_slugs = explode(',', $request[$attr_slug]);

                            if (!empty($attr_slugs))
                            {
                                foreach ($attr_slugs as $term_slug)
                                {
                                    $var_prod_ids = $wpdb->get_results("SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key='attribute_{$attr_slug}' AND meta_value='{$term_slug}'", ARRAY_N);
                                    $tmp = array();
                                    if (!empty($var_prod_ids))
                                    {
                                        foreach ($var_prod_ids as $value)
                                        {
                                            $tmp[] = $value[0];
                                        }
                                    }
                                    $var_prod_ids = $tmp;
                                    //***
                                    if (!empty($tmp))
                                    {
                                        $var_prod_ids_string = implode(',', $tmp);
                                        //+++
                                        $tmp1 = $wpdb->get_results("SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key='_stock' AND meta_value > 0 AND post_id IN($var_prod_ids_string)", ARRAY_N);
                                        $tmp2 = array();
                                        if (!empty($tmp1))
                                        {
                                            foreach ($tmp1 as $value)
                                            {
                                                $tmp2[] = $value[0];
                                            }
                                        }

                                        if (!isset($var_prod_ids_in_stock[$attr_slug]))
                                        {
                                            $var_prod_ids_in_stock[$attr_slug] = array();
                                        }

                                        $var_prod_ids_in_stock[$attr_slug] = array_merge($var_prod_ids_in_stock[$attr_slug], $tmp2);
                                    }
                                }
                            }
                        }


                        //print_r($var_prod_ids_in_stock);
                        //exit;

                        if (!empty($var_prod_ids_in_stock))
                        {

                            $var_prod_ids_in_stock_num = array();
                            foreach ($var_prod_ids_in_stock as $attr_slug => $var_prod_ids)
                            {
                                $var_prod_ids_in_stock_num[] = $var_prod_ids;
                            }

                            $intersected = $var_prod_ids_in_stock_num[0];
                            for ($i = 0; $i < count($var_prod_ids_in_stock_num); $i++)
                            {
                                $intersected = array_intersect($intersected, $var_prod_ids_in_stock_num[$i]);
                            }
                            $var_prod_ids_in_stock_string = implode(',', $intersected);
//echo $var_prod_ids_in_stock_string;
//exit;
                            //+++

                            if (!empty($var_prod_ids_in_stock_string))
                            {
                                $products = $wpdb->get_results("
                        SELECT posts.post_parent
                        FROM $wpdb->posts AS posts
                        WHERE posts.ID IN ($var_prod_ids_in_stock_string) AND posts.post_parent > 0", ARRAY_N);

                                $product_ids = array();
                                if (!empty($products))
                                {
                                    foreach ($products as $v)
                                    {
                                        $product_ids[] = $v[0];
                                    }
                                }

                                $product_ids = array_unique($product_ids);
                                if (!empty($product_ids))
                                {
                                    $product_ids = implode(',', $product_ids);
                                    $where .= " AND $wpdb->posts.ID IN($product_ids)";
                                    $where_instock = " AND $wpdb->posts.ID IN($product_ids)";
                                    //die($where);
                                }
                            }
                        }
                    }
                }
            }
        }
        //http://www.dev.woocommerce-filter.com/shop/?swoof=1&pa_size=xl&stock=instock&min_price=15&max_price=80&pa_color=green
        return $where;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_instock'] = new WOOF_EXT_BY_INSTOCK();
