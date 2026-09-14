<?php
/**
 * "How it works" section on the homepage.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$emr_steps = array(
	array(
		'title' => __( 'Zoek en filter', 'eazymatch-recruitment' ),
		'text'  => __( 'Filter het vacatureaanbod op vakgebied, regio en aantal uren tot je de vacatures ziet die bij je passen.', 'eazymatch-recruitment' ),
	),
	array(
		'title' => __( 'Solliciteer online', 'eazymatch-recruitment' ),
		'text'  => __( 'Vul het sollicitatieformulier in en voeg je cv toe. Je ontvangt direct een bevestiging per e-mail.', 'eazymatch-recruitment' ),
	),
	array(
		'title' => __( 'Kennismaken', 'eazymatch-recruitment' ),
		'text'  => __( 'Een recruiter neemt contact met je op, bespreekt de vacature en plant een kennismaking in.', 'eazymatch-recruitment' ),
	),
);
?>
<section class="emr-section">
	<div class="emr-container">
		<div class="emr-section__head emr-section__head--center">
			<span class="emr-eyebrow"><?php esc_html_e( 'Zo werkt het', 'eazymatch-recruitment' ); ?></span>
			<h2><?php esc_html_e( 'In drie stappen naar een nieuwe baan', 'eazymatch-recruitment' ); ?></h2>
		</div>

		<div class="emr-grid emr-grid--3 emr-steps">
			<?php foreach ( $emr_steps as $emr_step ) : ?>
				<div class="emr-card emr-step">
					<span class="emr-card__icon" aria-hidden="true"></span>
					<h3><?php echo esc_html( $emr_step['title'] ); ?></h3>
					<p><?php echo esc_html( $emr_step['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
