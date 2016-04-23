<?php
class YA_Options_pages_multi_select extends YA_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since YA_Options 1.0.1
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since YA_Options 1.0.1
	*/
	function render(){
		
		$class = (isset($this->field['class']))?'class="'.esc_attr( $this->field['class'] ).'" ':'';
		
		echo '<select id="'.esc_attr( $this->field['id'] ).'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" '.$class.'multiple="multiple" >';

		$args = wp_parse_args($this->field['args'], array());
		
		$pages = get_pages($args); 
		foreach ( $pages as $page ) {
			$selected = (is_array($this->value) && in_array($page->ID, $this->value))?' selected="selected"':'';
			echo '<option value="'.esc_attr( $page->ID ).'"'.$selected.'>'.esc_html( $page->post_title ).'</option>';
		}
		
		

		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><span class="description">'.esc_html( $this->field['desc'] ).'</span>':'';
		
	}//function
	
}//class
?>