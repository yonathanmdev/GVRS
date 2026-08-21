<?php

namespace App\Config;

class MenuConfig
{
    public static function getTree(): array
    {

       return [

            [
                'label' => \__('menu_dashboard'),
                'icon'  => 'fas fa-tachometer-alt',
                'url'   => '/dashboard',
                'roles' => ['system_admin', 'admin','mgmt', 'officer'],
            ],

            [
                'label' => \__('menu_register'),
                'icon'  => 'fas fa-edit',
                'roles' => ['system_admin'],
                'children' => [

                    [
                        'label' => \__('menu_organization'),
                        'url'   => '/register-organization',
                        'roles' => ['system_admin']
                    ],
                    [
                        'label' => \__('menu_user'),
                        'url'   => '/register-user',
                        'roles' => ['system_admin']
                        
                    ],
                ]
            ],
            [
                'label' => 'ተቆጣጣሪ',
                'icon'  => 'fas fa-edit',
                'roles' => ['admin'],
                'children' => [
                    [
                        'label' =>'መመዝገብ',
                        'url'   => '/register-user',
                        'roles' => ['system_admin', 'admin']
                        
                    ],
                ]
            ],
             [
                'label' => 'አስተዳደርያዊ መዋቅር',
                'icon'  => 'fas fa-edit',
                'roles' => ['system_admin', 'admin'],
                'children' => [
                    [
                        'label' => \__('menu_zone').' መመዝገብ',
                        'url'   => '/register-zone',
                        'roles' => ['system_admin', 'admin'],
                    ],
                    [
                        'label' => \__('menu_woreda').' መመዝገብ',
                        'url'   => '/register-woreda',
                        'roles' => ['system_admin', 'admin'],
                       
                    ],
                ]
            ],
             [
                'label' => 'ተቋማዊ መዋቅር',
                'icon'  => 'fas fa-edit',
                'roles' => ['system_admin', 'admin'],
                'children' => [
                    [
                        'label' => \__('menu_bureau').' መመዝገብ',
                        'url'   => '/register-bureau',
                        'roles' => ['system_admin', 'admin'],
                    ],
                    [
                        'label' => 'መምሪያ /ተጠሪ ተቋማት',
                        'url'   => '/accountable-office',
                        'roles' => ['system_admin', 'admin'],
                    ],
                ]
            ],
               [
                'label' => 'መረጃ ማዕከላዊ መዋቅር',
                'icon'  => 'fas fa-edit',
                'roles' => ['system_admin', 'admin'],
                'children' => [
                    [
                        'label' => 'የመኪና ብራንድ መመዝገብ',
                        'url'   => '/register-car-brand',
                        'roles' => ['system_admin', 'admin'],
                    ],
                           [
                        'label' => 'የመኪና አይነት መመዝገብ',
                        'url'   => '/register-car-type',
                        'roles' => ['system_admin', 'admin'],
                    ],
                    
                    [
                        'label' => 'የመኪና አይነት ዝርዝር',
                        'url'   => '/car-type-list',
                        'roles' => ['system_admin', 'admin'],
                    ],
                 
                ]
            ],
            [
                'label' => 'ተሽከርካሪ',
                'icon'  => 'fas fa-edit',
                'roles' => ['system_admin','mgmt', 'officer'],
                'children' => [
                    [
                        'label' => 'ተሽከርካሪ መመዝገብ',
                        'url'   => '/register-vehicle',
                       'roles' => ['system_admin', 'officer'],
                    ],
                    [
                        'label' => 'ተሽከርካሪ ዝርዝር',
                        'url'   => '/vehicles-list',
                       'roles' => ['system_admin','mgmt', 'officer'],
                    ],
                    
                ]
            ],
            
        ];
    }

    public static function getMenuForRoleAndLevel(string $role, int $level): array
    {
        $filtered = [];

        foreach (self::getTree() as $item) {

            if (!self::matches($item, $role, $level)) {
                continue;
            }

            if (!empty($item['children'])) {

                $children = array_values(array_filter(
                    $item['children'],
                    fn($child) => self::matches($child, $role, $level)
                ));

                if (empty($children)) {
                    continue;
                }

                $item['children'] = $children;
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    private static function matches(array $item, string $role, int $level): bool
    {
        $roleOk = in_array($role, $item['roles'] ?? [], true);

        $levelOk = !isset($item['levels'])
            || in_array($level, $item['levels'], true);

        return $roleOk && $levelOk;
    }
}