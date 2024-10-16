<?php
/*
Plugin Name: Custom Mobile Menu Output
Description: Dynamically display a simplified mobile version of the multilingual menu with a burger menu.
Version: 1.0
Author: Your Name
*/

// Shortcode to display mobile menu
function custom_mobile_menu_output_shortcode($atts) {
    $atts = shortcode_atts( array(
        'menu' => '',  // allow passing the menu name or ID
    ), $atts, 'custom_mobile_menu_output' );

    // If no menu provided, return an error message
    if (empty($atts['menu'])) {
        return '<p>Please specify a menu using the "menu" attribute in the shortcode.</p>';
    }

    // Get the menu items by menu name or ID
    $menu_items = wp_get_nav_menu_items($atts['menu']);

    // Log the menu items for debugging
    error_log(print_r($menu_items, true)); // Logs the menu items in PHP error log for debugging

    if (!$menu_items || is_wp_error($menu_items)) {
        return '<p>Menu not found or an error occurred.</p>'; // Return error message if no menu found
    }

    // Build the HTML for the burger menu and the full-width menu
    ob_start();
    ?>
    <div class="burger-menu">
        <button class="burger-button" onclick="toggleMenu()">☰</button>
        <div class="menu-overlay" id="menuOverlay">
            <div class="menu-content">
                <ul class="main-menu">
                    <?php echo custom_build_menu_tree($menu_items); ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('menuOverlay').classList.toggle('open');
        }
    </script>

    <style>
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }

        .menu-overlay.open {
            display: block;
        }

        .menu-content {
            color: white;
            text-align: center;
            margin-top: 50px;
        }

        .burger-button {
            font-size: 30px;
            cursor: pointer;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
        }

        .main-menu, .submenu {
            list-style-type: none;
            padding: 0;
        }

        .menu-item {
            padding: 10px;
        }

        .menu-link {
            text-decoration: none;
            color: white;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_mobile_menu_output', 'custom_mobile_menu_output_shortcode');

// Recursive function to build menu tree structure
function custom_build_menu_tree($menu_items, $parent_id = 0) {
    $output = '';

    foreach ($menu_items as $item) {
        if ($item->menu_item_parent == $parent_id) {
            $output .= '<li class="menu-item menu-item-' . $item->ID . '">';
            $output .= '<a class="menu-link" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';

            // Get the children
            $children = custom_build_menu_tree($menu_items, $item->ID);
            if ($children) {
                $output .= '<ul class="submenu">' . $children . '</ul>';
            }

            $output .= '</li>';
        }
    }

    return $output;
}

// Hook to add the custom menu to every page (footer or anywhere in the template)
add_action('wp_footer', 'add_custom_mobile_menu_to_footer');

function add_custom_mobile_menu_to_footer() {
    echo do_shortcode('[custom_mobile_menu_output menu="your-menu-name"]'); // Replace with correct menu slug/name

    // Fallback content to test rendering
    echo '<p>Custom menu should be here. If not, check menu name or configuration.</p>';
}

// Add CSS to hide the Divi header across all pages
add_action('wp_head', 'hide_divi_header_css');
function hide_divi_header_css() {
    ?>
    <style>
        #main-header {
            display: none !important;
        }
    </style>
    <?php
}
