<?php

namespace SteveEngine;

class File{
    public static function loadJson ( string $path ) : string{
        $result = "";
        if ( file_exists( $path )){
            $result = file_get_contents( $path );
        }
        return $result;
    }

    public static function getFolders( string $path ) : array{
        return array_filter( glob( "$path/*" ), "is_dir");
    }

    public static function getFilesFromFolder( string $path, string $extension = "", bool $isWebPath = true ) : array{
        $result = [];
        if ( $extension == "" ){
            $result = glob( "*.*" );
        }else{
            $result = glob( "$path*.$extension" );
        }

        if ( $isWebPath ){
            //$pos = strlen( $GLOBALS["appPath"]);
            $pos = strlen( "C:/xampp/htdocs");
            
            foreach ( $result as &$path ){
                $path = substr( $path, $pos );
            }
        }
        return $result;
    }
}