<?php

namespace SteveEngine;

trait Comparable{
    public function getVars() : array{
        return get_object_vars( $this );
    }
}