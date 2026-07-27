<?php
/**
 * Plugin Name:       Reer Landingsside
 * Plugin URI:        https://reer.no/
 * Description:        Publiserer den ferdige forsiden til Reer & Horten Trafikkskole som en fullbredde-sidemal, uavhengig av det aktive temaet. Påmeldingsskjemaet sender e-post til reer@reer.no via WordPress (wp_mail).
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      5.6
 * Author:            Reer & Horten Trafikkskole
 * License:           GPL-2.0-or-later
 * Text Domain:       reer-landing
 *
 * @package Reer_Landing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Ingen direkte tilgang.
}

define( 'REER_LANDING_TEMPLATE', 'reer-landing' );
define( 'REER_LANDING_SIGNUP_TO', 'reer@reer.no' );

/**
 * Gjør sidemalen valgbar i sideredigeringen (Sideattributter → Mal).
 *
 * @param array $templates Eksisterende sidemaler.
 * @return array
 */
function reer_landing_register_template( $templates ) {
	$templates[ REER_LANDING_TEMPLATE ] = __( 'Reer – Forside (fullbredde)', 'reer-landing' );
	return $templates;
}
add_filter( 'theme_page_templates', 'reer_landing_register_template' );

/**
 * Last inn vår egen mal – som rendrer hele dokumentet selv – når en side
 * bruker «Reer – Forside (fullbredde)». Uavhengig av det aktive temaet.
 *
 * @param string $template Stien til malen WordPress ellers ville brukt.
 * @return string
 */
function reer_landing_use_template( $template ) {
	if ( is_page() ) {
		$slug = get_page_template_slug( get_queried_object_id() );
		if ( REER_LANDING_TEMPLATE === $slug ) {
			$custom = plugin_dir_path( __FILE__ ) . 'template-landing.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}
	return $template;
}
add_filter( 'template_include', 'reer_landing_use_template' );

/**
 * Håndter påmeldingsskjemaet på front-end.
 *
 * Poster til samme URL. Bruker nonce + honeypot mot spam, sender med
 * wp_mail() til reer@reer.no, og redirigerer tilbake (Post/Redirect/Get)
 * med ?reer_sendt=1 (ok) eller ?reer_feil=1 (feil).
 */
function reer_landing_handle_signup() {
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
		return;
	}
	if ( ! isset( $_POST['reer_signup_nonce'] ) ) {
		return;
	}

	$base = remove_query_arg( array( 'reer_sendt', 'reer_feil' ) );

	// Ugyldig nonce → behandle som feil, men avslør ingenting.
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['reer_signup_nonce'] ) ), 'reer_signup' ) ) {
		wp_safe_redirect( add_query_arg( 'reer_feil', '1', $base ) . '#pamelding' );
		exit;
	}

	// Honeypot: bots fyller ut det skjulte feltet. Lat som alt gikk bra.
	if ( ! empty( $_POST['reer_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'reer_sendt', '1', $base ) . '#pamelding' );
		exit;
	}

	$navn    = isset( $_POST['navn'] ) ? sanitize_text_field( wp_unslash( $_POST['navn'] ) ) : '';
	$telefon = isset( $_POST['telefon'] ) ? sanitize_text_field( wp_unslash( $_POST['telefon'] ) ) : '';
	$epost   = isset( $_POST['epost'] ) ? sanitize_email( wp_unslash( $_POST['epost'] ) ) : '';
	$kurs    = isset( $_POST['kursvalg'] ) ? sanitize_text_field( wp_unslash( $_POST['kursvalg'] ) ) : '';
	$laerer  = isset( $_POST['laerer'] ) ? sanitize_text_field( wp_unslash( $_POST['laerer'] ) ) : '';
	$melding = isset( $_POST['melding'] ) ? sanitize_textarea_field( wp_unslash( $_POST['melding'] ) ) : '';

	// Navn og mobilnummer er påkrevd.
	if ( '' === $navn || '' === $telefon ) {
		wp_safe_redirect( add_query_arg( 'reer_feil', '1', $base ) . '#pamelding' );
		exit;
	}

	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$subject  = sprintf( 'Ny påmelding fra nettsiden – %s', $navn );

	$lines = array(
		'Ny påmelding via ' . $blogname,
		'',
		'Navn:     ' . $navn,
		'Telefon:  ' . $telefon,
		'E-post:   ' . ( '' !== $epost ? $epost : '(ikke oppgitt)' ),
		'Kurs:     ' . ( '' !== $kurs ? $kurs : '(ikke valgt)' ),
		'Lærer:    ' . ( '' !== $laerer ? $laerer : '(ikke valgt)' ),
		'',
		'Melding:',
		( '' !== $melding ? $melding : '(ingen melding)' ),
		'',
		'---',
		'Sendt ' . wp_date( 'd.m.Y H:i' ) . ' fra ' . home_url( '/' ),
	);
	$body = implode( "\n", $lines );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( '' !== $epost && is_email( $epost ) ) {
		$headers[] = 'Reply-To: ' . $navn . ' <' . $epost . '>';
	}

	$sent = wp_mail( REER_LANDING_SIGNUP_TO, $subject, $body, $headers );

	$arg = $sent ? array( 'reer_sendt' => '1' ) : array( 'reer_feil' => '1' );
	wp_safe_redirect( add_query_arg( $arg, $base ) . '#pamelding' );
	exit;
}
add_action( 'template_redirect', 'reer_landing_handle_signup' );
