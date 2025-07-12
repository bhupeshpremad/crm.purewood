<?php
class Auth {
    private $users = [
        'superadmin' => [
            [
                'email' => 'superadmin@purewood.in',
                'password' => 'Admin@123'
            ]
        ],
        'salesadmin' => [
            [
                'email' => 'export@purewood.in',
                'password' => 'Admin@123'
            ],
            [
                'email' => 'sales@purewood.in',
                'password' => 'Admin@123'
            ],
             [
                'email' => 'info@purewood.in',
                'password' => 'Admin@123'
            ],
        ],
        'accounts' => [
            [
                'email' => 'accounts@purewood.in',
                'password' => 'Admin@123'
            ],
            [
                'email' => 'lalit@purewood.in',
                'password' => 'Admin@123'
            ],
        ],
        'operation' => [
            [
                'email' => 'operation@purewood.in',
                'password' => 'Admin@123'
            ],
            [
                'email' => 'ops@purewood.in',
                'password' => 'Admin@123'
            ],
        ],
        'production' => [
            [
                'email' => 'production@purewood.in',
                'password' => 'Admin@123'
            ],
        ],
    ];

    public function login($role, $inputEmail, $inputPassword) {
        if (!isset($this->users[$role])) {
            return false;
        }
        foreach ($this->users[$role] as $user) {
            if ($inputEmail === $user['email'] && $inputPassword === $user['password']) {
                return true;
            }
        }
        return false;
    }
}
?>