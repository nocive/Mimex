<?php

/**
 * Enter description here ...
 *
 * @package	Mimex
 * @author	Jose' Pedro Saraiva <nocive at gmail.com>
 */
class Mimex
{
        /**
         * Enter description here ...
         *
         * @var string
         * @access public
         */
        public static $mimetypesMap = 'mime.types';


        /**
         * Enter description here ...
         *
         * @param string $file
         * @return string
         */
        public static function extension( $file, $realDetect = true )
	{
		if ($realDetect) {
			// TODO
			throw new Exception( 'not yet implemented' );
		}
                return strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
        } // extension }}}


        /**
         * Enter description here ...
         *
         * @param string $file
         * @return string
         */
        public static function mimetype( $file, $realDetect = true )
	{
		if ($realDetect) {
			// TODO
			throw new Exception( 'not yet implemented' );
		}
                return self::extensionToMimetype( self::extension( $file ) );
        } // mimetype }}}


        /**
         * Enter description here ...
         *
         * @param string $file
         * @return string
         */
        public static function extensionToMimetype( $ext )
        {
                # Returns the system MIME type (as defined in /etc/mime.types) for the filename specified.
                #
                # $file - the filename to examine
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
         * @param string $type
         * @return string
         */
        public static function mimetypeToExtension( $type )
        {
                # Returns the canonical file extension for the MIME type specified, as defined in /etc/mime.types (considering the first
                # extension listed to be canonical).
                #
                # $type - the MIME type
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
         * @return array
         */
        public static function mimetypesExtensions()
        {
                $mimeMap = self::_getMapFilename();
                # Returns the system MIME type mapping of MIME types to extensions, as defined in /etc/mime.types (considering the first
                # extension listed to be canonical).
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
         * @return array
         */
        public static function extensionsMimetypes()
        {
                $mimeMap = self::_getMapFilename();
                # Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
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
         * @return array
         * @throws Exception
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
