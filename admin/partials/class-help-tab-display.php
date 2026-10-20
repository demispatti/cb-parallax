<?php

namespace CbParallax\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * The class responsible for creating and displaying the help tab.
 *
 * @since             0.9.0
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_help_tab_display
{
    /**
     * Returns a string containing the 'Help Tab' content.
     *
     * @param string $domain
     *
     * @return string $html
     * @since 0.9.0
     */
    public static function help_tab_display(string $domain) : string
    {
        ob_start();
        ?>

        <div id="cb-parallax-help-tabs">
            <ul class="nav">
                <li><a href="#tabs-1"><?php esc_html_e('General Settings', 'cb-parallax') ?></a></li>
                <li><a href="#tabs-2"><?php esc_html_e('Scroll Directions', 'cb-parallax') ?></a></li>
                <li><a href="#tabs-3"><?php esc_html_e('Plugin Information and Privacy Notices', 'cb-parallax') ?></a></li>
            </ul>
            <div id="tabs-1"
                 class="cb-parallax-help-tabs"><?php echo wp_kses_post(self::tab_content_general_settings($domain)) ?></div>
            <div id="tabs-2"
                 class="cb-parallax-help-tabs"><?php echo wp_kses_post(self::tab_content_scroll_directions($domain)) ?></div>
            <div id="tabs-3"
                 class="cb-parallax-help-tabs"><?php echo wp_kses_post(self::tab_content_plugin_information_and_privacy_notices($domain)) ?></div>
        </div>
        
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Returns a string containing the content of this 'Help Tab' tab.
     *
     * @param string $domain
     *
     * @return string $html
     * @since 0.9.0
     */
    public static function tab_content_general_settings(string $domain) : string
    {
        /**
         * @todo: extract texts from quickfix-images into html
         */
        $img_postfix = 'de_DE' === get_locale() ? 'de' : 'default';
        ob_start();
        ?>

        <div class="item-container">
            <h4 class="item-title">
                <?php esc_html_e('Background Image Settings', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-1/settings-page-'.$img_postfix.'.png') ?>"
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>

            <h4 class="item-title">
                <?php esc_html_e('Manual adjustments', 'cb-parallax') ?>
            </h4>
            <div class="item-description">
                <?php esc_html_e('Since WordPress themes differ in structure, this step is not (yet) automated: It will most likely be necessary to set a "boxed" layout or to add opacity to container(s) which conceal the background image.', 'cb-parallax') ?>
            </div>
        </div>
        
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Returns a string containing the content of this 'Help Tab' tab.
     *
     * @param string $domain
     *
     * @return string $html
     * @since 0.9.0
     */
    public static function tab_content_scroll_directions(string $domain) : string
    {
        $img_postfix = 'de_DE' === get_locale() ? 'de' : 'default';
        ob_start();
        ?>

        <div class="item-container">
            <h4 class="item-title">
                <?php esc_html_e('Vertical Image Direction', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-2/vertical-'.$img_postfix.'.png') ?> "
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>


            <h4 class="item-title">
                <?php esc_html_e('To Top', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-2/vertical-to-top-'.$img_postfix.'.png') ?> "
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>


            <h4 class="item-title">
                <?php esc_html_e('To Bottom', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-2/vertical-to-bottom-'.$img_postfix.'.png') ?> "
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>
        </div>


        <div class="item-container">
            <h4 class="item-title">
                <?php esc_html_e('Horizontal Image Direction', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-2/horizontal-'.$img_postfix.'.png') ?> "
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>


            <h4 class="item-title">
                <?php esc_html_e('To The Left', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-2/horizontal-to-the-left-'.$img_postfix.'.png') ?> "
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>


            <h4 class="item-title">
                <?php esc_html_e('To The Right', 'cb-parallax') ?>
            </h4>
            <div class="image-holder">
                <img src="<?php echo esc_url(CBPARALLAX_ROOT_URL.'admin/images/contextual-help/tab-2/horizontal-to-the-right-'.$img_postfix.'.png') ?> "
                     alt="Contextual Help Image"/>
            </div>
            <div class="item-description">
                <?php /*echo __( 'Soon, there will be more.', "cb-parallax" )*/ ?>
            </div>
        </div>
        
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Returns a string containing the content of this 'Help Tab' tab.
     *
     * @param string $domain
     *
     * @return string $html
     * @since 0.9.0
     */
    public static function tab_content_plugin_information_and_privacy_notices(string $domain) : string
    {
        ob_start();
        ?>

        <div class="item-container">
            <div class="item-description">
                <h4><?php esc_html_e('Privacy notices', 'cb-parallax') ?></h4>
                <span><?php esc_html_e('This plugin does not:', 'cb-parallax') ?></span>
                <ul class="list">
                    <li>1. <?php esc_html_e('Track users', 'cb-parallax') ?></li>
                    <li>2. <?php esc_html_e('Process personal user data', 'cb-parallax') ?></li>
                    <li>3. <?php esc_html_e('Use cookies', 'cb-parallax') ?></li>
                </ul>
            </div>
        </div>
        
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
}
