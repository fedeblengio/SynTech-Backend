<?php

namespace App\Traits;

trait verificarUsuarioPerteneceGrupoAD
{
    /**
     * Uploads a file to the specified path
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $path
     * @return string
     */

     public function verificarPerteneceGrupoAD($user,$grupo){
        $groupsAD = $user->groups()->get();
            if($groupsAD->contains($grupo)){
                return true;
            }
        return false;
    }

}