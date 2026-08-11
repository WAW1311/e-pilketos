<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KioskToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiosk:token {--email=kiosk@evoting.local : Email untuk akun kiosk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat/mereset Sanctum bearer token untuk aplikasi kiosk mobile';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Kiosk',
                'password' => Hash::make(Str::random(40)),
            ]
        );

        // Cabut token lama agar hanya satu token aktif per kiosk.
        $user->tokens()->delete();

        $token = $user->createToken('mobile-kiosk')->plainTextToken;

        $this->info('Token kiosk berhasil dibuat.');
        $this->line('Akun  : ' . $user->email);
        $this->line('Token : ' . $token);
        $this->newLine();
        $this->comment('Salin token di atas ke file mobile/.env sebagai:');
        $this->line('API_TOKEN=' . $token);

        return self::SUCCESS;
    }
}
