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
/**
 * Samle og saniter skjemafeltene fra en innsendings-kilde (typisk $_POST).
 *
 * @param array $src Rådata.
 * @return array Sanerte felt.
 */
function reer_landing_collect_fields( $src ) {
	return array(
		'navn'        => isset( $src['navn'] ) ? sanitize_text_field( wp_unslash( $src['navn'] ) ) : '',
		'telefon'     => isset( $src['telefon'] ) ? sanitize_text_field( wp_unslash( $src['telefon'] ) ) : '',
		'epost'       => isset( $src['epost'] ) ? sanitize_email( wp_unslash( $src['epost'] ) ) : '',
		'fodselsdato' => isset( $src['fodselsdato'] ) ? sanitize_text_field( wp_unslash( $src['fodselsdato'] ) ) : '',
		'kurs'        => isset( $src['kursvalg'] ) ? sanitize_text_field( wp_unslash( $src['kursvalg'] ) ) : '',
		'laerer'      => isset( $src['laerer'] ) ? sanitize_text_field( wp_unslash( $src['laerer'] ) ) : '',
		'melding'     => isset( $src['melding'] ) ? sanitize_textarea_field( wp_unslash( $src['melding'] ) ) : '',
		'kilde'       => isset( $src['kilde'] ) ? sanitize_text_field( wp_unslash( $src['kilde'] ) ) : '',
	);
}

/**
 * Bygg og send påmeldings-e-posten til reer@reer.no.
 *
 * Tidsstempelet tvinges til norsk tid (Europe/Oslo) uavhengig av
 * WordPress' tidssone-innstilling, slik at klokkeslettet alltid stemmer.
 *
 * @param array $f Sanerte felt fra reer_landing_collect_fields().
 * @return bool True hvis wp_mail lyktes.
 */
function reer_landing_send_mail( $f ) {
	$blogname  = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$subject   = sprintf( 'Ny henvendelse fra nettsiden – %s', $f['navn'] );
	$sendt_tid = wp_date( 'd.m.Y H:i', null, new DateTimeZone( 'Europe/Oslo' ) );

	// Vis fødselsdato som dd.mm.åååå hvis den er en gyldig dato.
	$fdato = '';
	if ( '' !== $f['fodselsdato'] ) {
		$dt    = DateTime::createFromFormat( 'Y-m-d', $f['fodselsdato'] );
		$fdato = $dt ? $dt->format( 'd.m.Y' ) : $f['fodselsdato'];
	}

	$lines = array(
		'Ny henvendelse via ' . $blogname,
		'',
		'Navn:        ' . $f['navn'],
		'Telefon:     ' . $f['telefon'],
		'E-post:      ' . ( '' !== $f['epost'] ? $f['epost'] : '(ikke oppgitt)' ),
		'Fødselsdato: ' . ( '' !== $fdato ? $fdato : '(ikke oppgitt)' ),
		'Kurs:        ' . ( '' !== $f['kurs'] ? $f['kurs'] : '(ikke valgt)' ),
		'Lærer:       ' . ( '' !== $f['laerer'] ? $f['laerer'] : '(ikke valgt)' ),
		'Hørte om oss: ' . ( '' !== $f['kilde'] ? $f['kilde'] : '(ikke oppgitt)' ),
		'',
		'Melding:',
		( '' !== $f['melding'] ? $f['melding'] : '(ingen melding)' ),
		'',
		'---',
		'Sendt ' . $sendt_tid . ' fra ' . home_url( '/' ),
	);
	$body = implode( "\n", $lines );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( '' !== $f['epost'] && is_email( $f['epost'] ) ) {
		$headers[] = 'Reply-To: ' . $f['navn'] . ' <' . $f['epost'] . '>';
	}

	return (bool) wp_mail( REER_LANDING_SIGNUP_TO, $subject, $body, $headers );
}

/**
 * Server-håndtering av skjemaet (fallback uten JavaScript).
 *
 * Poster til samme URL. Nonce + honeypot mot spam, wp_mail til
 * reer@reer.no, og Post/Redirect/Get med ?reer_sendt=1 / ?reer_feil=1.
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

	$f = reer_landing_collect_fields( $_POST );

	// Navn og mobilnummer er påkrevd.
	if ( '' === $f['navn'] || '' === $f['telefon'] ) {
		wp_safe_redirect( add_query_arg( 'reer_feil', '1', $base ) . '#pamelding' );
		exit;
	}

	$sent = reer_landing_send_mail( $f );
	$arg  = $sent ? array( 'reer_sendt' => '1' ) : array( 'reer_feil' => '1' );
	wp_safe_redirect( add_query_arg( $arg, $base ) . '#pamelding' );
	exit;
}
add_action( 'template_redirect', 'reer_landing_handle_signup' );

/**
 * AJAX-håndtering av skjemaet (uten omlasting). Svarer med JSON.
 *
 * JavaScript sender skjemaet hit; server-veien over er fallback hvis
 * JS ikke er tilgjengelig eller AJAX feiler.
 */
function reer_landing_ajax_signup() {
	check_ajax_referer( 'reer_signup_ajax', 'nonce' );

	// Honeypot: lat som alt gikk bra.
	if ( ! empty( $_POST['reer_hp'] ) ) {
		wp_send_json_success();
	}

	$f = reer_landing_collect_fields( $_POST );
	if ( '' === $f['navn'] || '' === $f['telefon'] ) {
		wp_send_json_error( 'Fyll inn navn og mobilnummer.' );
	}

	if ( reer_landing_send_mail( $f ) ) {
		wp_send_json_success();
	}
	wp_send_json_error( 'Kunne ikke sende akkurat nå. Prøv igjen, eller ring 930 20 620.' );
}
add_action( 'wp_ajax_reer_signup', 'reer_landing_ajax_signup' );
add_action( 'wp_ajax_nopriv_reer_signup', 'reer_landing_ajax_signup' );
