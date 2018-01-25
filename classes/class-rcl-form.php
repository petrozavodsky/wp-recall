<?php

class Rcl_Form extends Rcl_Custom_Fields{
    
    public $action = '';
    public $method = 'post';
    public $submit;
    public $onclick;
    public $fields = array();
    public $values = array();

    function __construct($args = false) {
        
        $this->init_properties($args);

    }
    
    function init_properties($args){
        
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $name=>$val){
            if(isset($args[$name])) $this->$name = $args[$name];
        }
        
    }

    function get_form($args = false){

        $content = '<div class="rcl-form preloader-parent">';
            
            $content .= '<form method="'.$this->method.'" action="'.$this->action.'">';

                foreach($this->fields as $field){

                    $value = (isset($this->values[$field['slug']]))? $this->values[$field['slug']]: false;

                    $required = (isset($field['required']) && $field['required'] == 1)? '<span class="required">*</span>': '';

                    $content .= '<div id="field-'.$field['slug'].'" class="form-field rcl-option">';

                        if(isset($field['title'])){
                            $content .= '<h3 class="field-title">';
                            $content .= $this->get_title($field).' '.$required;
                            $content .= '</h3>';
                        }

                        $content .= $this->get_input($field,$value);

                    $content .= '</div>';

                }

                $content .= '<div class="submit-box">';
                
                if($this->onclick){
                    $content .= '<a href="#" title="'.$this->submit.'" class="recall-button" onclick=\''.$this->onclick.'\'>';
                    $content .= '<i class="fa fa-check-circle" aria-hidden="true"></i> '.$this->submit;
                    $content .= '</a>';
                }else{
                    $content .= '<input type="submit" class="recall-button" value="'.$this->submit.'"/>';
                }

                $content .= '</div>';
                $content .= wp_nonce_field('rcl-form-nonce','_wpnonce',true,false);

            $content .= '</form>';
            
        $content .= '</div>';
        
        return $content;
        
    }
    
}

