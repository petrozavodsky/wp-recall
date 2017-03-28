<?php

class Rcl_Custom_Fields_Manager extends Rcl_Custom_Fields{

    public $name_option;
    public $post_type;
    public $options;
    public $options_html;
    public $field;
    public $types;
    public $status;
    public $primary;
    public $select_type;
    public $meta_key;
    public $exist_placeholder;
    public $sortable;
    public $fields;
    public $name_field;
    public $new_slug;
    
    public $defaultOptions = array();

    function __construct($post_type, $options = false){

        $this->select_type = (isset($options['select-type']))? $options['select-type']: true;
        $this->meta_key = (isset($options['meta-key']))? $options['meta-key']: true;
        $this->exist_placeholder = (isset($options['placeholder']))? $options['placeholder']: true;
        $this->sortable = (isset($options['sortable']))? $options['sortable']: true;
        $this->types = (isset($options['types']))? $options['types']: array();
        $this->primary = $options;
        $this->post_type = $post_type;

        switch($this->post_type){
            case 'post': $name_option = 'rcl_fields_post_'.$this->primary['id']; break;
            case 'orderform': $name_option = 'rcl_cart_fields'; break;
            case 'profile': $name_option = 'rcl_profile_fields'; break;
            default: $name_option = 'rcl_fields_'.$this->post_type;
        }
        
        $this->name_option = $name_option;

        $fields = stripslashes_deep(get_option( $name_option ));
        
        if($fields){

            foreach($fields as $k => $field){
                
                if(isset($field['field_select'])){
                    
                    $field['field_select'] = rcl_edit_old_option_fields($field['field_select'], $field['type']);
                    
                    if(is_array($field['field_select'])){
                        
                        $fields[$k]['values'] = $field['field_select'];
                        
                    }
                    
                }
                
            }
            
        }
        
        $this->fields = apply_filters('rcl_custom_fields', $fields, $this->post_type);

    }

    function manager_form($defaultOptions = false){

        $this->defaultOptions = $defaultOptions;

        $form = '<div id="rcl-custom-fields-editor" data-type="'.$this->post_type.'" class="rcl-custom-fields-box">
            
            <h3>'.__('Активные поля','wp-recall').'</h3>
            
            <form action="" method="post">
            '.wp_nonce_field('rcl-update-custom-fields','_wpnonce',true,false).'
            <input type="hidden" name="rcl-fields-options[name-option]" value="'.$this->name_option.'">
            <input type="hidden" name="rcl-fields-options[placeholder]" value="'.$this->exist_placeholder.'">';
        
        $form .= apply_filters('rcl_custom_fields_form','',$this->name_option);

        $form .= '<ul id="rcl-fields-list" class="rcl-sortable-fields">';

        $form .= $this->loop($this->get_active_fields());

        $form .= $this->empty_field();

        $form .= '</ul>';

        $form .= "<div class=fields-submit>
                <input type=button onclick='rcl_get_new_custom_field();' class='add-field-button button-secondary right' value='+ ".__('Add field','wp-recall')."'>
                <input class='button button-primary' type=submit value='".__('Save','wp-recall')."' name='rcl_save_custom_fields'>
                <input type=hidden id=rcl-deleted-fields name=rcl_deleted_custom_fields value=''>
            </div>
        </form>";
                
        if($this->sortable){
            $form .= '<script>
                jQuery(function(){
                    jQuery(".rcl-sortable-fields").sortable({
                        connectWith: ".rcl-sortable-fields",
                        handle: ".field-header",
                        cursor: "move",
                        placeholder: "ui-sortable-placeholder",
                        distance: 15,
                        receive: function(ev, ui) {
                            if(!ui.item.hasClass("must-receive"))
                              ui.sender.sortable("cancel");
                        }
                    });
                    return false;
                });
            </script>';
        }
        
        $form .= "<script>rcl_init_custom_fields(\"".$this->post_type."\",\"".wp_slash(json_encode($this->primary))."\",\"".wp_slash(json_encode($this->defaultOptions))."\");</script>";

        $form .= '</div>';

        return $form;
    }

