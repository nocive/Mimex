<?php

/**
 * Mimex
 * Simple class for converting extension to mimetypes and vice versa.
 * It also detects file mimetypes using PHP Fileinfo extension.
 * This code was inspired by http://goo.gl/KsTLx
 *
 * @package	Mimex
 * @author	Jose' Pedro Saraiva <nocive at gmail.com>
 */
class Mimex
{
        /**
         * Enter description here ...
         *
         * @var		string
         * @access	public
         */
	public static $mimetypesMap = 'mime.types';


        /**
         * Enter description here ...
         *
         * @param	string $file
         * @param	bool $realDetect
         * @return	string
         */
        public static function extension( $file, $realDetect = true )
	{
		if ($realDetect) {
			return self::mimetypeToExtension( self::detectMimetype( $file ) );
		}
                return strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
        } // extension }}}


        /**
         * Enter description here ...
         *
         * @param	string $file
         * @param	bool $realDetect
         * @return	string
         */
        public static function mimetype( $file, $realDetect = true )
	{
		if ($realDetect) {
			return self::detectMimetype( $file );
		}
                return self::extensionToMimetype( self::extension( $file ) );
	} // mimetype }}}


	/**
	 * Attempts to detect file mimetype using PHP Fileinfo
	 *
	 * @param	string $file
	 * @return	string
	 */
	public static function detectMimetype( $file )
	{
		static $finfo;

		if (! extension_loaded( 'fileinfo' )) {
			throw new Exception( 'Can\'t detect mimetype, Fileinfo extension not loaded' );
		}
		if (! $finfo) {
			$finfo = new finfo( FILEINFO_MIME_TYPE );
		}
		$mimetype = $finfo->file( $file );
		if ($mimetype === 'image/x-ico') {
			$mimetype = 'image/x-icon';
		}
		return $mimetype;
	}


        /**
         * Enter description here ...
         *
         * @param	string $file
         * @return	string
         */
        public static function extensionToMimetype( $ext )
        {
                static $types;
                if (! isset( $types )) {
                        $types = self::extensionsMimetypes();
                }
                $ext = strtolower( $ext );
                return isset( $types[$ext] ) ? $types[$ext] : null;
        } // extensionToMimetype }}}


        /**
         * Enter description here ...
         *
         * @param	string $type
         * @return	string
         */
        public static function mimetypeToExtension( $type )
        {
                static $exts;
                if (! isset( $exts )) {
                        $exts = self::mimetypesExtensions();
		}
                $extension = isset( $exts[$type] ) ? $exts[$type] : null;
                // prefer jpg over jpeg
                if ($extension === 'jpeg') {
                        $extension = 'jpg';
                }
                return $extension;
        } // mimetypeToExtension }}}


        /**
         * Enter description here ...
         *
         * @return	array
         */
        public static function mimetypesExtensions()
        {
                $mimeMap = self::_getMapFilename();
                // Returns the system MIME type mapping of MIME types to extensions, 
                // as defined in /etc/mime.types (considering the first extension listed to be canonical).
                $out = array();
                $file = fopen( $mimeMap, 'r' );
                while ( ($line = fgets( $file )) !== false ) {
                        $line = trim( preg_replace( '@#.*@', '', $line ) );
                        if (! $line) {
                                continue;
                        }
                        $parts = preg_split( '@\s+@', $line );
                        if (count( $parts ) == 1) {
                                continue;
                        }
                        $type = array_shift( $parts );
                        if (! isset( $out[$type] )) {
                                $out[$type] = array_shift( $parts );
                        }
                }
                fclose( $file );
                return $out;
        } // sysMimetypeExtensions }}}


        /**
         * Enter description here ...
         *
         * @return	array
         */
        public static function extensionsMimetypes()
        {
                $mimeMap = self::_getMapFilename();
                // Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
                $out = array();
                $file = fopen( $mimeMap, 'r' );
                while ( ($line = fgets( $file )) !== false ) {
                        $line = trim( preg_replace( '@#.*@', '', $line ) );
                        if (! $line) {
                                continue;
                        }
                        $parts = preg_split( '@\s+@', $line );
                        if (count( $parts ) == 1) {
                                continue;
                        }
                        $type = array_shift( $parts );
                        foreach ( $parts as $part ) {
                                $out[$part] = $type;
                        }
                }
                fclose( $file );
                return $out;
        } // sysExtensionMimetypes }}}


        /**
         * Enter description here ...
         *
         * @throws	Exception
         * @return	array
         */
        protected static function _getMapFilename()
        {
                static $checked = false;

                if (! $checked) {
                        if (! is_file( self::$mimetypesMap )) {
                                throw new Exception( "System mimetypes map not found '" . self::$mimetypesMap . "'" );
                        }
                        if (! is_readable( self::$mimetypesMap )) {
                                throw new Exception( "System mimetypes map not readable '" . self::$mimetypesMap . "'" );
                        }
                        $checked = true;
                }

                return self::$mimetypesMap;
        } // _getMimetypesMap }}}
}

?>
