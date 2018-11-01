<?php

/**
 * The file extends the Walker_Nav_Menu class to customize the menu option;
 * @since 1.0.0
 */
class Walker_DSP_Submenu extends Walker_Nav_Menu {

    /**
     * Ends the element output.
     * 
     * @global type $dsp_theme_options
     * @param type $output
     * @param type $item
     * @param type $depth
     * @param type $args
     */
    function end_el(&$output, $item, $depth = 0, $args = array()) {
        global $dsp_theme_options;
        $posts = $dsp_theme_options['opt-menu-sorter']['enabled'];
        unset($posts['placebo']);
        if ($dsp_theme_options['opt-menu-title'] == $item->title) {
            $output .= '<ul class="dropdown-menu position-absolute" role="menu">';
            foreach ($posts as $key => $value):
                $output .= '<li><a href="/category/' . $key . '">' . $value . '</a></li>';
            endforeach;
            $output .= '</ul>';
        }
    }

}