    function loop($fields = null){
        
        $form = '';
        
        if(!isset($fields))
            $fields = $this->fields;
        
        if($fields){
            
            foreach($fields as $key => $args){
                if($key==='options') continue;
                $form .= $this->field($args);
            }
            
        }

        return $form;
    }
    
    function get_options_field(){
        
        $types = array(
            'select',
            'multiselect',
            'checkbox',
            'agree',
            'radio',
            'file'
        );
        
        $options = (isset($this->field['options-field']))? $this->field['options-field']: array();

        if(in_array($this->field['type'],$types)){
            
            if($this->field['type']=='file'){
                
                $options[] = array(
                    'type' => 'number',
                    'slug' => 'sizefile',
                    'title' => __('Размер файла','wp-recall'),
                    'notice' => __('maximum size of uploaded file, MB (Default - 2)','wp-recall')
                );
                
                $options[] = array(
                    'type' => 'textarea',
                    'slug' => 'field_select',
                    'title' => __('Разрешенные типы файлов','wp-recall'),
                    'notice' => __('allowed types of files are divided by comma, for example: pdf, zip, jpg','wp-recall')
                );
                
            }else if($this->field['type']=='agree'){
                
                $options[] = array(
                    'type' => 'url',
                    'slug' => 'url-agreement',
                    'title' => __('Agreement URL','wp-recall')
                );
                
                $options[] = array(
                    'type' => 'textarea',
                    'slug' => 'text-confirm',
                    'title' => __('Текст подтверждения согласия','wp-recall')
                );
                
            }else{
                
                $options[] = array(
                    'type' => 'dynamic',
                    'slug' => 'values',
                    'title' => __('Указание опций','wp-recall'),
                    'notice' => __('указывайте каждую опцию в отдельном поле','wp-recall')
                );
                
            }
            
        }else{
            
            if($this->exist_placeholder && $this->field['type'] != 'custom'){
                
                $options[] = array(
                    'type' => 'text',
                    'slug' => 'placeholder',
                    'title' => __('Placeholder','wp-recall')
                );
                
            }
            
            if($this->field['type'] == 'text' || $this->field['type'] == 'textarea'){
                
                $options[] = array(
                    'type' => 'number',
                    'slug' => 'maxlength',
                    'title' => __('Maxlength','wp-recall'),
                    'notice' => __('максимальное количество символов в поле','wp-recall')
                );
                
            }
            
        }
        
        $options = array_merge($options, $this->defaultOptions);
        
        return $options;
        
    }
    
    function get_input_option($option, $value = false){
        
        $value = (isset($this->field[$option['slug']]))? $this->field[$option['slug']]: $value;
        
        if($this->field['slug']){
            
            $option['name'] = 'field['.$this->field['slug'].']['.$option['slug'].']';
            
        }else{
            
            $option['name'] = 'new-field['.$this->new_slug.']['.$option['slug'].']';
        }
        
        return $this->get_input($option, $value);
        
    }
    
    function get_options(){
        
        $options = apply_filters('rcl_custom_field_options', $this->get_options_field(), $this->field, $this->post_type);
        
        if(!$options) return false;
        
        $content = '';
        
        foreach($options as $option){
            
            $content .= $this->get_option($option);
            
        }
        
        return $content;
        
    }
    
    function get_option($option, $value = false){
        
        if($option['type'] == 'hidden')
            return $this->get_input_option($option);

        $content = '<div class="option-content">';
            $content .= '<label>'.$this->get_title($option).'</label>';
            $content .= '<div class="option-input">';
                $content .= $this->get_input_option($option, $value);
            $content .= '</div>';
        $content .= '</div>';
        
        return $content;
    }
    
    function header_field(){
        
        $delete = (isset($this->field['delete']))? $this->field['delete']: true;
        
        $content = '<div class="field-header">
                    <span class="field-type type-'.$this->field['type'].'"></span>
                    <span class="field-title">'.$this->field['title'].'</span>                           
                    <span class="field-controls">
                    ';
        
        if($delete)
            $content .= '<a class="field-delete field-control" title="'.__('Delete','wp-recall').'" href="#"></a>';
                                
        $content .= '<a class="field-edit field-control" href="#" title="'.__('Edit','wp-recall').'"></a>
                    </span>
                </div>';
        
        return $content;
    }

