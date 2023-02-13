<?php

namespace App\Console\Commands;

use App\Models\bedelias;
use App\Models\usuarios;
use Illuminate\Console\Command;
use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Models\ActiveDirectory\User;

class CrearPrimerosUsuarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:first-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea los primeros usuarios del sistema';

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
        dump('Creando usuario');
        $cedula = "00000000";
        $user = (new User)->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->cn =$cedula;
        $user->unicodePwd = $cedula;
        $user->samaccountname =$cedula;
        $user->save();
        $user->refresh();
        $user->userAccountControl = 66048;
        $user->save();

        usuarios::factory()->create([
            'id' => $cedula,
            'ou'=>'Bedelias'
        ]);

        bedelias::factory()->create([
            'id' => $cedula,
            'Cedula_Bedelia'=>$cedula,
            'cargo'=>'administrador'
         ]);

         try{
            $group = Group::find('cn=Supervisor,ou=Grupos,dc=syntech,dc=intra');
            $group->members()->attach($user);
         }catch(\Exception $e){
            dump($e);
         }
        

        dump('Fin');

    }
}
