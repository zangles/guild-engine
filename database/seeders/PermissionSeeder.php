<?php

namespace Database\Seeders;

use App\Enums\GuildPermission;
use App\Models\Main\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['slug' => GuildPermission::ManageEvents->value,       'name' => 'Gestionar eventos'],
            ['slug' => GuildPermission::ApproveMembers->value,     'name' => 'Aprobar miembros'],
            ['slug' => GuildPermission::InviteMembers->value,      'name' => 'Invitar miembros'],
            ['slug' => GuildPermission::KickMembers->value,        'name' => 'Expulsar miembros'],
            ['slug' => GuildPermission::ManageDkp->value,          'name' => 'Gestionar DKP'],
            ['slug' => GuildPermission::ManageDonations->value,    'name' => 'Gestionar donaciones'],
            ['slug' => GuildPermission::RegisterAttendance->value, 'name' => 'Registrar asistencia'],
            ['slug' => GuildPermission::ViewAuditLog->value,       'name' => 'Ver log de auditoría'],
            ['slug' => GuildPermission::ManageRoles->value,        'name' => 'Gestionar roles'],
            ['slug' => GuildPermission::TransferLeadership->value, 'name' => 'Transferir liderazgo'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            );
        }
    }
}
