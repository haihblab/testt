<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(
            [
                ['name' => 'Admin'],
                ['name' => 'Manager'],
                ['name' => 'User'],
            ]
        );
        DB::table('departments')->insert(
            [
                ['name' => 'Hành chính nhân sự'],
                ['name' => 'HB1'],
                ['name' => 'HB2'],
                ['name' => 'HB3'],
                ['name' => 'HB4'],
            ]
        );
        DB::table('users')->insert(
            [
                [
                    'email' => 'user@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Admin',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'adm1',
                ],

                [
                    'email' => 'hainv@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Ngô Văn Hải',
                    'role_id' => 2,
                    'department_id' => 4,
                    'status' => 1,
                    'staff_id' => 'hainv',
                ],

                [
                    'email' => 'haidn@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Đỗ Ngọc Hải',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'haidn',
                ],

                [
                    'email' => 'anhnkd@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Kim Duy Anh',
                    'role_id' => 2,
                    'department_id' => 3,
                    'status' => 1,
                    'staff_id' => 'anhnkd',
                ],

                [
                    'email' => 'giangtt@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Trần Thu Giáng',
                    'role_id' => 2,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'giangtt',
                ],

                [
                    'email' => 'chienlx@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Lê Xuân Chiến',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'chienlx',
                ],
                
                [
                    'email' => 'conglb@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Lưu Bình Công',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'conglb',
                ],

                [
                    'email' => 'vuht@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Hoàng Thái Vũ',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'vuht',
                ],

                [
                    'email' => 'datdt@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Đồng Tiến Đạt',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'datdt',
                ],

                [
                    'email' => 'xoannt@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Thị Xoan',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'xoannt',
                ],

                [
                    'email' => 'namnh@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Hoàng Nam',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'namhn',
                ],

                [
                    'email' => 'lannh@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Hoàng Lân',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'lannh',
                ],
                
                [
                    'email' => 'huannn@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Ngọc Huân',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'huannn',
                ],

                [
                    'email' => 'dieppv@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Phùng Văn Điệp',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'dieppv',
                ],

                [
                    'email' => 'vinhnt@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Thiện Vinh',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'vinhnt',
                ],

                [
                    'email' => 'trangtt@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Thái Thị Trang',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'trangtt',
                ],

                [
                    'email' => 'longtm@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Trần Mạnh Long',
                    'role_id' => 3,
                    'department_id' => 2,
                    'status' => 1,
                    'staff_id' => 'longtm',
                ],

                [
                    'email' => 'manhdc@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Đỗ Công Mạnh',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'manhdc',
                ],

                [
                    'email' => 'datnt@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Nguyễn Thành Đạt',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'datnt12',
                ],

                [
                    'email' => 'huongdv@hblab.vn', 
                    'password' => Hash::make(12345678),
                    'name' => 'Đàm Văn Hưởng',
                    'role_id' => 1,
                    'department_id' => 1,
                    'status' => 1,
                    'staff_id' => 'huongdv',
                ],
            ]
        );
    }
}
