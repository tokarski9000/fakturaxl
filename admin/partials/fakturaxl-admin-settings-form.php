<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  rt-studio.pl
 * @since 1.0.0
 *
 * @package    Fakturaxl
 * @subpackage Fakturaxl/admin/partials
 * Included by class-fakturaxl-admin-settings.php
 */
?>
<h2>
    <?php esc_html_e('FakturaXL settings', $this->plugin_name); ?>
</h2>
<form method="post" action="admin.php?page=settings-fakturaxl">


<table class="form-table">
    <tr>
        <th><?php esc_html_e('API Token', $this->plugin_name); ?></th><td><input id="api-token" name="api-token" type="text" style="width: 100%; max-width: 84.5ch" value="<?php esc_html_e($api_token); ?>"></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Company Name', $this->plugin_name); ?></th><td><input id="company-name" name="company-name" type="text" style="width: 100%; max-width: 84.5ch" value="<?php esc_html_e($company_name); ?>"></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Invoice theme', $this->plugin_name); ?></th>
        <td>
            <select name="invoice-theme" id="invoice-theme">
                <?php
                foreach ($invoice_theme_names as $value => $name) :
                    ?>
                            <option value="<?php echo $value; ?>" 
                                <?php
                                if ($value === $invoice_theme) {
                                    esc_html_e('selected=selected');
                                }
                                ?>
                            ><?php echo $value.' : '.$name; ?></option>
                        <?php
                endforeach;
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <th><?php esc_html_e('Company department', $this->plugin_name); ?></th><td><select id="company-department" name="company-department" type="text" value="<?php echo esc_html_e($company_department); ?>">
            <?php
            foreach ($available_company_departments_array['dzial'] as $value) :
                ?>
                            <option value="<?php echo $value['id']; ?>" 
                                <?php
                                if ($value['id'] === $company_department) {
                                    esc_html_e('selected=selected');
                                }
                                ?>
                            ><?php echo $value['id'].' : '.$value['nazwa']; ?></option>
                        <?php
            endforeach;
            ?>
        </select>
    </td>
    </tr>
    <tr>
        <th>
            <?php esc_html_e('Invoice type', $this->plugin_name); ?>
        </th>
        <td>
            <select name="invoice-type" id="invoice-type">
            <?php
            foreach ($invoice_type_array as $value => $name) :
                ?>
                            <option value="<?php echo $value; ?>" 
                                <?php
                                if ($value == $invoice_type) {
                                    esc_html_e('selected=selected');
                                }
                                ?>
                            ><?php echo $value.' : '.$name; ?></option>
                        <?php
            endforeach;
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <?php esc_html_e('Invoice subtype', $this->plugin_name); ?>
        </th>
        <td>
            <select name="invoice-subtype" id="invoice-subtype">
            <?php
            foreach ($intovice_subtype_array as $value => $name) :
                ?>
                            <option value="<?php echo $value; ?>" 
                                <?php
                                if ($value == $invoice_subtype) {
                                    esc_html_e('selected=selected');
                                }
                                ?>
                            ><?php echo $value.' : '.$name; ?></option>
                        <?php
            endforeach;
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <button class="button button-primary" type="submit" value="page=settings-fakturaxl"><?php esc_html_e('Zapisz zmiany', $this->plugin_name); ?></button>
        </th>
    </tr>


</table>
</form>
<?php

