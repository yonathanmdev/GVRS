<?php
// src/Routes/Yoniroutes.php

return [
    // User Management
    'register-user'                 => ['UserController', 'showRegisterForm', true],
    'register-process'              => ['UserController', 'handleRegistration', true],
    'edit-user'                     => ['UserController', 'getUserById', true],
    'edit-user-process'             => ['UserController', 'handleUpdateUser', true],
    'reset-password'                => ['UserController', 'resetPassword', true],
    'delete-user-process'           => ['UserController', 'delete', true],
    'deleted-users'                 => ['UserController', 'showDeletedLists', true],
    'restore-user'           => ['UserController', 'restore', true],
    'purge-user'             => ['UserController', 'purge', true],

    // Organization Management
    'register-organization'         => ['OrgController', 'showRegisterForm', true],
    'register-organization-process' => ['OrgController', 'handleRegistration', true],
    'update-organization-process'   => ['OrgController', 'handleEditOrganization', true],
    'delete-organization-process'   => ['OrgController', 'delete', true],
    'organization-deleted-lists'         => ['OrgController', 'showDeletedLists', true],
    'restore-organization'   => ['OrgController', 'restore', true],
    'purge-organization'    => ['OrgController', 'purge', true],
    'archived-organizations' => ['OrgController', 'archiveList', true],
    'restore-from-archive' => ['OrgController', 'restoreFromArchive', true],
     // Branch Management
    'register-bureau'               => ['FunctionalOrgController', 'bureauIndex', true],
    'register-bureau-process'       => ['FunctionalOrgController', 'breauRegistration', true],
    'update-bureau-process'   => ['FunctionalOrgController', 'bureauEdit', true],
    'delete-branch-process'   => ['FunctionalOrgController', 'delete', true],
    'deleted-branches'         => ['OrgController', 'showDeletedLists', true],
    'restore-branch'   => ['OrgController', 'restore', true],
    'purge-branch'    => ['OrgController', 'purge', true],
    'archived-branches' => ['OrgController', 'branchArchiveList', true],
    'restore-from-archive-branch' => ['OrgController', 'restoreFromArchive', true],
    //zones/city administration
    'register-zone'                 => ['AdministrativeUnitController', 'zoneIndex', true],
    'register-zone-process'       => ['AdministrativeUnitController', 'zoneRegistration', true],
    'update-zone-process'   => ['AdministrativeUnitController', 'zoneEdit', true],
     //woreda
    'register-woreda'               => ['AdministrativeUnitController', 'woredaIndex', true],
    'register-woreda-process'       => ['AdministrativeUnitController', 'woredaRegistration', true],
    'update-woreda-process'   => ['AdministrativeUnitController', 'woredaEdit', true],
    

    //accoutable office
    'accountable-office'               => ['FunctionalOrgController', 'accountableOfficeIndex', true],
    'register-accountable-office'       => ['FunctionalOrgController', 'accountableOfficeRegistration', true],
    'update-accountable-office'   => ['FunctionalOrgController', 'accountableOfficeEdit', true],
    //Vehicle Management
    'register-vehicle'              => ['VehicleController', 'index', true],
    'vehicles-institutions'         => ['VehicleController', 'institutions', true],
    'vehicles-woredas'              => ['VehicleController', 'woredas', true],
    'vehicles-car-types'            => ['VehicleController', 'carTypesByBrand', true],
    'vehicles-store'      => ['VehicleController', 'store', true],
    'vehicles-list'       => ['VehicleController', 'list', true],
    'edit-vehicle'                  => ['VehicleController', 'getVehicleById', true],
    // Audit Logs
    'audit-logs'         => ['AuditController', 'index', true],
    'audit-logs-data'    => ['AuditController', 'data', true],
    'audit-logs-stats'   => ['AuditController', 'stats', true],
    'audit-logs-show'    => ['AuditController', 'show', true],
    'audit-logs-export'  => ['AuditController', 'export', true],
   ];