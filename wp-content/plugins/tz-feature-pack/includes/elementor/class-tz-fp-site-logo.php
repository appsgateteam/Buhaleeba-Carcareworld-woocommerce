<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 24/07/2018
 * Time: 14:49
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Utils;

class Widget_TZ_Site_Logo extends Widget_Base{

    public function get_name() {
        return 'tz-site-logo';
    }

    public function get_title() {
        return esc_html__( 'Site Logo', 'tz-feature-pack' );
    }

    public function get_icon() {
        return 'fa fa-paper-plane';
    }

    public function get_categories() {
        return [ 'themes-zone-elements' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_settings',
            [
                'label' => esc_html__( 'Content', 'tz-feature-pack'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'tag_on',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'tz-feature-pack'),
                'label_on' => esc_html__( 'Yes', 'tz-feature-pack'),
                'return_value' => 'yes',
                'default' => 'yes',
                'label' => esc_html__( 'Show tag-line?', 'tz-feature-pack'),
            ]
        );

        $this->add_control(
            'text_logo_on',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'tz-feature-pack'),
                'label_on' => esc_html__( 'Yes', 'tz-feature-pack'),
                'return_value' => 'yes',
                'default' => 'yes',
                'label' => esc_html__( 'Show Text Logo?', 'tz-feature-pack'),
            ]
        );

        $this->add_control(
            'mobile_logo_img',
            [
                'label' => esc_html__( 'Mobile Logo Image', 'tz-feature-pack' ),
                'description' => esc_html__( 'Select image for banner background.', 'tz-feature-pack' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'mobile_logo_size',
            [
                'label' => esc_html__( 'Mobile Logo Image size', 'tz-feature-pack' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'full',
                'options' => [
                    'thumbnail' => esc_html__('Thumbnail', 'tz-feature-pack'),
                    'medium' => esc_html__('Medium', 'tz-feature-pack'),
                    'large' => esc_html__('Large', 'tz-feature-pack'),
                    'full' => esc_html__('Full', 'tz-feature-pack'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__( 'Content Styling', 'tz-feature-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'logo_color',
            [
                'label' => __( 'Color', 'tz-feature-pack' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .site-branding .site-title, {{WRAPPER}} .site-branding .site-title a, {{WRAPPER}} .site-branding .site-description' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'logo_typography',
                'selector' => '{{WRAPPER}} .site-branding .site-title, {{WRAPPER}} .site-branding .site-description',
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            ]
        );

        $this->add_responsive_control(
            'cont_align',
            [
                'label' => __('Content Alignment', 'tz-feature-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'tz-feature-pack'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'tz-feature-pack'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'tz-feature-pack'),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'tz-feature-pack'),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .site-branding.tz-logo .site-title, {{WRAPPER}} .site-branding.tz-logo .site-description' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
            ]
        );

        $this->add_responsive_control(
            'logo_text_space',
            [
                'label' => __( 'Spacing under Logo', 'tz-feature-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .site-title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();

        ?>
        <div class="site-branding tz-logo">
            <?php

            the_custom_logo();

            if ( $settings['mobile_logo_img'] && $settings['mobile_logo_img']['id']!='' ) :
                $mobile_logo_img = wp_get_attachment_image( $settings['mobile_logo_img']['id'], $settings['mobile_logo_size'], false, ['class'=>'tz-mobile-logo'] );
            ?>

            <a class="mobile-logo-link" href="<?php echo esc_url(home_url('/'))?>" rel="home">
                <?php echo wp_kses_post($mobile_logo_img); ?>
            </a>

            <?php  endif; ?>


            <?php

            if ( ( 'yes' == $settings['text_logo_on'] ) )
                if ( ( is_front_page() && is_home() ) ) : ?>
                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php else : ?>
                    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                <?php
                endif;
            $description = get_bloginfo( 'description', 'display' );
            if ( ( 'yes' == $settings['tag_on'] ) && ( $description || is_customize_preview() ) ) : ?>
                <p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
            <?php
            endif;

            ?>
        </div><!-- .site-branding -->
        <?php
    }

}