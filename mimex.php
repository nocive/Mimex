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
if (! defined( 'MIMEX_MAP' )) {
	define( 'MIMEX_MAP', realpath( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'mime.types' );
}

class Mimex
{
        /**
         * Enter description here ...
         *
         * @var		string
         * @access	public
         */
	public static $mimetypesMap = MIMEX_MAP;


        /**
         * Enter description here ...
         *
         * @param	string $file
         * @param	bool $realDetect
         * @return	string
         */
        public static function extension( $file, $realDetect = true )
	{
		return $realDetect ? self::mimetypeToExtension( self::detectMimetype( $file ) ) : strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
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
		return $realDetect ? self::detectMimetype( $file ) : self::extensionToMimetype( self::extension( $file ) );
	} // mimetype }}}


	/**
	 * Attempts to detect file mimetype using Fileinfo extension
	 *
	 * @param	string $file
	 * @return	string
	 */
	public static function detectMimetype( $file )
	{
		static $finfo;

		if (! $finfo) {
			if (! extension_loaded( 'fileinfo' )) {
				throw new Exception( 'Can\'t detect mimetype, Fileinfo extension not loaded' );
			}
			$finfo = new finfo( FILEINFO_MIME_TYPE );
		}
		$mimetype = $finfo->file( $file );
		// fix erroneous mimetype for favicons returned by some versions of fileinfo
		$mimetype = str_replace( 'image/x-ico', 'image/x-icon', $mimetype );
		return $mimetype;
	} // detectMimetype }}}


        /**
         * Enter description here ...
         *
         * @param	string $ext
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
                $extension = str_replace( 'jpeg', 'jpg', $extension );
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
                $file = fopen( $mimeMap, 'r' );
                // Returns the system MIME type mapping of MIME types to extensions, 
                // as defined in /etc/mime.types (considering the first extension listed to be canonical).
                $out = array();
		while (($line = fgets( $file )) !== false) {
			$line = trim( preg_replace( array( '@^\s*#.*$@', '@^\s*$@' ), '', $line ) );
			if (! $line) {
				continue;
			}
			$parts = preg_split( '@\s+@', $line );
			if (count( $parts ) <= 1) {
				continue;
			}
			$type = array_shift( $parts );
			if (! isset( $out[$type] )) {
				$out[$type] = array_shift( $parts );
			}
		}
		fclose( $file );
		return $out;
	} // mimetypesExtensions }}}


        /**
         * Enter description here ...
         *
         * @return	array
         */
        public static function extensionsMimetypes()
        {
                $mimeMap = self::_getMapFilename();
                $file = fopen( $mimeMap, 'r' );
                // Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
                $out = array();
		while (($line = fgets( $file )) !== false) {
			$line = trim( preg_replace( array( '@^\s*#.*$@', '@^\s*$@' ), '', $line ) );
			if (! $line) {
				continue;
			}
			$parts = preg_split( '@\s+@', $line );
			if (count( $parts ) <= 1) {
				continue;
			}
			$type = array_shift( $parts );
			foreach( $parts as $p ) {
				$out[$p] = $type;
			}
		}
		fclose( $file );
		return $out;
	} // extensionsMimetypes }}}


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
        } // _getMapFilename }}}
}

?>
