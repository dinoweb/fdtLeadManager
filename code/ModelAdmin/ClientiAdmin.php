<?php

class ClientiAdmin extends ModelAdmin {
  public static $managed_models = array('Cliente', 'Leads'); // Can manage multiple models
  static $url_segment = 'clienti'; // Linked as /admin/products/
  static $menu_icon = 'framework/admin/images/menu-icons/16x16/community.png';
  static $menu_title = 'Clienti e Leads';
}