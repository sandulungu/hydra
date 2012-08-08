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

/**
 * Base class for all forms and fields.
 * 
 * ATTENTION: Because of the complex guessing mechanics, during 'form.options' 
 * hook execution some services described in the 'options' property might
 * get inconsistent values. Usually, this won't be the case, but if not sure,
 * better unset the service before returning from the hook callback.
 * 
 * Make sure you test well any new Form workflow, especially when relying on guessers.
 * 
 * @property mixed $data
 * @property mixed $webData
 * @property Form $parentForm
 * @property array $children
 * @property bool $hasChildren
 * @property bool $valid
 * @property array $validators
 * @property array $behaviors
 * @property string $type
 * @property array $options
 * @property string $dataType
 * @property string $label
 * @property string $name
 * @property string $fullName
 * @property array $attributes
 * @property string|false $twigDefaultView
 */
abstract class Form extends Container {
    
    /**
     * Default service values.
     */
    var $defaultOptions = array(
        'type' => null,
        'behaviors' => array(),
        'name' => null,
        'children' => null,
        'data' => null,
        'dataType' => null,
        'validators' => null,
        'label' => null,
        'attributes' => array(),
    );
    
    var $constructorOptions,
        $errors = array(),
        $multiple = false,
        $dataClass,
        $required = false,
        $errorBubble = false, 
        $messages = array(), 
        $twigView = false, 
        $twigBlocks = array(),
        $helpMessage = '';
    
    /**
     * @var App
     */
    var $app;
    
    /**
     * @var Form
     */
    var $parent;
    
    protected $_hasErrors = false;

    function __construct(App $app, array $options = array()) {
        $this->app = $app;
        $this->constructorOptions = $options;
        parent::__construct('form');
    }

    /**
     * Alternative way of defining child items.
     * 
     * Useful when building complex forms with well-known structure.
     * 
     * Note: By using this method children guessers will be completely disabled
     * for current form item.
     */
    function addChild($name, $subform = array()) {
        
        // Disable children guessers.
        if (!isset($this->children)) {
            $this->children = array();
        }
        
        $this->_prepareChild($name, $subform);
        $this->children[$name] = $subform;
    }
    
    protected function _prepareChild($name, &$subform) {
        if (is_string($subform)) {
            $subform = array('type' => $subform);
        }

        if (is_array($subform)) {
            $subform = $this->app->hook('form.init', $subform);
        }
        if ($this->twigView && !isset($subform->constructorOptions['twigView'])) {
            $subform->constructorOptions['twigView'] = $this->twigView;
        }
        $subform->name = $name;
        $subform->parent = $this;

        if (!isset($subform->constructorOptions['data']) && empty($subform->constructorOptions['dataClass'])) {
            if (is_array($this->data) && isset($this->data[$name])) {
                $subform->constructorOptions['data'] =& $this->data[$name];
            }
            elseif (is_object($this->data) && isset($this->data->$name)) {
                $subform->constructorOptions['data'] =& $this->data->$name;
            }
        }
        
        // Load form options. This is required for $form->type guessers to work properly.
        $subform->options;
    }
    
    function render($block_name = 'widget', array $context = array()) {
        if (!$this->twigDefaultView) {
            return;
        }
        
        if (isset($this->twigBlocks[$block_name])) {
            $block_name = $this->twigBlocks[$block_name];
        }
        if (!$block_name) {
            return;
        }
        
        if ($this->twigView) {
            $template = $this->app->twig->loadTemplate($this->twigView);
            if (!$template->hasBlock($block_name)) {
                $template = null;
            }
        }
        if (empty($template)) {
            $template = $this->app->twig->loadTemplate($this->twigDefaultView);
        }
        
        $context += array('form' => $this);
        return $template->renderBlock($block_name, $context);
    }
    
    /**
     * Validates and optionally standartize a unsafe web value.
     * 
     * This will be usually called internally just before binding the actual value.
     */
    function validate(&$data = null) {
        unset($this->valid);
        $this->errors = array();
        $this->app->hook('form.validate', $this, $data);
        return $this->valid;
    }
    
    function addError($message = 'invalid', array $params = array(), Validator $validator = null) {
        $this->_hasErrors = true;
                
        if (isset($this->messages[$message])) {
            $message = $this->messages[$message];
        }
        elseif (isset($validator->messages[$message])) {
            $message = $validator->messages[$message];
        }
        else {
            $message = Utils::humanize($message);
        }
        
        $form = $this;
        while ($form->errorBubble && $form->parent) {
            $form = $form->parent;
        }
        $form->errors[] = $this->app->translate($message, $params);
    }
    
    /**
     * Binds a new value to the form/field.
     * 
     * @param mixed $data
     * @param bool $validate For internal use. Setting this to FALSE will only skin pre-bind validation.
     * @return type 
     */
    function bind($data, $validate = true) {
        $validate && $this->validate($data);
        $this->webData = $this->app->hook('form.bind', $this, $data);
        return $this->valid;
    }
    
    function &service__options() {
        
        // Override class properties.
        $this->options = array();
        foreach($this->constructorOptions as $name => &$value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            } else {
                $this->options[$name] = $value;
            }
        }
        
