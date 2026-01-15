<?php 
/**
 * Variables esperadas:
 * $card_style, $image_html, $anim, $value_style, $data_attr, $final_output, $label, $url
 */

$tag = ! empty( $url ) ? 'a' : 'div';
$href_attr = ! empty( $url ) ? 'href="' . esc_url( $url ) . '"' : '';
$link_class = ! empty( $url ) ? 'acm-is-link' : '';
$extra_style = ! empty( $url ) ? 'text-decoration: none; color: inherit; display: block;' : '';
?>

<<?php echo $tag; ?> class="acm-widget-card <?php echo $link_class; ?>" <?php echo $href_attr; ?> <?php echo $card_style; ?> style="<?php echo $extra_style; ?>">
    <?php echo $image_html; ?>
    <div class="acm-value acm-anim-<?php echo esc_attr( $anim ); ?>" <?php echo $value_style; ?> <?php echo $data_attr; ?>>
        <?php echo $final_output; ?>
    </div>
    <div class="acm-label">
        <?php echo esc_html( $label ); ?>
    </div>
</<?php echo $tag; ?>>