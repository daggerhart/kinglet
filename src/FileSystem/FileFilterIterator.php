<?php

namespace Kinglet\Filesystem;

use Iterator;
use FilterIterator;

/**
 * Class FilenameFilterIterator
 *
 * Based on symfony/finder
 *
 * @link https://github.com/symfony/finder/blob/master/Glob.php
 *
 * @package Kinglet\Filesystem
 */
class FileFilterIterator extends FilterIterator {

	/**
	 * File type mode values.
	 */
	const ONLY_FILES = 1;
	const ONLY_DIRECTORIES = 2;

	private $mode;
	protected $matchRegexps = [];
	protected $noMatchRegexps = [];

	/**
	 * @param \Iterator $iterator The Iterator to filter
	 * @param string[] $matchPatterns An array of patterns that need to match
	 * @param string[] $noMatchPatterns An array of patterns that need to not match
	 * @param int $mode File type mode.
	 */
	public function __construct( Iterator $iterator, $matchPatterns = [], $noMatchPatterns = [], $mode = 1 ) {
		$this->mode = $mode;
		foreach ( $matchPatterns as $pattern ) {
			$this->matchRegexps[] = $this->toRegex( $pattern );
		}

		foreach ( $noMatchPatterns as $pattern ) {
			$this->noMatchRegexps[] = $this->toRegex( $pattern );
		}

		parent::__construct( $iterator );
	}

	/**
	 * Filters the iterator values.
	 *
	 * @return bool true if the value should be kept, false otherwise
	 */
	public function accept() {
		// Check the mode for file type.
		$fileinfo = $this->current();
		if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
			return false;
		} elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
			return false;
		}

		return $this->isAccepted($this->current()->getFilename());
	}

	/**
	 * Converts glob to regexp.
	 *
	 * PCRE patterns are left unchanged.
	 * Glob strings are transformed with Glob::toRegex().
	 *
	 * @param string $str Pattern: glob or regexp
	 *
	 * @return string regexp corresponding to a given glob or regexp
	 */
	protected function toRegex( $str ) {
		return $this->isRegex( $str ) ? $str : Glob::toRegex( $str );
	}

	/**
	 * Checks whether the string is accepted by the regex filters.
	 *
	 * If there is no regexps defined in the class, this method will accept the string.
	 * Such case can be handled by child classes before calling the method if they want to
	 * apply a different behavior.
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	protected function isAccepted( $string ) {
		// should at least not match one rule to exclude
		foreach ( $this->noMatchRegexps as $regex ) {
			if ( preg_match( $regex, $string ) ) {
				return FALSE;
			}
		}

		// should at least match one rule
		if ( $this->matchRegexps ) {
			foreach ( $this->matchRegexps as $regex ) {
				if ( preg_match( $regex, $string ) ) {
					return TRUE;
				}
			}

			return FALSE;
		}

		// If there is no match rules, the file is accepted
		return TRUE;
	}

	/**
	 * Checks whether the string is a regex.
	 *
	 * @return bool
	 */
	protected function isRegex( $str ) {
		if ( preg_match( '/^(.{3,}?)[imsxuADU]*$/', $str, $m ) ) {
			$start = substr( $m[1], 0, 1 );
			$end = substr( $m[1], - 1 );

			if ( $start === $end ) {
				return ! preg_match( '/[*?[:alnum:] \\\\]/', $start );
			}

			foreach (
				[
					[ '{', '}' ],
					[ '(', ')' ],
					[ '[', ']' ],
					[ '<', '>' ]
				] as $delimiters
			) {
				if ( $start === $delimiters[0] && $end === $delimiters[1] ) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

}
