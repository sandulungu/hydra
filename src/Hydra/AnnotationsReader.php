<?php
/**
 * This file is part of Hydra, the cozy RESTfull PHP5.3 micro-framework.
 *
 * @author      Sandu Lungu <sandu@lungu.info>
 * @package     hydra
 * @subpackage  core
 * @filesource
 * @license     http://www.opensource.org/licenses/MIT MIT
 */

namespace Hydra;

class AnnotationsReader extends Container {
    
    function __construct() {
        parent::__construct('annotation');
    }
    
    function forMethod($classname, $method, array $filter = array()) {
        $reflection = new \ReflectionMethod($classname, $method);
        return $this->_parseDocComment(
            $class->getDocComment(), 
            $filter,
            array('type' => 'method', 'method' => $reflection->getName(), 'class' => $classname)
        );
    }
    
    function forProperty($classname, $property, array $filter = array()) {
        $reflection = new \ReflectionProperty($classname, $property);
        return $this->_parseDocComment(
            $class->getDocComment(), 
            $filter,
            array('type' => 'property', 'property' => $reflection->getName(), 'class' => $classname)
        );
    }
    
    function forClass($classname, array $filter = array()) {
        $class = new \ReflectionClass($classname);
        $annotations = $this->_parseDocComment(
            $class->getDocComment(), 
            $filter,
            array('type' => 'class', 'class' => $class->getName())
        );
        
        foreach ($class->getMethods() as $method) {
            $annotations = array_merge($annotations, $this->_parseDocComment(
                $method->getDocComment(), 
                $filter,
                array('type' => 'method', 'method' => $method->getName(), 'class' => $class->getName())
            ));
        }
        
        foreach ($class->getProperties() as $property) {
            $annotations = array_merge($annotations, $this->_parseDocComment(
                $property->getDocComment(), 
                $filter,
                array('type' => 'method', 'method' => $property->getName(), 'class' => $class->getName())
            ));
        }
        
        return $annotations;
    }
    
    protected function _parseDocComment($doc_comment, $filter, array $defaults = array()) {
        $self = $this;
        return array_filter(array_map(function($line) use ($filter, $defaults, $self) {
            if ($line && preg_match('/^\s*\*\s*@[a-z]/i', $line)) {
                $line = preg_replace('/^\s*\*\s*@/i', '', $line);
                list ($name, $value) = explode(' ', $line, 2);
                if (!$filter || in_array($name, $filter)) {
                    $annotation = array(
                        'name' => $name,
                        'value' => trim($value)
                    ) + $defaults;
                    return $self->$name($annotation);
                }
            } 
        }, explode("\n", $doc_comment)));
    }
    
}