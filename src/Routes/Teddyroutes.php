<?php
// src/Routes/Teddyroutes.php

return [
    'register-car-brand'          => ['CarBrandRegistration', 'carregIndex', true],
    'register-car-brand-process'  => ['CarBrandRegistration', 'carBrandRegistration', true],
    'update-car-brand-process'    => ['CarBrandRegistration', 'carBrandEdit', true],
    'delete-car-brand-process'    => ['CarBrandRegistration', 'deleteCarBrand', true],
    'register-car-type'           => ['CarBrandRegistration', 'index', true],
    'register-car-type-process'   => ['CarBrandRegistration', 'store', true],
    'update-car-type-process'     => ['CarBrandRegistration', 'updateProcess', true],
    'delete-car-type-process'     => ['CarBrandRegistration', 'deleteProcess', true],
    'car-type-list'                 => ['CarTypeListController', 'index', true],
    'register-car-type-list-process' => ['CarTypeListController', 'store', true],
    'update-car-type-list-process'   => ['CarTypeListController', 'updateProcess', true],
    'delete-car-type-list-process'   => ['CarTypeListController', 'deleteProcess', true],

 

 
   
    
];
