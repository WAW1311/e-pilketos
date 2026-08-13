<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
                            {--name= : Nama admin}
                            {--email= : Email admin}
                            {--password= : Password admin; hindari pemakaian pada shell bersama}';

    protected $description = 'Membuat akun administrator melalui terminal';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nama admin');
        $email = $this->option('email') ?: $this->ask('Email admin');
        $password = $this->option('password') ?: $this->secret('Password admin (minimal 12 karakter)');

        validator(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
                'password' => ['required', 'string', 'min:12'],
            ]
        )->validate();

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('Akun administrator berhasil dibuat.');

        return self::SUCCESS;
    }
}
