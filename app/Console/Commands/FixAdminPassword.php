<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:admin-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mereset password semua akun admin menjadi "password" dengan hashing Bcrypt yang benar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminUsers = \App\Models\User::where('role', 'admin')->get();

        if ($adminUsers->isEmpty()) {
            $this->error('Tidak ditemukan user dengan role admin.');
            return;
        }

        foreach ($adminUsers as $user) {
            $this->info("Memproses user: {$user->email}");
            
            // Log current password info (not the password itself)
             $info = password_get_info($user->password);
             $this->line("Algoritma saat ini: " . ($info['algoName'] ?? 'unknown'));

             $user->password = 'password';
             $user->save();

            $this->info("Password untuk {$user->email} telah direset menjadi 'password'.");
        }

        $this->info('Selesai.');
    }
}