        // Process and validate options.
        $this->options += $this->defaultOptions;
        $this->app->hook('form.options', $this);
        foreach($this->options as $name => &$value) {
            if (!array_key_exists($name, $this->defaultOptions)) {
                if (property_exists($this, $name)) {
                    throw new \LogicException("Inside 'form.options' hook, Form object properties should be overridden directly and not through options array. Conflicting option: $name.");
                }
                throw new \LogicException("Unsupported Form option: $name.");
            }
            
        }
        return $this->options;
    }
        
    protected function service__type() {
        if (!empty($this->options['type'])) {
            return $this->options['type'];
        }
        
        if ($this->hasChildren) {
            return is_array($this->data) && Utils::arrayIsNumeric($this->data) ? 'collection' : 'subform';
        }
        if (is_bool($this->data)) {
            return 'checkbox';
        }
        if (is_int($this->data)) {
            return 'number';
        }
        if (is_string($this->data) && strpos($this->data, "\n") !== false) {
            return 'textarea';
        }
        return 'text';
    }

    protected function service__parentForm() {
        $form = $this->parent;
        if (!$form) {
            return;
        }
        while (!in_array('form', $form->behaviors)) {
            if (!$form->parent) {
                return;
            }
            $form = $form->parent;
        }
        return $form;
    }
    
    protected function service__behaviors() {
        $behaviors = array(-1 => $this->type);
        if ($this->options['behaviors']) {
            $behaviors += $this->options['behaviors'];
        }
        return $behaviors;
    }

    protected function service__dataType() {
        if (isset($this->options['dataType'])) {
            return $this->options['dataType'];
        }

        if (is_array($this->data)) {
            return 'array';
        }
        if (is_string($this->data)) {
            return 'string';
        }
        if (is_bool($this->data)) {
            return 'bool';
        }
        if (is_int($this->data)) {
            return 'int';
        }
        if (is_float($this->data)) {
            return 'float';
        }
    }
    
    protected function service__label() {
        if (isset($this->options['label'])) {
            return $this->options['label'];
        }
        return Utils::humanize($this->name);
    }

    protected function service__twigDefaultView() {
        return isset($this->app->config->form__twigViews[$this->type]) ?
            $this->app->config->form__twigViews[$this->type] :
            $this->app->config->form__twigViews['default'];
    }

    protected function service__children() {
        $children = $this->options['children'];
        $this->app->hook('form.children', $this, $children);
        
        // set a consistent value for the cases when children are not supported
        if (!is_array($children)) {
            $children = false;
        }
        
        if ($children) {
            foreach ($children as $name => &$subform) {
                $this->_prepareChild($name, $subform);
            }
        }
        return $children;
    }

    protected function service__hasChildren() {
        // not sure what to return in case of empty array (may have children, but currently doesn't)
        return (bool)$this->children;
    }
    
    function &service__webData() {
        return $this->app->hook('form.transform.toWeb', $this, $this->data);
    }
    
    function &service__data() {
        if (isset($this->webData)) {
            return $this->app->hook('form.transform.fromWeb', $this, $this->webData);
        }
        
        $data = array();
        if (isset($this->options['data'])) {
            if ($this->options['data'] instanceof \Closure) {
                $callback = $this->options['data'];
                return $callback($this);
            }
            return $this->options['data'];
        }
        if ($this->dataClass) {
            $dataClass = $this->dataClass;
            return new $dataClass;
        }
        return $data;
    }
    
    protected function service__name() {
        $name = $this->options['name'];
        if (!$name) {
            $name = strtolower(str_replace('\\', '_', get_class($this)));
        }
        return $name;
    }
    
    protected function service__fullName() {
        return $this->parent ? "{$this->parent->fullName}[$this->name]" : $this->name;
    }
    
    protected function service__attributes() {
        $attributes = $this->options['attributes'] + array(
            'name' => $this->fullName . ($this->multiple ? '[]' : ''),
            'id' => $this->parent ?
                "{$this->parent->attributes['id']}-$this->name" : 
                $this->name,
        );
        return array_filter($attributes);
    }
    
    protected function service__validators() {
        $validators = $this->options['validators'];
        $this->app->hook('form.validators', $this, $validators);
        if (!is_array($validators)) {
            $validators = array();
        }
        
        foreach ($validators as &$validator) {
            if (is_string($validator)) {
                $validator = array('type' => $validator);
            }
            if (is_array($validator)) {
                if (empty($validator['type'])) {
                    $json = json_encode($validator);
                    throw new \LogicException("A validator array definition must contain a 'type' element, but $json given.");
                }
                $method = "validator__{$validator['type']}";
                $validator = $this->$method($validator);
            }
            
            if ($validator instanceof \Closure) {
                $validator = new Validator($this, $validator);
            }
            elseif (!$validator instanceof Validator) {
                $json = json_encode($validator);
                throw new \LogicException("A validator should be either a Closure or Hydra\Validator class.");
            }
        }
        return $validators;
    }
    
    protected function service__valid() {
        if ($this->hasChildren) {
            foreach ($this->children as $subform) {
                if (!$subform->valid) {
                    return false;
                }
            }
        }
        return !$this->_hasErrors;
    }
    
}