    function field($args){
        
        $this->field = $args;
        
        $this->status = true;
        
        $classes = array('rcl-custom-field');
           
        if(isset($this->field['class']))
            $classes[] = $this->field['class'];

        $field = '<li id="field-'.$this->field['slug'].'" data-slug="'.$this->field['slug'].'" data-type="'.$this->field['type'].'" class="'.implode(' ',$classes).'">
                    '.$this->header_field().'
                    <div class="field-settings">';
        
                        $field .= $this->get_field_value(array(
                                'type' => 'text',
                                'slug' => 'slug',
                                'title' => __('Meta-key','wp-recall'),
                            ),
                            $this->field['slug']  
                        );

                        $field .= $this->get_option(array(
                                'type' => 'text',
                                'slug' => 'title',
                                'title' => __('Title','wp-recall'),
                                'required' => 1,
                            ),
                            $this->field['title']  
                        );
                        
                        if($this->select_type){
                        
                            $typeEdit = (isset($this->field['type-edit']))? $this->field['type-edit']: true;

                            if($typeEdit)
                                $field .= $this->get_types();
                            else
                                $field .= '<input type="hidden" name="field['.$this->field['slug'].'][type]" value="'.$this->field['type'].'">';

                        }else{

                            $field .= '<input type="hidden" name="field['.$this->field['slug'].'][type]" value="custom">';

                        }

                        $field .= '<div class="options-custom-field">';
                        $field .= $this->get_options();
                        $field .= '</div>';

                    $field .= '</div>';

                    $field .= '<input type="hidden" name="fields[]" value="'.$this->field['slug'].'">';
                    
                $field .= '</li>';
                        
        $this->field = false;

        return $field;

    }

    function empty_field(){
        
        $this->status = false;
        $this->new_slug = '$$new$$'.rand(10,100);
        $this->field['type'] = ($this->types)? $this->types[0]: 'text';

        $field = '<li data-slug="'.$this->new_slug.'" data-type="'.$this->field['type'].'" class="rcl-custom-field new-field">
                    <div class="field-header">
                        <span class="field-title half-width">'.$this->get_option(array('type'=>'text','slug'=>'title','title'=>__('Name','wp-recall'))).'</span>
                        <span class="field-controls half-width">
                            <a class="field-edit field-control" href="#" title="'.__('Edit','wp-recall').'"></a>
                        </span>
                    </div>
                    <div class="field-settings">';
        
                    if($this->meta_key){

                        $edit = ($this->primary['custom-slug'])? true: false;

                        $field .= $this->get_option(array(
                            'type' => 'text',
                            'slug'=>'slug',
                            'title'=>__('MetaKey','wp-recall'),
                            'notice'=>__('not required, but you can list your own meta_key in this field','wp-recall'),
                            'placeholder'=>__('Latin letters and numbers','wp-recall')
                        ));

                    } 
                    
                    $field .= $this->get_types();

                    $field .= '<div class="options-custom-field">';
                    $field .= $this->get_options();
                    $field .= '</div>';
                    
                $field .= '</div>';
                
                if(!$this->select_type)
                    $field .= '<input type="hidden" name="new-field['.$this->new_slug.'][type]" value="custom">';
                
                $field .= '<input type="hidden" name="fields[]" value="">';
                
            $field .= '</li>';

        return $field;
    }
    
    function get_types(){
        
        if(!$this->select_type) return false;
        
        $fields = array(
            'text'=>__('Text','wp-recall'),
            'textarea'=>__('Multiline text area','wp-recall'),
            'select'=>__('Select','wp-recall'),
            'multiselect'=>__('MultiSelect','wp-recall'),
            'checkbox'=>__('Checkbox','wp-recall'),
            'radio'=>__('Radiobutton','wp-recall'),
            'email'=>__('E-mail','wp-recall'),
            'tel'=>__('Phone','wp-recall'),
            'number'=>__('Number','wp-recall'),
            'date'=>__('Date','wp-recall'),
            'time'=>__('Time','wp-recall'),
            'url'=>__('Url','wp-recall'),
            'agree'=>__('Agreement','wp-recall'),
            'file'=>__('File','wp-recall'),
            'dynamic'=>__('Dynamic','wp-recall')
        );
        
        if($this->types){
            
            $newFields = array();
            
            foreach($fields as $key => $fieldname){
                
                if(!in_array($key,$this->types)) continue;
                
                $newFields[$key] = $fieldname;
                
            }
            
            $fields = $newFields;
            
        }
        
        $content .= $this->get_option(array(
            'title'=>__('Field type','wp-recall'),
            'slug' => 'type',
            'type' => 'select',
            'class' => 'typefield',
            'values' => $fields
        ));
        
        return $content;
        
    }

