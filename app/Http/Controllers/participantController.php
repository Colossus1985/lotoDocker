<?php

namespace App\Http\Controllers;

use App\Models\Gains;
use App\Models\Groups;
use App\Models\Money;
use App\Models\Participants;
use App\Models\User;
use App\Models\Users;
use Egulias\EmailValidator\Parser\PartParser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class participantController extends Controller
{
    public function home($userId)
    {
        if ($userId == "all") {
            $participants = Participants::query()
            ->orderBy('nameGroup', 'asc')
            ->get();
        
            $groups = Groups::query()
                ->get();

            $arrayFondsByGroup = $this->fonds($groups);
            $arrayGainByGroup = $this->gains($groups);
            $groupsDispo = $this->groupsDisponible();

            return view('pages.main', [
                'participants' => $participants, 
                'fonds' => $arrayFondsByGroup, 
                'groupsDispo' => $groupsDispo,
                'sommeGainsByGroups' => $arrayGainByGroup,
                'groups' => $groups]);
        } else {
            $participants = Participants::query()
            ->where('id', '=', $userId)
            ->get();
        
            $groups = Groups::query()
                ->get();

            $arrayFondsByGroup = $this->fonds($groups);
            $arrayGainByGroup = $this->gains($groups);
            $groupsDispo = $this->groupsDisponible();

            return view('pages.main', [
                'participants' => $participants, 
                'fonds' => $arrayFondsByGroup, 
                'groupsDispo' => $groupsDispo,
                'sommeGainsByGroups' => $arrayGainByGroup,
                'groups' => $groups]);
        }
    }
    
    public function addParticipant(Request $request)
    {
        $controle = $this->controlesInputs($request);
        if (!$controle[0]['bool']) {
            return redirect()->back()
                    ->with('error', $controle[0]['message']);
        }

        $request -> validate([
            'inputFirstName' => 'required',
            'inputLastName' => 'required',
            'inputPseudo' => 'required',
            'inputTel' => 'required',
            'inputPassword' => 'required',
            'inputPassword_confirmation' => 'required|same:inputPassword'
        ]);

        $pseudo = $request->inputPseudo;
        $email = $request->inputEmail;
        $password = $request->inputPassword;

        //###---controle if pseudo or mail are already in use---###############################################
        if ($email == null || $email == "") {
            $participantExist = Participants::query()
                ->where('pseudo', '=', $pseudo)
                ->get();
            
            if (count($participantExist) != 0) {
                return redirect()->back()
                    ->with('error', $pseudo.' déjà existant!');
            }
        } else {
            $participantExist = Participants::query()
                ->where('email', '=', $email)
                ->get();
            
            if (count($participantExist) != 0) {
                return redirect()->back()
                    ->with('error', $email.' déjà existant!');
            }
        }

        //###---Add participant in UsersTable---###############################################
        $user = new User();
        $user->firstName = $request->inputFirstName;  
        $user->lastName = $request->inputLastName; 
        $user->pseudo = $pseudo;   
        $user->email = $email;  
        $user->phone = $request->inputTel;  
        $user->password = Hash::make($password);
        $user->save();

        //###---Add participant in ParticipantsTable---###############################################
        $participant = new Participants();
        $participant->firstName = $request->inputFirstName;
        $participant->lastName = $request->inputLastName;
        $participant->nameGroup = $request->inputNameGroup;
        $participant->pseudo = $pseudo;
        $participant->email = $email;
        $participant->tel = $request->inputTel;
        $participant->save();

        //###---Add participant in moneyTable---###############################################
        $participant = Participants::query()
            ->where('pseudo', '=', $pseudo)
            ->get();
        $id_pseudo = $participant[0]->id;
        $money = new Money();
        $money->pseudo = $pseudo;
        $money->id_pseudo = $id_pseudo;
        $money->save();

        return redirect()->back()
            ->with('success', $pseudo." enregistré(e) avec succès !");
    }

    public function participantDelete($idParticipant)
    {
        
        $participant = Participants::query()
            ->select('pseudo')
            ->where('id', '=', $idParticipant)
            ->get();
        $pseudo = $participant[0]->pseudo;

        $admins = Users::query()
            ->select('admin')
            ->where('admin', '=', 1)
            ->get();

        if (Auth::user()->admin == 1 && Auth::user()->id != $idParticipant) {
            $this->deleteUser($idParticipant, $pseudo);
            return redirect()->route('home', 'all')
                ->with('success', $pseudo.' a été supprimé avec succès!');

        } else if (Auth::user()->admin == 1 && Auth::user()->id == $idParticipant) {
            if (count($admins) > 1 ) {
                $this->deleteUser($idParticipant, $pseudo);
                
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                
                return redirect()->route('logReg')
                    ->with('success', 'Vous ne faite plus parti(e) de "Loto avec Flo"');

            } else {
                return redirect()->back()
                    ->with('error', 'Tant que vous êtes le seul administrateur vous ne pouvez vous supprimer!'); 
            }

        } else if (Auth::user()->admin == 0) {
            $this->deleteUser($idParticipant, $pseudo);
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            
            return redirect()->route('logReg')
                ->with('success', 'Vous ne faite plus parti(e) de "Loto avec Flo"');
        }
            
        
    }

    public function updateParticipant(Request $request, $idParticipant)
    {
        // dd($request);
        $controle = $this->controlesInputs($request);
        if (!$controle[0]['bool']) {
            return redirect()->back()
                    ->with('error', $controle[0]['message']);
        }

        $request->validate([
            'inputPasswordActuel' => 'required|current_password',
        ]);

        $pseudo = $request->inputPseudo;

        $participant = Participants::find($idParticipant);

        $inputNameGroupNew = $request->inputNameGroupNew;
        if ($inputNameGroupNew == "Pas de groupe") {
            $inputNameGroup = Null;
            // dd($inputNameGroup);
            $id_group = Null;
            $participant->id_group = $id_group;
        } else {
            $inputNameGroup = $inputNameGroupNew;
            $id_group = Groups::where('nameGroup', '=', $inputNameGroup)->first();
            $participant->id_group = $id_group['id'];
        }
        
        $participant->firstName = $request->inputFirstName;
        $participant->lastName = $request->inputLastName;
        $participant->nameGroup = $inputNameGroup;
        $participant->pseudo = $request->inputPseudo;
        $participant->email = $request->inputEmail;
        $participant->tel = $request->inputTel;
        try {
            $participant->save();
        } catch (Exception $e){
            return redirect()->back()
                ->with('error', 'La mise à jour a échoué!');
        }
        
        $user = User::find($idParticipant);
        if ($request -> inputPassword != "" || $request -> inputPassword != null) {
            $user->password = Hash::make($request -> inputPassword);
        } 
        try {
            $user->save();
        } catch (Exception $e){
            return redirect()->back()
                ->with('error', 'La mise à jour a échoué!');
        }

        return redirect()->back()
            ->with('success', $pseudo.' mis(e) à jour!');
    }
    
    public function participant($idParticipant)
    {
        // dd($idParticipant);
        $participants = Participants::query()
            ->get();

        $participant = Participants::query()
            ->where('id', '=', $idParticipant)
            ->get();
        // dd($participant);
        // dd($participant[0]->nameGroup);
        $id_pseudo = $participant[0]->id;
        $money = Money::query()
            ->where('id_pseudo', '=', $id_pseudo)
            ->orderBy('date', 'desc')
            ->paginate(15);

        $groups = Groups::query()
            ->get();

        $arrayFondsByGroup = $this->fonds($groups);
        $arrayGainByGroup = $this->gains($groups);
        $groupsDispo = $this->groupsDisponible();

        return view('pages.participant', [
            'actions' => $money, 
            'participants' => $participants,
            'participant' => $participant,
            'fonds' => $arrayFondsByGroup,
            'groupsDispo' => $groupsDispo,
            'sommeGainsByGroups' => $arrayGainByGroup,
            'groups' => $groups]);
    }

    public function searchParticipant(Request $request)
    {
        $controle = $this->controlesInputs($request);
        if (!$controle[0]['bool']) {
            return redirect()->back()
                    ->with('error', $controle[0]['message']);
        }

        $userSearched = $request->inputParticipant;
        
        $participants = Participants::query()
            ->where('pseudo', 'like', "%{$userSearched}%")
            ->orderBy('created_at', 'ASC')
            ->get();

        if (count($participants) == 0) {
            return redirect()->route('home')
                ->with('error', 'il n\'y pas de Pseudo contenant "'.$userSearched.'"');
        }

        $groups = Groups::query()
            ->get();

        $arrayFondsByGroup = $this->fonds($groups);
        $arrayGainByGroup = $this->gains($groups);
        $groupsDispo = $this->groupsDisponible();

        return view('pages.main', [
            'participants' => $participants, 
            'fonds' => $arrayFondsByGroup, 
            'groupsDispo' => $groupsDispo,
            'sommeGainsByGroups' => $arrayGainByGroup,
            'groups' => $groups]);
    }

    public function changeGroup(Request $request, $idParticipant)
    {
        $name_group = $request->inputNameGroupNew;
        $id_group = Groups::where('nameGroup', '=', $name_group)->first();

        $participant = Participants::find($idParticipant);
        $participant->nameGroup = $name_group;
        if ($id_group != null) {
            $participant->id_group = $id_group['id'];
        } else {
            $participant->id_group = null;
        }
        
        try {
            $participant->save();
        } catch(Exception) {
            return redirect()->back()
                ->with('error', 'Un problème de mise a jour a été rencontré !');
        }
        
        return redirect()->back()
            ->with('success', 'Modification de groupe a été enregistrée avec succès !');
    }

    public function rgpd() 
    {
        $participants = Participants::query()
            ->get();

        $groups = Groups::query()
            ->get();

        $arrayFondsByGroup = $this->fonds($groups);
        $arrayGainByGroup = $this->gains($groups);
        $groupsDispo = $this->groupsDisponible();

        return view('pages.rgpd', [ 
            'participants' => $participants, 
            'fonds' => $arrayFondsByGroup, 
            'groupsDispo' => $groupsDispo,
            'sommeGainsByGroups' => $arrayGainByGroup,
            'groups' => $groups]);
    }
    

//####################################################################################################

    public function fonds($groups)
    {
        $arrayFondsByGroup = [];
        if (count($groups) != 0) {
            for ($i = 0;  $i < count($groups) ; $i++) {
                $participantOfGroup = Participants::query()
                    ->where('nameGroup', '=', $groups[$i]->nameGroup)
                    ->get();

                $fonds = 0.00;
                if (count($participantOfGroup) != 0) {
                    for ($a = 0;  $a < count($participantOfGroup); $a++) {
                        $fonds = $fonds + $participantOfGroup[$a]->amount;
                    }

                    $fonds = number_format($fonds, 2);
                    array_push($arrayFondsByGroup, ['nameGroup' => $groups[$i]->nameGroup, 'fonds' => $fonds]);
                    
                }
            }
        }
        return $arrayFondsByGroup;
    }

    public function gains($groups)
    {
        $arrayGainByGroup = [];
        if (count($groups) != 0) {
            for ($i = 0;  $i < count($groups) ; $i++) {
               
                $gainGroup = Gains::query()
                    ->where('nameGroup', '=', $groups[$i]->nameGroup)
                    ->get();

                $sommeGains = 0.00;
                if (count($gainGroup) != 0) {
                    for ($a = 0; $a < count($gainGroup); $a++) {
                        $sommeGains = $sommeGains + $gainGroup[$a]->amount;
                    }   
                    $sommeGains = number_format($sommeGains, 2);
                    array_push($arrayGainByGroup, ['nameGroup' => $groups[$i]->nameGroup, 'sommeGains' => $sommeGains]); 
                    
                } 
            }
        }
        return $arrayGainByGroup;
    }

    public function groupsDisponible() 
    {
        $groupsDispo = Groups::join(
                'participants', 'groups.id', '=', 'participants.id_group')
            ->select('groups.nameGroup')
            ->groupByRaw('groups.nameGroup')
            ->get();

        return $groupsDispo;
    }
    
    public function controlesInputs($request)
    {
        $arrayControles = [];
        $regexInputName = "/^(\s)*[A-Za-z]+((\s)?((\'|\-|\.)?([A-Za-zéèîôàêç@])*))*(\s)*$/";
        $regexInputPseudoPdw = "/^(\s)*[A-Za-z0-9éèîôàêç@]+((\s)?((\'|\-|\.)?([A-Za-z0-9éèîôàêç@])*))*(\s)*$/";
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
           if (!preg_match($regexInputPseudoPdw, $pwd_one)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères trop spéciaux dans le mot de passe !"]);
                return $arrayControles;
            } 
        }
 
        if ($pwd_two != null || $pwd_two != '') {
            if (!preg_match($regexInputPseudoPdw, $pwd_two)) {
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
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux et chiffres dans le prenoms!"]);
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
            if (!preg_match($regexInputPseudoPdw, $pseudo)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux dans le pseudo!"]);
                return $arrayControles;
            }
        }
        
        if ($log_identifiant != null || $log_identifiant != '') {
            if (!preg_match($regexInputPseudoPdw, $log_identifiant)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux dans l'identifiant !"]);
                return $arrayControles;
            }
        }

        if ($nameGroup != null || $nameGroup != '') {
            if (!preg_match($regexInputPseudoPdw, $nameGroup)) {
                array_push($arrayControles, ['bool' => false, 'message' => "Attention aux charactères spéciaux dans le nom du groupe!"]);
                return $arrayControles;
            }
        }
        
        

        //###---If all is alright sending back 'true' with empty message---###

        array_push($arrayControles, ['bool' => true, 'message' => ""]);
            return $arrayControles;
    }

    public function deleteUser($idParticipant, $pseudo)
    {
        try {
            DB::table('participants')
            ->where('id', '=', $idParticipant)
            ->delete();
        } catch(Exception) {
            return redirect()->back()
                ->with('error', 'Problème de suppression de '.$pseudo.' dans la table "Participants" !');
        }

        try {
            DB::table('users')
            ->where('pseudo', '=', $pseudo)
            ->delete();
        } catch(Exception) {
            return redirect()->back()
                ->with('error', 'Problème de suppression de '.$pseudo.' dans la table "Users" !');
        }
    }
}



