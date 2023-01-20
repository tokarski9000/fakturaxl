<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  rt-studio.pl
 * @since 1.0.0
 *
 * @package    Fakturaxl
 * @subpackage Fakturaxl/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fakturaxl
 * @subpackage Fakturaxl/admin
 * @author     Rafał Tokarski <kontakt@rt-studio.pl>
 */
class Fakturaxl_Settings
{

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;


    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        // Call admin_menu hook
        add_action('admin_menu', [$this, 'settings_init']);

    }//end __construct()


    /**
     * settings_init
     * Initializing menu page
     *
     * @return void
     */
    public function settings_init()
    {
        add_menu_page(
            __('Ustawienia FakturaXL', $this->plugin_name),
            __('FakturaXL', $this->plugin_name),
            'manage_options',
            'settings-fakturaxl',
            [
                $this,
                'admin_page_contents',
            ],
            'dashicons-media-document',
            3
        );

    }//end settings_init()


    /**
     * admin_page_contents
     * HTML form for admin page called by @settings_init method
     *
     * @return void
     */
    public function admin_page_contents()
    {
        if (isset($_POST['api-token'])) {
            update_option('fakturaxl_api_token', trim($_POST['api-token']));
        }

        if (isset($_POST['company-name'])) {
            update_option('fakturaxl_company_name', trim($_POST['company-name']));
        }

        if (isset($_POST['invoice-theme'])) {
            update_option('fakturaxl_invoice_theme', $_POST['invoice-theme']);
        }

        if (isset($_POST['company-department'])) {
            update_option('fakturaxl_company_department', $_POST['company-department']);
        }

        if (isset($_POST['invoice-type'])) {
            update_option('fakturaxl_invoice_type', $_POST['invoice-type']);
        }

        if (isset($_POST['invoice-subtype'])) {
            update_option('fakturaxl_invoice_subtype', $_POST['invoice-subtype']);
        }

        $invoice_type_array     = [
            0  => 'Faktura VAT',
            1  => 'Faktura proforma',
            5  => 'Faktura marża',
            6  => 'Faktura bez vat (rachunek)',
            11 => 'Faktura zaliczkowa',
            14 => 'Paragon',
            31 => 'PZ - Przyjęcie Zewnętrzne',
            30 => 'WZ - Wydanie Zewnętrzne',
            22 => 'Faktura WDT',
            23 => 'Eksport Towarów',
            24 => 'Eksport Usług',
            25 => 'Eksport Usług VAT-UE 28b',
        ];
        $intovice_subtype_array = [
            0 => 'Niefiskalny',
            1 => 'Fiskalny',
        ];

        $api_token           = get_option('fakturaxl_api_token', '');
        $company_name        = get_option('fakturaxl_company_name', '');
        $invoice_theme       = get_option('fakturaxl_invoice_theme', '0');
        $invoice_theme_names = [
            0 => 'Podstawowy',
            1 => 'Prosty',
            2 => 'Ekonomiczny',
        ];
        $company_department  = get_option('fakturaxl_company_department', '');
        $invoice_type        = get_option('fakturaxl_invoice_type', '');
        $invoice_subtype     = get_option('fakturaxl_invoice_subtype', '');
        $available_company_departments_array = $this->get_company_departments($api_token);
        /*
         *  Include form settings
         *
         */
        include_once 'partials/fakturaxl-admin-settings-form.php';

    }//end admin_page_contents()


    /**
     * get_company_departments
     *
     * @param  string $api_token
     * @return string
     */
    private function get_company_departments(string $api_token) : array
    {
        $input_xml = "
		<dokument>
		
		  <api_token>$api_token</api_token>
		
		</dokument>
		";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://program.fakturaxl.pl/api/dokument_lista_dzialow.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input_xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $return_xml = curl_exec($ch);
        curl_close($ch);
        // Parse xml to array.
        $array_data = json_decode(json_encode(simplexml_load_string($return_xml)), true);
        return $array_data;

    }//end get_company_departments()


}//end class
