<?php
<div class="wrap">
<h2>WP Plugin Template</h2>
<form method="post" action="options.php">
        <?php @settings_fields('nxf_golf_tournament'); ?>
        <?php @do_settings_fields('nxf_golf_tournament'); ?>

        <table class="form-table">  
            <tr valign="top">
                <th scope="row"><label for="current_year">Tournament Year</label></th>
                <td><input type="text" name="current_year" id="current_year" value="<?php echo get_option('current_year); ?>" /></td>
            </tr>
        </table>

        <?php @submit_button(); ?>
    </form>
</div>