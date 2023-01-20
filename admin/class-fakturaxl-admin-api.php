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
class Fakturaxl_Admin_Api
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
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        //Add button to order page view
        add_action('woocommerce_admin_order_data_after_order_details', [$this, 'add_button'], 10, 1);
        //Init ajax for issue invoice ajax
        add_action('wp_ajax_issue_invoice', [$this, 'issue_invoice']);
        //Add invoce info column for order list view
        add_filter('manage_edit-shop_order_columns', [$this, 'register_faktura_order_column'], 10, 1);
        //Display invoce info in invoice column
        add_action('manage_shop_order_posts_custom_column', [$this, 'display_faktura_column'], 10, 1);

    }//end __construct()


    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Fakturaxl_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Fakturaxl_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'css/fakturaxl-admin.css', [], $this->version, 'all');

    }//end enqueue_styles()


    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Fakturaxl_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Fakturaxl_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__).'js/fakturaxl-admin.js', [ 'jquery' ], $this->version, false);
        wp_localize_script($this->plugin_name, 'ajax_object', [ 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('_wpnonce')]);

    }//end enqueue_scripts()


    public function issue_invoice()
    {
        check_ajax_referer('_wpnonce', 'security');
        $postID      = $_POST['postID'];
        $order       = new WC_Order($postID);
        $wystawienie = new DateTime();

        $payment_methods = [
            'Przelew',
            'Gotówka',
            'Karta płatnicza',
            'Barter',
            'BLIK',
            'Czek',
            'DotPay',
            'Kompensata',
            'Opłata za pobraniem',
            'PayPal',
            'PayU',
            'Płatność elektroniczna',
            'Przelewy24',
            'Bon',
            'Kredyt',
        ];
        // Get Order WooCommerce vars.
        $payment_title       = $order->get_payment_method_title();
        $payment_method      = $order->get_payment_method();
        $payment             = $payment_title;
        $nip                 = get_post_meta($postID, 'nip', true);
        $billing_company     = get_post_meta($postID, '_billing_company', true);
        $firma_czy_prywatnie = $nip ? '0' : '1';
        if ($payment_title === 'Za pobraniem') {
            $payment = 'Opłata za pobraniem';
        }

        // Workaround for other p24 payments not supported by FakturaXL
        if ($payment_title !== 'BLIK' || $payment_title !== 'Przelewy24') {
            if (str_contains($payment_method, 'przelewy24')) {
                $payment = 'Przelewy24';
            }
        }

        // Get plugin user settings options.
        $api_token          = get_option('fakturaxl_api_token', '');
        $invoice_theme      = get_option('fakturaxl_invoice_theme', '0');
        $company_department = get_option('fakturaxl_company_department', '');
        $invoice_type       = get_option('fakturaxl_invoice_type', '');
        $invoice_subtype    = get_option('fakturaxl_invoice_subtype', '');
        $company_name       = get_option('fakturaxl_company_name', '');

        $input_xml = '
			<dokument>

			<api_token>'.$api_token.'</api_token>

			<typ_faktury>'.$invoice_type.'</typ_faktury>
			<typ_faktur_podtyp>'.$invoice_subtype.'</typ_faktur_podtyp>
			<obliczaj_sume_wartosci_faktury_wg>1</obliczaj_sume_wartosci_faktury_wg>
			<numer_faktury></numer_faktury>
			<data_wystawienia>'.$order->get_date_created()->format('Y-m-d').'</data_wystawienia>
			<data_sprzedazy>'.$order->get_date_created()->format('Y-m-d').'</data_sprzedazy>
			<termin_platnosci_data>'.$order->get_date_paid()->format('Y-m-d').'</termin_platnosci_data>
			<data_oplacenia>'.$order->get_date_paid()->format('Y-m-d').'</data_oplacenia>
			<kwota_oplacona>'.$order->get_total().'</kwota_oplacona>
			<uwagi>Numer Zamówienia: '.$order->get_id().'</uwagi>
			<waluta>'.$order->get_currency().'</waluta>
			<kurs>1</kurs>
			<rodzaj_platnosci>'.$payment.'</rodzaj_platnosci>
			<jezyk>PL</jezyk>
			<szablon>'.$invoice_theme.'</szablon>
			<imie_nazwisko_wystawcy>'.$company_name.'</imie_nazwisko_wystawcy>
			<imie_nazwisko_odbiorcy>'.$order->get_billing_first_name().' '.$order->get_billing_last_name().'</imie_nazwisko_odbiorcy>
			<nr_zamowienia>'.$order->get_id().'</nr_zamowienia>
			<dodatkowe_uwagi></dodatkowe_uwagi>
			<id_dzialy_firmy>'.$company_department.'</id_dzialy_firmy>
			<wyslij_dokument_do_klienta_emailem>0</wyslij_dokument_do_klienta_emailem>
			<magazyn_id></magazyn_id>
			<automatyczne_tworzenie_dokumentu_magazynowego>0</automatyczne_tworzenie_dokumentu_magazynowego>

			<nabywca>
				<firma_lub_osoba_prywatna>'.$firma_czy_prywatnie.'</firma_lub_osoba_prywatna>
				<nazwa>'.$billing_company.'</nazwa>
				<imie>'.$order->get_billing_first_name().'</imie>
				<nazwisko>'.$order->get_billing_last_name().'</nazwisko>
				<nip>'.$nip.'</nip>
				<ulica_i_numer>'.$order->get_billing_address_1().' '.$order->get_billing_address_2().'</ulica_i_numer>
				<kod_pocztowy>'.$order->get_billing_postcode().'</kod_pocztowy>
				<miejscowosc>'.$order->get_billing_city().'</miejscowosc>
				<kraj>'.$order->get_billing_country().'</kraj>
				<email>'.$order->get_billing_email().'</email>
				<telefon>'.$order->get_billing_phone().'</telefon>
				<fax></fax>
				<www></www>
				<nr_konta_bankowego></nr_konta_bankowego>
			</nabywca>';
        // Iterate through order items
        foreach ($order->get_items() as $item_id => $item) {
            $cena_brutto = ($item->get_total_tax() + $item->get_total());
            $input_xml  .= '
					<faktura_pozycje>
						<nazwa>'.$item->get_name().'</nazwa>
						<produkt_id>'.$item->get_variation_id().'</produkt_id>
						<ilosc>'.$item->get_quantity().'</ilosc>
						<jm>szt.</jm>
						<vat>23</vat>
						<wartosc_brutto>'.$cena_brutto.'</wartosc_brutto>
					</faktura_pozycje>
				';
        }

        // Add shippment method
            $input_xml .= '
				<faktura_pozycje>
					<nazwa>Wysyłka: '.$order->get_shipping_method().'</nazwa>
					<kod_produktu></kod_produktu>
					<ilosc>1</ilosc>
					<jm>szt.</jm>
					<vat>23</vat>
					<wartosc_brutto>'.$order->get_shipping_total().'</wartosc_brutto>
				</faktura_pozycje>
			</dokument>
			';
        // curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://program.fakturaxl.pl/api/dokument_dodaj.php');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input_xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $return_xml = curl_exec($ch);
            curl_close($ch);

            // Parsing xml to array
            $array_data = json_decode(json_encode(simplexml_load_string($return_xml)), true);
        if ($array_data['kod'] == '1') {
            update_post_meta($postID, 'faktura_id', $array_data['dokument_id']);
            update_post_meta($postID, 'faktura_nr', $array_data['dokument_nr']);
            update_post_meta($postID, 'faktura_u_kod', $array_data['unikatowy_kod']);
            update_post_meta($postID, 'dokument_id', $array_data['dokument_id']);
            $note = 'Faktura nr: '.$array_data['dokument_nr'].'<br>Dokument id: '.$array_data['dokument_id'];
            $order->add_order_note($note);
        }

            echo json_encode($array_data);
            wp_die();

    }//end issue_invoice()


    /**
     * Add button to order page
     *
     * @param  WC_Order $order
     * @return void
     */
    public function add_button(WC_Order $order) : void
    {
        echo '<div style="margin-top: 1rem;" class="button" id="wystaw-fakture">Wystaw Fakture</div>';

    }//end add_button()


    /**
     * Add fakturaxl column to order view list
     *
     * @param  array $columns
     * @return array
     */
    public function register_faktura_order_column(array $columns) : array
    {
        $columns['Faktura'] = 'Faktura';
        return $columns;

    }//end register_faktura_order_column()


    /**
     * Display fakturaxl data in order view list column
     *
     * @param  mixed $column
     * @return void
     */
    public function display_faktura_column(array $column) : void
    {
        global $post;

        if ('Faktura' === $column) {
            $nr_faktury = get_post_meta($post->ID, 'faktura_nr', true);

            if ($nr_faktury && strlen($nr_faktury) > 0) {
                echo $nr_faktury;
            }
        }

    }//end display_faktura_column()


}//end class
