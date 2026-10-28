<?php

namespace App\Console\Commands;

use App\Domain\Auth\Models\User;
use App\Enums\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateTeacherCommand extends Command
{
    protected $signature = 'teacher:create {--name= : اسم المدرس} {--email= : إيميل المدرس} {--password= : الباسورد}';
    protected $description = 'إنشاء حساب مدرس جديد (لا يوجد تسجيل عام للمدرسين)';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('اسم المدرس');
        $email = $this->option('email') ?? $this->ask('إيميل المدرس');
        $password = $this->option('password') ?? $this->secret('الباسورد');

        // Validation
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        // Create Teacher
        $teacher = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => UserRole::Teacher,
        ]);

        $this->info("تم إنشاء المدرس بنجاح: {$teacher->email} (ID: {$teacher->id})");
        return self::SUCCESS;
    }
}