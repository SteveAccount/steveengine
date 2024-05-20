<?php

namespace SteveEngine;

class TreeNode{
    public $id;
    public $content;
    public $nodes;
    public static $orphans = [];

    public function __construct( $id = 0, $content = null ){
        $this->id = $id;
        $this->content = $content;
        return $this;
    }

    public function fillTree( array $data, string $idField, string $parentField ) : TreeNode{
        foreach( $data as $row ){
            $subNode = new TreeNode( $row->$idField, $row );
            if ( $row->$parentField == "0" ){
                $this->nodes[] = $subNode;            
            }else{
                $node = $this->findNodeById( $row->$parentField );
                if ( $node ){
                    $node->nodes[] = $subNode;
                }
                else {
                    //Sometimes,there are orphan elems,whose parents not in the tree yet
                    self::$orphans[$row->$idField] = $row;
                }
            }
        }

        /* ADD Orphans! */
        foreach(self::$orphans as $orphan){
            $orphanNode = new TreeNode( $orphan->$idField, $orphan );
            $node = $this->findNodeById( $orphan->$parentField );
            if ( $node ){
                $node->nodes[] = $orphanNode;
            }
        }

        return $this;
    }

    public function findNodeById( $id ){
        foreach( $this->nodes as $node){
            if ( $node->id == $id ){
                return $node;
            }else{
                if ( isset( $node->nodes ) && count( $node->nodes ) > 0 ){
                    $result = $node->findNodeById( $id );
                    if ( $result ){
                        return $result;
                    }
                }
            }
        }
        return false;
    }
}