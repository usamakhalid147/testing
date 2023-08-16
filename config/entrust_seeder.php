<?php

return [
    'role_structure' => [
        'admin' => [
            'admin_users'       => 'c,r,u,d',
            'roles'             => 'c,r,u,d',
            'login_sliders'     => 'c,r,u,d',
            'users'             => 'c,r,u,d',
            'hosts'             => 'c,r,u,d',
            // 'email_to_users'    => 'r,u',
            'sliders'           => 'c,r,u,d',
            'featured_cities'   => 'c,r,u,d',
            'pre_footers'       => 'c,r,u,d',
            'popular_cities'    => 'c,r,u,d',
            'discount_banners'  => 'r,u',
            'reservations'      => 'r',
            'payouts'           => 'r',
            'penalties'         => 'r',
            'reviews'           => 'r,u',
            'hotels'            => 'c,r,u,d',
            'rooms'             => 'c,r,u,d',
            'property_types'    => 'c,r,u,d',
            'room_types'        => 'c,r,u,d',
            'amenity_types'     => 'c,r,u,d',
            'hotel_amenities'   => 'c,r,u,d',
            'room_amenities'    => 'c,r,u,d',
            'bed_types'         => 'c,r,u,d',
            'guest_accesses'    => 'c,r,u,d',
            'hotel_rules'       => 'c,r,u,d',
            'meal_plans'        => 'c,r,u,d',
            'help_categories'   => 'c,r,u,d',
            'helps'             => 'c,r,u,d',
            'blog_categories'   => 'c,r,u,d',
            'blogs'             => 'c,r,u,d',
            'api_credentials'   => 'r,u',
            'payment_gateways'  => 'r,u',
            'email_configurations'=> 'r,u',
            'global_settings'   => 'r,u',
            'social_media_links'=> 'r,u',
            'metas'             => 'r,u',
            'fees'              => 'r,u',
            'referral_settings' => 'r,u',
            'referrals'         => 'r,u',
            'reports'           => 'r',
            'transactions'      => 'r',
            'coupon_codes'      => 'c,r,u,d',
            'countries'         => 'c,r,u,d',
            'cities'            => 'c,r,u,d',
            'currencies'        => 'c,r,u,d',
            'static_page_header'=> 'r,u',
            'static_pages'      => 'c,r,u,d',
            // 'popular_localities'=> 'c,r,u,d',
            // 'languages'         => 'c,r,u,d',
            // 'translations'      => 'r,u',
        ],
        'host' => [
            'edit_profile'      => 'r,u',
            'edit_company'      => 'r,u',
            'host_users'        => 'c,r,u,d',
            'host_roles'        => 'c,r,u,d',
            'host_hotels'       => 'c,r,u,d',
            'host_rooms'        => 'c,r,u,d',
            'host_reservations' => 'r',
            'host_payouts'      => 'r',
            'host_reviews'      => 'r',
            'host_coupon_codes' => 'c,r,u,d',
            'payout_methods'    => 'c,r,u,d',
            'host_reports'      => 'r',
            // 'inbox'             => 'r,u',
        ],
    ],
    'user_roles' => [
        'admin' => [
            [
                'username' => "admin",
                'email' => 'admin@gmail.com',
                'password' => '12345678',
                'user_currency' => 'en',
                'status' => 1,
                'primary' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'view',
        'u' => 'update',
        'd' => 'delete',
        'm' => 'manage',
    ],
];