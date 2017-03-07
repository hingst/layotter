<?php

namespace Layotter\Upgrades;

class MigrationHelper {

	const UPGRADE_OPTION = 'layotter_needs_upgrade';

	/**
	 * MigrationHelper constructor.
	 */
	public function __construct() {
		if ( $this->needs_upgrade() ) {
			# @TODO: the magic!
		}
	}

	/**
	 * @return bool
	 */
	public static function needs_upgrade() {
		$needs_upgrade = \get_option( self::UPGRADE_OPTION );

		if ( ctype_digit( $needs_upgrade ) ) {
			return (bool) $needs_upgrade;
		}

		$has_upgradable_posts = (bool) self::count_upgradable_posts();

		if ( $has_upgradable_posts ) {
			\update_option( self::UPGRADE_OPTION, 1 );
		}

		return true;
	}

	/**
	 * @return int
	 */
	private static function count_upgradable_posts() {
		$upgradable_posts = self::get_upgradable_post_ids();

		return count( $upgradable_posts );
	}

	public static function post_needs_upgrade($id) {
	    return in_array($id, self::get_upgradable_post_ids());
    }

	/**
	 * @return array|null|object
	 */
	private static function get_upgradable_post_ids() {
		global $wpdb;
		$q = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE '[layotter%[/layotter]' OR ID IN(SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key='layotter_json')";

		$flatten = function ( $value ) {
			return reset($value);
		};

		return array_map( $flatten, $wpdb->get_results( $q, ARRAY_N ) );
	}

}