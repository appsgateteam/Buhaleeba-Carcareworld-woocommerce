<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $post;
$woobadges_values = get_post_meta($post->ID, 'woobadge_product', true);
$position_checked = isset($woobadges_values['position']) ? $woobadges_values['position'] : 'none';

$positions = WOOBADGES::get_positions();
?>

<div class="woobadge-config">
    <h4><?=__('Position', 'badges-woo')?></h4>
    <div class="row">

        <?php
            
            foreach($positions as $pos) {
                echo '<label class="col featured-image-woobadges" style="background-image:url('.get_the_post_thumbnail_url($post->ID).')">
                    <input type="radio" class="woobadges_radio" name="woobadges_position" '.checked($position_checked, $pos, false).' value="'.$pos.'">
                    <img src="'.WOOBADGES_URL . 'img/'.$pos.'.png" />
                </label>';
            }
        
        ?>
        
    </div>
    <br>
    <div>
        <div class="woobadges-input">
            <h4><?=__('Opacity', 'badges-woo')?> <span class="woobadges_opacity_value"><?= isset($woobadges_values['opacity']) ? $woobadges_values['opacity'] : '1' ?></span></h4>
            <div class="slidecontainer">
                <input type="range" name="woobadges_opacity" min="0" max="1" step="0.1" value="<?= isset($woobadges_values['opacity']) ? $woobadges_values['opacity'] : '1' ?>" class="slider">
                <p></p>
            </div>
        </div>

        <div class="woobadges-input">
            <h4><?=__('Text', 'badges-woo')?></h4>
            <input type="text" name="woobadges_text" placeholder="Sale Off 50%" value="<?= isset($woobadges_values['text']) ? $woobadges_values['text'] : '' ?>" />
            <p class="description"><?= __('You can insert emojis, <a target="_blank" href="https://taxarpro.com/en/emojis-for-your-website/">see link</a>', 'badges-woo')?></p>
        </div>

        <div class="woobadges-input">
            <h4><?=__('Background Color', 'badges-woo')?></h4>
            <input type="text" name="woobadges_background" class="colorPicker" data-default-color="#333" value="<?= isset($woobadges_values['background']) ? $woobadges_values['background'] : '' ?>" />
        </div>

        <div class="woobadges-input">
            <h4><?=__('Color', 'badges-woo')?></h4>
            <input type="text" name="woobadges_color" class="colorPicker" data-default-color="#FFF" value="<?= isset($woobadges_values['color']) ? $woobadges_values['color'] : '' ?>" />
        </div>

        <div class="woobadges-input">
            <h4><?=__('Font Size', 'badges-woo')?></h4>
            <input type="text" name="woobadges_fontSize" placeholder="12px" value="<?= isset($woobadges_values['fontSize']) ? $woobadges_values['fontSize'] : '12px' ?>" />
        </div>

        <div class="woobadges-input">
            <h4><?=__('Font Weight', 'badges-woo')?></h4>
            <input type="text" name="woobadges_fontWeight" placeholder="normal" value="<?= isset($woobadges_values['fontWeight']) ? $woobadges_values['fontWeight'] : 'normal' ?>" />
        </div>
        
        <div class="woobadges-input">
            <h4><?=__('Show on single product page', 'badges-woo')?></h4>
            <input type="checkbox" name="woobadges_showSingle" value="1" <?= isset($woobadges_values['showSingle']) ? 'checked="checked"' : '' ?> />
        </div>

        <div class="woobadges-input">
            <h4><?=__('Zoom for single product page', 'badges-woo')?> <span class="woobadges_zoom_value"><?= isset($woobadges_values['zoomSingleProduct']) ? $woobadges_values['zoomSingleProduct'] : '1' ?></span></h4>
            <div class="slidecontainer">
                <input type="range" name="woobadges_zoomSingleProduct" min="0.5" max="5" step="0.1" value="<?= isset($woobadges_values['zoomSingleProduct']) ? $woobadges_values['zoomSingleProduct'] : '1' ?>" class="slider">
                <p></p>
            </div>
        </div>

        
    </div>
</div>
<style>
.woobadges-input .slidecontainer {
  width: 100%;
}
.woobadges-input input[type='text'] {
    width: 100%;
}

.woobadges-input .slider {
  -webkit-appearance: none;
  appearance: none;
  
  height: 25px;
  border-radius:50px;
  background: #d3d3d3;
  outline: none;
  opacity: 0.7;
  -webkit-transition: .2s; 
  transition: opacity .2s;
}

#side-sortables .woobadges-input .slider {
    width: 100%;
}

.woobadges-input .slider:hover {
  opacity: 1;
}

.woobadges-input .slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 25px;
  height: 25px;
  border-radius:100px;
  background: #0073AA;
  cursor: pointer;
}

.woobadges-input .slider::-moz-range-thumb {
  width: 25px;
  height: 25px;
  background: #0073AA;
  cursor: pointer;
}
.woobadge-config .row {
    width: 100%;
}

.woobadge-config .row .col {
    width: 50px;
    height: 50px;
    float: left;
    margin: 10px;
    background-position: center !important;
    background-size: cover !important;
    background:#CCC;
}

.woobadge-config .row .col img {
    width: 100%;
}

.woobadge-config .row:after {
    content: '';
    clear: both;
    display: block;
}
/* HIDE RADIO */
.woobadges_radio[type=radio] { 
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* IMAGE STYLES */
.woobadges_radio[type=radio] + img {
    cursor: pointer;
}

/* CHECKED STYLES */
.woobadges_radio[type=radio]:checked + img {
    outline: 2px solid #279edb;
}

</style>