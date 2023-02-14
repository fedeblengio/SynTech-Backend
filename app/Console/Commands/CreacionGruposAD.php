<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LdapRecord\Models\ActiveDirectory\Group;

class CreacionGruposAD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:gruposAD';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea los grupos de AD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
    dump('Creando Grupos AD');
    $grupos = ['Adminsitrativo', 'Director', 'Adscripto', 'Subdirector', 'Profesor', 'Alumno', 'Bedelias', 'Supervisor'];
    foreach ($grupos as $grupo) {
        if(Group::find('cn='.$grupo.',ou=Grupos,dc=syntech,dc=intra'))
        { 
            continue;
        }
        $group = (new Group)->inside('ou=Grupos,dc=syntech,dc=intra');
        $group->cn = $grupo;
        $group->save();
    }
    $group = Group::find('cn=Bedelias,ou=Grupos,dc=syntech,dc=intra');
    $groupsMembers = ['Adminsitrativo', 'Director', 'Adscripto', 'Subdirector'];
    foreach ($groupsMembers as $groupMember) {
        $groupsMember = Group::find('cn='.$groupMember.',ou=Grupos,dc=syntech,dc=intra');
        $group->members()->attach($groupsMember);
    }
    dump('Fin');
    }
}
