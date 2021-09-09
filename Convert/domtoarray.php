<?php

namespace SteveEngine\Convert;

class DomToArray{
    public static function convert( $xml ) {
        return self::DOMtoArray( $xml );
    }
    
    private static function DOMtoArray( $root ) {
        $result = array();
    
        if ( $root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ( $attrs as $attr ) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }
    
        if ( $root->hasChildNodes()) {
            $children = $root->childNodes;
            if ( $children->length == 1 ) {
                $child = $children->item( 0 );
                if ( in_array( $child->nodeType,[XML_TEXT_NODE,XML_CDATA_SECTION_NODE])) {
                    $result['_value'] = $child->nodeValue;
                    return count( $result ) == 1
                        ? $result['_value']
                        : $result;
                }
    
            }
            $groups = array();
            foreach ( $children as $child ) {
                $parts = explode( ":", $child->nodeName );
                $nodeName = $parts[count($parts) - 1]; 
                if ( !isset($result[$nodeName])) {
                    $result[$nodeName] = self::DOMtoArray( $child );
                } else {
                    if ( !isset( $groups[$nodeName])) {
                        $result[$nodeName] = array( $result[$nodeName]);
                        $groups[$nodeName] = 1;
                    }
                    $result[$nodeName][] = self::DOMtoArray( $child );
                }
            }
        }
        return $result;
    }
}