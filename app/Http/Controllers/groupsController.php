<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\Participants;
use Illuminate\Http\Request;

class groupsController extends Controller
{
    public function addGroup(Request $request)
    {
        $controle = $this->controlesInputs($request);
        if (!$controle[0]['bool']) {
            return redirect()->back()
                    ->with('error', $controle[0]['message']);
        }

        $nameGroup = $request->inputNameGroup;
        $groupExists = Groups::query()
            ->where('nameGroup', '=', $nameGroup)
            ->get();
        if (count($groupExists) != 0) {
            return redirect()->back()
                ->with('error', 'Le groupe '.$nameGroup.' existe déjà!');
        }

        $group = new Groups();
        $group->nameGroup = $nameGroup;
        $group->save();

        return redirect()->back()
            ->with('success', 'Le groupe "'.$nameGroup.'" à été crée avec succès!');
    }

    public function participantGroup(Request $request)
    {
        $nameGroup = $request->inputNameGroup;
        $idGroup = Groups::where('nameGroup', '=', $nameGroup)->first();

        $arrayParticipant = $request->inputParticipantArray;
        for ($i = 0; $i < count($arrayParticipant); $i++) {
            $participant = Participants::query()
                ->where('pseudo', '=', $arrayParticipant[$i])
                ->get();

            $idParticipant = $participant[0]->id;

            $participantGroup = Participants::find($idParticipant);
            $participantGroup->nameGroup = $nameGroup;
            $participantGroup->id_group = $idGroup['id'];
            $participantGroup->save();
        }
        
        return redirect()->back()
            ->with('success', 'Nouvelles composition du group '.$nameGroup.' réussi!');
    }

    public function controlesInputs($request)
    {
        $arrayControles = [];
        $regexInputName = "/^(\s)*[A-Za-z]+((\s)?((\'|\-|\.)?([A-Za-zéèîôàêç@])*))*(\s)*$/";
        $regexLiberty = "/^(\s)*[A-Za-z0-9éèîôàêç@]+((\s)?((\'|\-|\.)?([A-Za-z0-9éèîôàêç@])*))*(\s)*$/";
        $regexPhone = "/^([0-9]*)$/";

        $pwd_one = $request->inputPassword;
        $pwd_two = $request->inputPassword_confirmation;
        $phone = $request->inputTel;
        $firstName = $request->inputFirstName;
        $lastName = $request->inputLastName;
        $pseudo = $request->inputPseudo;
        $log_identifiant = $request->inputRegister;
        $nameGroup = $request->inputNameGroup;

        if ($pwd_one != null || $pwd_one != '') {
           if (!preg_match($regexLiberty, $pwd_one)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères trop spéciaux dans le mot de passe !"]);
                return $arrayControles;
            } 
        }
 
        if ($pwd_two != null || $pwd_two != '') {
            if (!preg_match($regexLiberty, $pwd_two)) {
                 array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères trop spéciaux dans le mot de passe !"]);
                 return $arrayControles;
             } 
         }
        
        if ($pwd_one != null && $pwd_two != null) {
            if ($pwd_one != $pwd_two) {
                array_push($arrayControles, ['bool' => false, 'message' => "Les deux mot de passes ne correspondent pas!"]);
                return $arrayControles;
            }
        }
        
        if ($phone != null || $phone != '') {
           if (!preg_match($regexPhone, $phone)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Veuillez rentrer seulement des numéros sans espaces pour le numéro de téléphone, s'il vous plait!"]);
                return $arrayControles;
            } 
        }
        
        if ($firstName != null || $firstName != '') {
            if (!preg_match($regexInputName, $firstName)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux et chiffres dans le prenom!"]);
                return $arrayControles;
            }
        }
        
        if ($lastName != null || $lastName != '') {
           if (!preg_match($regexInputName, $lastName)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux et chiffres dans le nom!"]);
                return $arrayControles;
            } 
        }
        
        if ($pseudo != null || $pseudo != '') {
            if (!preg_match($regexLiberty, $pseudo)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux dans le pseudo!"]);
                return $arrayControles;
            }
        }
        
        if ($log_identifiant != null || $log_identifiant != '') {
            if (!preg_match($regexLiberty, $log_identifiant)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux dans l'identifiant !"]);
                return $arrayControles;
            }
        }

        if ($nameGroup != null || $nameGroup != '') {
            if (!preg_match($regexLiberty, $nameGroup)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux dans le nom du groupe!"]);
                return $arrayControles;
            }
        }

//###---If all is alright sending back 'true' with empty message---###

        array_push($arrayControles, ['bool' => true, 'message' => ""]);
            return $arrayControles;
    }
}
