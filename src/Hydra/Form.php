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
 * @property array $children
 * @property bool $hasChildren
 * @property bool $valid
 * @property array|Traversable $choices
 * @property array $validators
 * @property array $behaviors
 * @property string $type
 * @property string $options
 * @property string $dataType
 * @property string $label
 * @property string $name
 * @property string $fullName
 * @property array $attributes
 * @property string|false $twigDefaultView
 */
class Form extends Container {
    
    var $errors = array(), $overridenOptions;
    
    /**
     * Default form (shared) options.
     */
    var $defaultOptions = array(
        'type' => null, // service
        'behaviors' => array(), // service
        'name' => null, // service
        'children' => null, // service
        'multiple' => false,

        'data' => null, // service
        'dataClass' => null,
        'dataType' => null, // service

        // Validation options, can be safely changed in 'form.options' hook
        'required' => false,
        'errorBubble' => false,
        'validators' => null, // service
        'messages' => array(),

        // View options, can be safely changed in 'form.options' hook
        'label' => null, // service
        'attributes' => array(), // service
        'twigView' => null,
        'twigBlocks' => array(),
    );
    
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
        $this->overridenOptions = $options;
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
        if ($this->options['twigView'] && !isset($subform->overridenOptions['twigView'])) {
            $subform->overridenOptions['twigView'] = $this->options['twigView'];
        }
        $subform->name = $name;
        $subform->parent = $this;

        if (!isset($subform->overridenOptions['data']) && empty($subform->overridenOptions['dataClass'])) {
            if (is_array($this->data) && isset($this->data[$name])) {
                $subform->overridenOptions['data'] =& $this->data[$name];
            }
            elseif (is_object($this->data) && isset($this->data->$name)) {
                $subform->overridenOptions['data'] =& $this->data->$name;
            }
        }
        
        // Load form options. This is required for $form->type guessers to work properly.
        $subform->options;
    }
    
    function render($block_name = 'widget', array $context = array()) {
        if (!$this->twigDefaultView) {
            return;
        }
        
        if (isset($this->options['twigBlocks'][$block_name])) {
            $block_name = $this->options['twigBlocks'][$block_name];
        }
        if (!$block_name) {
            return;
        }
        
        if ($this->options['twigView']) {
            $template = $this->app->twig->loadTemplate($this->options['twigView']);
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
    
    function validate(&$data = null) {
        unset($this->valid);
        $this->errors = array();
        $this->app->hook('form.validate', $this, $data);
        return $this->valid;
    }
    
    function addError($message = 'invalid', array $params = array(), Validator $validator = null) {
        $this->_hasErrors = true;
                
        if (isset($this->options['messages'][$message])) {
            $message = $this->options['messages'][$message];
        }
        if (isset($validator->options['messages'][$message])) {
            $message = $this->options['messages'][$message];
        }
        
        $form = $this;
        while ($form->options['errorBubble'] && $form->parent) {
            $form = $form->parent;
        }
        $form->errors[] = $this->app->translate($message, $params);
    }
    
    function bindRequest(Request $request) {
        if (empty($this->options['method'])) {
            throw new \LogicException("A 'method' option should be set before binding a Form to a Request.");
        }
        $data = strtoupper($this->options['method']) == 'GET' ? $request->query : $request->data;
        if (!isset($data[$this->name])) {
            return false;
        }
        return $this->bind($data[$this->name]);
    }
    
    function bind($data, $validate = true) {
        $validate && $this->validate($data);
        $this->data = $this->app->hook('form.bind', $this, $data);
        return $this->valid;
    }
    
    function &service__options() {
        $this->options = $this->overridenOptions + $this->defaultOptions;
        return $this->app->hook('form.options', $this, $this->options);
    }
    
    function service__type() {
        if ($this->options['type']) {
            return $this->options['type'];
        }
        return $this->app->hook('form.type', $this);
    }

    function service__behaviors() {
        $behaviors = array(-1 => $this->type);
        if ($this->options['behaviors']) {
            $behaviors += $this->options['behaviors'];
        }
        return $behaviors;
    }

    function service__dataType() {
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
    
    function service__label() {
        if (isset($this->options['label'])) {
            return $this->options['label'];
        }
        return Utils::humanize($this->name);
    }

    function service__twigDefaultView() {
        return isset($this->app->config->form__twigViews[$this->type]) ?
            $this->app->config->form__twigViews[$this->type] :
            $this->app->config->form__twigViews['default'];
    }

    function service__children() {
        $children = $this->options['children'];
        $this->app->hook('form.children', $this, $children);
        if (!is_array($children)) {
            $children = array();
        }
        
        foreach ($children as $name => &$subform) {
            $this->_prepareChild($name, $subform);
        }
        return $children;
    }

    function service__hasChildren() {
        return (bool)$this->children;
    }
    
    function service__choices() {
        $choices =& $this->options['choices'];
        if ($choices instanceof \Closure) {
            $choices = $choices($this);
        }
        if ($choices && !is_array($choices) && !$choices instanceof \Traversable) {
            throw new \LogicException("Form choices should be an array or Traversable class.");
        }
        return $choices;
    }
    
    function &service__data() {
        $data = array();
        if (isset($this->options['data'])) {
            if ($this->options['data'] instanceof \Closure) {
                $callback = $this->options['data'];
                return $callback($this);
            }
            return $this->options['data'];
        }
        if ($this->options['dataClass']) {
            $dataClass = $this->options['dataClass'];
            return new $dataClass;
        }
        return $data;
    }
    
    function service__name() {
        $name = $this->options['name'];
        if (!$name) {
            $name = strtolower(str_replace('\\', '_', get_class($this)));
        }
        return $name;
    }
    
    function service__fullName() {
        return $this->parent ? "{$this->parent->fullName}[$this->name]" : $this->name;
    }
    
    function service__attributes() {
        $attributes = $this->options['attributes'] + array(
            'name' => $this->fullName . ($this->options['multiple'] ? '[]' : ''),
            'id' => $this->parent ? 
                "{$this->parent->attributes['id']}-$this->name" : 
                $this->name,
        );
        return array_filter($attributes);
    }
    
    function service__validators() {
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
    
    function service__valid() {
        foreach ($this->children as $subform) {
            if (!$subform->valid) {
                return false;
            }
        }
        return !$this->_hasErrors;
    }
    
}
