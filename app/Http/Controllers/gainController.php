<?php

namespace App\Http\Controllers;

use App\Models\Gains;
use App\Models\Groups;
use App\Models\Money;
use App\Models\Participants;
use Illuminate\Http\Request;

class gainController extends Controller
{
    public function addGain(Request $request)
    {
        // dd($request->inputDate);
        $nameGroup = $request->inputNameGroup;
        if (!$nameGroup || $nameGroup == '') {
            return redirect()->back()
            ->with('error', 'indiquez le groupe gagniant, s\'il vous plait');
        }

        $arrayParticipantWin = Participants::query()
            ->where('nameGroup', '=', $nameGroup)
            ->get();

        $gainValue = $request->inputAmount;
        $nbPersonnes = count($arrayParticipantWin);
        $gainIndividuel = bcdiv($gainValue, $nbPersonnes, 2); //downRounding 0.9999 = 0.99

        $gain = new Gains();
        $gain->amount = $gainValue;
        $gain->nameGroup = $request->inputNameGroup;
        $gain->date = $request->inputDate;
        $gain->nbPersonnes = $nbPersonnes;
        $gain->gainIndividuel = $gainIndividuel;
        $gain->save();
        
        $addMoney = $request->inputAddGainAuto;
        if ($addMoney === "true") {
            for ($i = 0; $i < count($arrayParticipantWin); $i++) {
                $participant = Participants::query()
                    ->where('pseudo', '=', $arrayParticipantWin[$i]->pseudo)
                    ->get();
                $idParticipant = $participant[0]->id;
                $pseudo = $participant[0]->pseudo;

                $money = Money::query()
                    ->where('id_pseudo', '=', $idParticipant)
                    ->orderBy('id', 'desc')
                    ->get();

                $credit = $gainIndividuel;
                $amount = $money[0]->amount;
                    $amount = $amount + $credit;
                $totalAmount = $participant[0]->totalAmount;
                    $totalAmount = $totalAmount + $credit;
                
        
                $participant = Participants::find($idParticipant);
                $participant->amount = number_format($amount, 2);
                $participant->totalAmount = number_format($totalAmount, 2);
                $participant->save();
        
                $action = new Money();
                $action->pseudo = $pseudo;
                $action->id_pseudo = $idParticipant;
                $action->date = $request->inputDate;
                $action->amount = number_format($amount, 2);
                $action->creditGain = number_format($credit, 2);
                $action->save();
            }
            return redirect()->back()
                ->with('success', 'felicitation, votre gain de '. $gainValue. '€ à été enrégistré et partagé parmi les participant(s)! 🥳🥳🥳🥳🥳🥳');
        }

        return redirect()->back()
            ->with('success', 'felicitation, votre gain de '. $gainValue. '€ à été enrégistré. Pense à la distribution du Gain ;) 🥳🥳🥳🥳🥳🥳');
    }

    public function getGainHistory()
    {
        $participants = Participants::query()
            ->get();
        
        $gains = Gains::query()
            ->orderBy('date', 'desc')
            ->get();

        $groups = Groups::query()
            ->get();

        $arrayFondsByGroup = $this->fonds($groups);
        $groupsDispo = $this->groupsDisponible();

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
        } else {
            array_push($arrayGainByGroup, ['nameGroup' => "pas de groupe", 'sommeGains' => 0]);
            $sommeGains = "";
        }
        return view('pages.gains', [
            'gains' => $gains,
            'sommeGains' => $sommeGains,
            'participants' => $participants,
            'groups' => $groups,
            'groupsDispo' => $groupsDispo,
            'fonds' => $arrayFondsByGroup,
            'sommeGainsByGroups' => $arrayGainByGroup]);
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
}
