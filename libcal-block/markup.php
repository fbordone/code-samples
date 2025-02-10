<?php
/**
 * Libcal (Compact) block markup.
 *
 * For demo purposes, this file outputs a simplified event card.
 *
 * @package Cpl
 */

// Set defaults.
$attributes = wp_parse_args(
	$attributes,
	[
		'colorScheme'     => 'dark',
		'eventDaysAhead'  => 30,
		'showDescription' => true,
		'numberOfEvents'  => 1,
		'eventLocations'  => [],
		'eventCategories' => [],
		'eventTags'       => [],
	]
);

$event_calendar_url = 'https://cpl.libcal.com/calendar/events';

// Instantiate the LibCal class.
$libcal = new \CplPlugin\LibCal\LibCal();
$events = $libcal->get_events( $attributes );

// Set the wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'theme-' . $attributes['colorScheme'] ) );
?>

<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wp-block-cpl-libcal-compact__header">
		<h2 class="wp-block-cpl-libcal-compact__title"><?php esc_html_e( 'Upcoming Events', 'cpl' ); ?></h2>

		<a href="<?php echo esc_url( $event_calendar_url ); ?>" target="_blank" class="wp-element-button is-style-arrow">
			<?php esc_html_e( 'All events', 'cpl' ); ?>
		</a>
	</div>

	<?php if ( empty( $events ) ) : ?>
		<p class="wp-block-cpl-libcal-compact__no-results"><?php esc_html_e( 'No upcoming events found.', 'cpl' ); ?></p>
	<?php else : ?>
		<div class="wp-block-cpl-libcal-compact__events">
			<?php foreach ( $events as $event ) : ?>
				<div class="wp-block-cpl-libcal-compact__event">
					<h3><?php echo esc_html( $event['title'] ); ?></h3>
					<p><?php echo esc_html( $event['date'] . ' ' . $event['start_time'] . ' - ' . $event['end_time'] ); ?></p>

					<?php if ( $attributes['showDescription'] && $event['description'] ) : ?>
						<p><?php echo esc_html( $event['description'] ); ?></p>
					<?php endif; ?>

					<a href="<?php echo esc_url( $event['link'] ); ?>"><?php esc_html_e( 'View Event', 'cpl' ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
