<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tour;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. TẠO CÁC VAI TRÒ (ROLES) TRƯỚC TIÊN
        Role::findOrCreate('admin');
        Role::findOrCreate('host');
        Role::findOrCreate('tourist');
        Permission::findOrCreate(User::PERMISSION_HOST_PENDING);
        Permission::findOrCreate(User::PERMISSION_HOST_REJECTED);
        Permission::findOrCreate(User::PERMISSION_ACCOUNT_LOCKED);
        
        // 1. Tạo một tài khoản Host mẫu
        $host = User::create([ 
            'name' => 'Sùng A Páo',
            'email' => 'sungapao@reocao.vn',
            'password' => bcrypt('password'),
        ]);
        
        // Cấp quyền host (Nếu đã cài Spatie ở bước trước)
        $host->assignRole('host');

        $admin = User::create([
            'name' => 'ADMIN',
            'email' => 'admin@reocao.vn',
            'password' => bcrypt('reocao111'),
        ]);

        $admin->assignRole('admin');

        // 2. Tạo bài viết Tạp chí mẫu
        Post::create([
            'title' => 'Kỳ vĩ sóng lúa ruộng bậc thang thung lũng Mường Hoa',
            'slug' => Str::slug('Kỳ vĩ sóng lúa ruộng bậc thang thung lũng Mường Hoa'),
            'content' => 'Nội dung chi tiết về hành trình trải nghiệm sắc vàng rực rỡ...',
            'status' => 'published',
        ]);

        Post::create([
            'title' => 'Sắc màu chàm đặc trưng trong trang phục người Dao đỏ',
            'slug' => Str::slug('Sắc màu chàm đặc trưng trong trang phục người Dao đỏ'),
            'content' => 'Tìm hiểu nghệ thuật nhuộm chàm cổ truyền, kỹ thuật thêu tay...',
            'status' => 'published',
        ]);

        // 3. Tạo Tour trải nghiệm mẫu
        Tour::create([
            'host_id' => $host->id,
            'name' => 'Tour gặt lúa cùng người bản địa Hoàng Su Phì',
            'slug' => Str::slug('Tour gặt lúa cùng người bản địa Hoàng Su Phì'),
            'description' => 'Trải nghiệm văn hóa nông nghiệp độc đáo mùa lúa chín...',
            'price' => 3200000,
            'capacity' => 10,
            'itinerary' => 'Ngày 1: Đón khách tại Hà Giang... Ngày 2: Gặt lúa...',
            'is_active' => true,
        ]);
    }
}
