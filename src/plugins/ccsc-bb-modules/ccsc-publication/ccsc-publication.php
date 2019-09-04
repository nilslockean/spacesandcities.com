<?php

// https://kb.wpbeaverbuilder.com/article/598-cmdg-03-define-module-settings
class MyModuleClass extends FLBuilderModule {
  public function __construct() {
    parent::__construct(array(
      'name'            => __( 'CCSC Publication Module', 'fl-builder' ),
      'description'     => __( 'A totally awesome module!', 'fl-builder' ),
      'group'           => __( 'My Group', 'fl-builder' ),
      'category'        => __( 'My Category', 'fl-builder' ),
      'dir'             => CCSC_MODULES_DIR . 'ccsc-publication/',
      'url'             => CCSC_MODULES_URL . 'ccsc-publication/',
      'icon'            => 'button.svg',
      'editor_export'   => true, // Defaults to true and can be omitted.
      'enabled'         => true, // Defaults to true and can be omitted.
      'partial_refresh' => false, // Defaults to false and can be omitted.
    ));
  }
}

?>