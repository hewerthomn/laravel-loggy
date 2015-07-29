<?php

/**
 * Loggy Config
 */

return [
  'app_id' => 'laravel',

  'userClass' => 'App\User',

  'classes' => [
    'success' => 'text-success',
    'info' => 'text-info',
    'warning' => 'text-warning',
    'error' => 'text-danger',
    'exception' => 'text-danger'
  ],

  'icons' => [
    'success' => '<i class="fa fa-ok"></i>',
    'info' => '<i class="fa fa-info-sign"></i>',
    'warning' => '<i class="fa fa-warning-sign"></i>',
    'error' => '<i class="fa fa-exclamation-sign"></i>',
    'exception' => '<i class="fa fa-bug"></i>'
  ]
];