<?php
/**
 * Page for admins to run database upgrades
 */
if (!defined('ABSPATH'))
    exit;

if (is_admin()) {

    load_class('wefund4u_db_custom_tables.class.php');
    $db_setup = new Wefund4u_DB_Custom_Tables();
    $upgrade_count = $db_setup->avaiable_count();

    if ($upgrade_count > 0)
        add_action('admin_notices', 'db_upgrade_alert_notice');


    add_action('admin_notices', 'db_updated_tables_notice');
    add_action('admin_menu', 'database_upgrades_menu');
}

function db_upgrade_alert_notice() {

    global $pagenow;
//    if ( $pagenow == 'admin.php' /*&& isset($_GET['page']) && $_GET['page'] == 'database-upgrades' */) {
    ?>
    <div class="notice notice-error is-dismissible" style=" margin: 15px 0 !important;padding: 10px !important;">
        <p><strong>Please upgrade tables.</strong></p>
    </div>
    <?php
//    }
}

function database_upgrades_menu() {
    $db_setup = $GLOBALS['db_setup'];
    $upgrade_count = $db_setup->avaiable_count();
    add_menu_page('Database Upgrades', $upgrade_count ? sprintf('Database<br> Upgrades <span class="awaiting-mod">%d</span>', $upgrade_count) : 'Database Upgrades', 'manage_options', 'database-upgrades', 'database_upgrades_page', 'dashicons-arrow-up-alt', 2);
}

/** Admin Notice on Activation. @since 0.1.0 */
function db_updated_tables_notice() {
    global $pagenow;
//    if ( $pagenow == 'admin.php' /*&& isset($_GET['page']) && $_GET['page'] == 'database-upgrades' */) {
    if (get_transient('table_updated')) {
        ?>

        <div class="notice notice-success is-dismissible" style=" margin: 15px 0 !important;padding: 10px !important;">
            <div><p><strong><?php echo get_transient('table_updated') ?> tables were created.</strong></p></div>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient('table_updated');
    }
//    }
}

function database_upgrades_page() {

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    //load_class('wefund4u_db_custom_tables.class.php');
    $db_setup = $GLOBALS['db_setup'];

    // Run db custom tables setup on POST
    if (isset($_POST['db-upgrade']) && isset($_POST['db-upgrade-confirm']) && strtolower($_POST['db-upgrade-confirm']) == 'confirm') {
        $results = $db_setup->setup();
    }
    $tables = $db_setup->check();
    ?>

    <style>
        .missing {
            color: white;
            background: red;
        }
        .current_old {
            color: black;
            background: yellow;
        }
        .current {
            color: white;
            background: green;
        }
        .updated-text {
            color: forestgreen;
        }

    </style>

    <h2>Database Upgrades</h2>
    <p>Run the database upgrade tool</p>

    <?php if (isset($_GET['db-upgrade-confirm']) && $_GET['db-upgrade-confirm'] == 1) { ?>

        <script>
            jQuery(document).ready(function ($) {
                $('#db-upgrade-confirm-button').prop('disabled', true);
                $('#db-upgrade-confirm').keyup(function () {
                    if ($(this).val().toLowerCase() == 'confirm') {
                        $('#db-upgrade-confirm-button').prop('disabled', false);
                    }
                })
            });
        </script>

        <!-- Confirm Form -->
        <form id="db-upgrade" method="post" action="/wp-admin/admin.php?page=database-upgrades">
            <p>Please type the word <strong>"CONFIRM"</strong> in the box below to run the upgrade:</p>
            <input type="text" name="db-upgrade-confirm" id="db-upgrade-confirm" value="">
            <input type="hidden" name="db-upgrade">
            <?php submit_button('Confirm', 'primary', 'db-upgrade-confirm-button', true); ?>
        </form>
        <!-- /Confirm Form -->

    <?php } else { ?>

        <!-- Form -->
        <form id="db-upgrade" method="post" action="/wp-admin/admin.php?page=database-upgrades&db-upgrade-confirm=1">
            <?php submit_button('Run', 'primary', 'db-upgrade', true); ?>
        </form>
        <!-- /Form -->

    <?php } ?>

    <hr>

    <h3>Database Check</h3>

    <?php if (!empty($tables)) { ?>

        <!-- Show Database Custom Table Info -->
        <table cellpadding="4" cellspacing="0" border="1">
            <tr>
                <td><strong>Table</strong></td>
                <td><strong>Current Version</strong></td>
                <td><strong>Latest Version</strong></td>
                <td><strong>Last Upgraded Date</strong></td>
            </tr>
            <?php
            foreach ($tables as $key => $table) {

                if ($table['exists'] == 1 && ( $table['old_version'] == $table['new_version'] )) {
                    $color = 'current';
                } else if ($table['exists'] == 1 && ( $table['old_version'] != $table['new_version'] )) {
                    $color = 'current_old';
                } else {
                    $color = 'missing';
                }

                $upgraded_date = ( intval($table['upgraded_date']) ) ? date("n-d-y", $table['upgraded_date']) : $table['upgraded_date'];
                ?>
                <tr>
                    <td class="<?php echo $color ?>"><?php echo $table['table_name'] ?></td>
                    <td class="<?php echo $color ?>"><?php echo $table['old_version'] ?></td>
                    <td class="<?php echo $color ?>"><?php echo $table['new_version'] ?></td>
                    <td class="<?php echo $color ?>"><?php echo $upgraded_date ?></td>
                </tr>
            <?php } ?>
            <tr>

            </tr>
        </table>
        <!-- /Show Database Custom Table Info -->
        <?php
    }
}
