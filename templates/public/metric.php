<?php 
/**
 * Variables esperadas:
 * $card_style, $image_html, $anim, $value_style, $data_attr, $final_output, $label
 */
?>
<div class="acm-widget-card" <?php echo $card_style; ?>>
    <?php echo $image_html; ?>
    <div class="acm-value acm-anim-<?php echo esc_attr( $anim ); ?>" <?php echo $value_style; ?> <?php echo $data_attr; ?>>
        <?php echo $final_output; ?>
    </div>
    <div class="acm-label">
        <?php echo esc_html( $label ); ?>
    </div>
</div>