    function get_vals($name){
        foreach($this->fields as $field){
            if($field[$name]) return $field;
        }
    }

    function option($type, $args, $edit = true, $key = false){
        
        $args['type'] = $type;
        
        if(isset($args['label']))
            $args['title'] = $args['label'];
        
        if(isset($args['name']))
            $args['slug'] = $args['name'];
        
        if(isset($args['value']))
            $args['values'] = $args['value'];

        return $args;
        
    }

    function options($args){
        
        $val = ($this->fields['options']) ? $this->fields['options'][$args['name']]: '';
        $ph = (isset($args['placeholder']))? $args['placeholder']: '';
        $pattern = (isset($args['pattern']))? 'pattern="'.$args['pattern'].'"': '';
        $field = '<input type="text" placeholder="'.$ph.'" title="'.$ph.'" '.$pattern.' name="options['.$args['name'].']" value="'.$val.'"> ';
        
        return $field;
    }
    
    function inactive_fields_box(){

        $content = '<div id="rcl-inactive-fields" class="rcl-inactive-fields-box rcl-custom-fields-box">';
        
            $content .= '<h3>'.__('Неактивные поля','wp-recall').'</h3>';

            $content .= '<form>';

                $content .= '<ul class="rcl-sortable-fields">';

                    $content .= $this->loop($this->get_inactive_fields());

                $content .= '</ul>';

            $content .= '</form>';
        
        $content .= '</div>';
        
        return $content;
        
    }
    
    function get_default_fields(){
        return apply_filters('rcl_default_custom_fields', array(), $this->post_type);
    }
    
    function get_inactive_fields(){
        
        $default_fields = $this->get_default_fields();
        
        if($default_fields){
            
            foreach($default_fields as $k => $field){
                
                if($this->exist_active_field($field['slug'])){
                    unset($default_fields[$k]); continue;
                }
                
                $default_fields[$k]['class'] = 'must-receive';
                $default_fields[$k]['type-edit'] = false;
                
            }
            
        }
        
        return $default_fields;
        
    }
    
    function get_active_fields(){
        
        if(!$this->fields) return false;
        
        $options = $this->get_default_fields_options();
        
        foreach($this->fields as $k => $field){
            
            if($this->is_default_field($field['slug'])){
                
                if(isset($options[$field['slug']])){
                    $this->fields[$k]['options-field'] = $options[$field['slug']];
                }
                
                $this->fields[$k]['type-edit'] = false;
                $this->fields[$k]['class'] = 'must-receive';
                
            }
            
        }
        
        return $this->fields;
        
    }
    
    function exist_active_field($slug){
        
        if(!$this->fields) return false;
        
        foreach($this->fields as $k => $field){
            
            if($field['slug'] == $slug){
                
                return true;
                
            }
            
        }
        
        return false;
        
    }
    
    function get_default_fields_options(){
        
        $fields = $this->get_default_fields();
        
        if(!$fields) return $fields;
        
        $options = array();
        foreach($fields as $field){
            
            if(!isset($field['options-field'])) continue;
            
            $slug = $field['slug'];
            
            $options[$slug] = $field['options-field'];
            
        }
        
        return $options;
        
    }
    
    function is_default_field($slug){
        
        $fields = $this->get_default_fields();
        
        foreach($fields as $field){
            
            if($field['slug'] == $slug) return true;
            
        }
        
        return false;
        
    }
    
    /*depricated*/
    function verify(){
        
    }
    
    /*depricated*/
    function update_fields($table='postmeta'){
        
    }
    
    /*depricated*/
    function edit_form($defaultOptions = false){
        return $this->manager_form($defaultOptions);
    }
}