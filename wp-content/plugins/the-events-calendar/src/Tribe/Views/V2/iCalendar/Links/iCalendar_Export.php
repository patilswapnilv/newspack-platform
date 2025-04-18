<?php
/**
 * Handles iCalendar export links.
 *
 * @since   5.12.0
 *
 * @package Tribe\Events\Views\V2\iCalendar
 */

namespace Tribe\Events\Views\V2\iCalendar\Links;
use Tribe\Events\Views\V2\View;
use Tribe__Events__Main;

/**
 * Class iCal
 *
 * @since   5.12.0
 *
 * @package Tribe\Events\Views\V2\iCalendar
 */
class iCalendar_Export extends Link_Abstract {
	/**
	 * {@inheritDoc}
	 */
	public static $slug = 'ics';

	/**
	 * {@inheritDoc}
	 */
	public function register() {
		add_filter( 'tec_views_v2_subscribe_link_ics_visibility', [ $this, 'filter_tec_views_v2_subscribe_link_ics_visibility'], 10, 2 );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function label(): string {
		return __( 'Export .ics file', 'the-events-calendar' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function single_label(): string {
		return $this->label();
	}

	/**
	 * Filters the is_visible() function to not display on single events.
	 *
	 * @since 5.14.0
	 *
	 * @param boolean $visible Whether to display the link.
	 * @param View    $view     The current View object.
	 *
	 * @return boolean $visible Whether to display the link.
	 */
	public function filter_tec_views_v2_subscribe_link_ics_visibility( $visible ) {
		// Don't display on single event by default.
		return ! is_single();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_uri( View $view = null ) {
		if ( null === $view || is_single( Tribe__Events__Main::POSTTYPE ) ) {
			// Try to construct it for the event single.
			return add_query_arg( [ 'ical' => 1 ], get_the_permalink() );
		}

		$ical = $view->get_ical_data();

		if ( empty( $ical->display_link ) ) {
			return '';
		}

		if ( empty( $ical->link->url ) ) {
			return '';
		}

		/**
		 * Filters the iCalendar export URL.
		 *
		 * @since 6.11.0
		 *
		 * @param string $feed_url The iCalendar export URL.
		 * @param View   $view The view.
		 */
		return (string) apply_filters( 'tec_views_v2_subscribe_links_ics_feed_url', $ical->link->url, $view );
	}
}
