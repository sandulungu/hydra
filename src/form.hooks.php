<?php
/**
 * This file is part of Hydra, the cozy RESTfull PHP5.3 micro-framework.
 *
 * @link        https://github.com/z7/hydra
 * @author      Sandu Lungu <sandu@lungu.info>
 * @package     hydra
 * @subpackage  core
 * @filesource
 * @license     http://www.opensource.org/licenses/MIT MIT
 */

namespace Hydra;

use Hydra\Utils;

// Default app setings.
$hooks['app.config'][-1000][] = function (&$config) {
    $config['form.twigViews'] = array(
        'default' => 'form.html.twig',
    );
};

// Form API entry point.
$methods['app.form'][0] = function(App $app, array $options, Form $form = null) {
    $app->hook('form.init', $options, $form);
    
    // Load form options. This is required for $form->type guessers to work properly.
    $form->options;
    
    return $form;
};

// Usage of custom form classes.
// When creating your Form types, most probably, you'll want to use something similar.
$hooks['form.init'][0][] = function(array &$options, &$form, App $app) {
    if (isset($options['type'])) {
        $type = $options['type'];
        if ($type == 'list' || $type == 'select') {
            $form = new Form\SelectField($app, $options);
        }
    }
};

// Create a default form.
$hooks['form.init'][1000][] = function(array &$options, &$form, App $app) {
    if (!$form) {
        $form = new Form($app, $options);
    }
};

// Set template block names and some other low-level defaults, based on $form->behaviors.
$hooks['form.options'][0][] = function(Form $form, array &$options) {
    $twig_blocks =& $options['twigBlocks'];
    foreach ($form->behaviors as $behavior) {
        switch ($behavior) {
            
            // Core forms
            case 'form':
                $twig_blocks += array('widget' => "form_widget");
                $options += array('method' => 'POST');
                break;
            case 'subform':
            case 'collection':
                $twig_blocks += array('widget' => "{$form->type}_widget");
                break;
            
            // Core fields
            case 'textarea':
                $form->children = false;
                $twig_blocks += array('widget' => "textarea_widget");
                break;
            case 'hidden':
                $form->children = false;
                $options['errorBubble'] = true;
                $twig_blocks += array('row' => 'widget', 'label' => false);
                break;
            case 'checkbox':
                $form->children = false;
                $twig_blocks += array('widget' => 'checkbox_widget', 'label' => false);
                break;
            case 'file':
                if ($form->parentForm) {
                    $form->parentForm->options['attributes']['enctype'] = "multipart/form-data";
                }
                $form->children = false;
                break;
            case 'text':
            case 'password':
            case 'image':
            case 'submit':
                
            // HTML5 fields
            // Description & browser support info: http://www.w3schools.com/html5/html5_form_input_types.asp
            // TODO: Add default validators and data types
            case 'color':
            case 'date':
            case 'datetime':
            case 'datetime-local':
            case 'email':
            case 'month':
            case 'number':
            case 'range':
            case 'search':
            case 'tel':
            case 'time':
            case 'url':
            case 'week':
                $form->children = false;
                break;
            
            // SelectField
            case 'list':
                $twig_blocks += array('attributes' => 'list_attributes');
            case 'select':
                if (!isset($options['choices'])) {
                    throw new \LogicException("For 'list' or 'select' form fields 'choices' option is required.");
                }
                if (!isset($form->overridenOptions['multiple'])) {
                    $options['multiple'] = is_array($form->data);
                }
                $twig_blocks += array('widget' => "{$form->type}_widget");
                break;
        }
    }
};

// Default form type guesser.
$hooks['form.type'][1000][] = function(Form $form) {
    if ($form->hasChildren) {
        if (!$form->parent) {
            return 'form';
        }
        return is_array($form->data) && Utils::arrayIsNumeric($form->data) ? 'collection' : 'subform';
    }
    if (is_bool($form->data)) {
        return 'checkbox';
    }
    if (is_int($form->data)) {
        return 'number';
    }
    if (is_string($form->data) && strpos($form->data, "\n") !== false) {
        return 'textarea';
    }
    return 'text';
};

// Default children guesser (supports plain objects and arrays).
$hooks['form.children'][1000][] = function(Form $form, &$children) {
    if ($children === null && (is_object($form->data) || is_array($form->data))) {
        $children = array();
        $columns = array_keys(is_array($form->data) ? $form->data : get_object_vars($form->data));
        foreach ($columns as $name) {
            $children[$name] = array();
        }
    }
};

// Default array to object/array binding.
$hooks['form.bind'][1000][] = function(Form $form, &$data) {
    if (!$form->hasChildren || !isset($data)) {
        return;
    }
    
    if (is_object($form->data) || is_array($form->data)) {
        if (is_array($data)) {
            $array_access = is_array($form->data);
            foreach ($form->children as $name => $subform) {
                if (array_key_exists($name, $data)) {
                    $subform->bind($data[$name], false);
                    if ($array_access) {
                        $form->data[$name] =& $subform->data;
                    } else {
                        $form->data->$name =& $subform->data;
                    }
                }
            }
            $data = $form->data;
        } else {
            throw new \InvalidArgumentException("When binding to an array/object an array input should be provided.");
        }
    }
};

// Normalize values and validate required fields.
$hooks['form.validate'][-1000][] = function(Form $form, &$data) {
    if ($form->dataType) {
        $method = "normalize__$form->dataType";
        $data = $form->app->$method($data);
    }
    if ($form->options['required']) {
        if (empty($data)) {
            $form->addError('required');
            return false;
        }
    }
};

// Validate subforms.
$hooks['form.validate'][-500][] = function(Form $form, &$data) {
    if (!$form->hasChildren) {
        return;
    }
    
    if (is_array($data)) {
        foreach ($form->children as $name => $subform) {
            if (array_key_exists($name, $data)) {
                $subform->validate($data[$name]);
            } else {
                $subform->validate();
            }
        }
    }
    elseif (isset($data)) {
        $form->addError();
    }

};

$hooks['form.validate'][0][] = function(Form $form, &$data) {

    // Validate user choice(s).
    if ($data && $form instanceof Form\SelectField) {
        $values = $form->options['multiple'] ? $data : array($data);
        foreach ($form->choices as $k => $v) {
            if ($v instanceof \Traversable) {
                $v = iterator_to_array($v);
            }
            $values = array_diff($values, is_array($v) ? array_keys($v) : array($k));
            if (!$values) {
                break;
            }
        }
        if ($values) {
            $form->addError('invalid_choices', array('$choices' => implode(', ', $values)));
            return false;
        }
    }
    
    // Apply defined validators (main validation).
    foreach ($form->validators as $validator) {
        if (!$validator->validate($data)) {
            return false;
        }
    }
};
