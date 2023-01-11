<?php

namespace App\Http\Controllers;

use App\Models\Money;
use App\Models\Participants;
use Illuminate\Http\Request;

class moneyController extends Controller
{
    public function addMoney(Request $request, $idParticipant)
    {
        $participant = Participants::query()
            ->where('id', '=', $idParticipant)
            ->get();

        $pseudo = $participant[0]->pseudo;

        $money = Money::query()
            ->where('id_pseudo', '=', $idParticipant)
            ->orderBy('id', 'desc') // to get the last amount
            ->get();

        $credit = $request->inputMontant;
        $amount = $money[0]->amount;
            $amount = $amount + $credit;
        $totalAmount = $participant[0]->totalAmount;
            $totalAmount = $totalAmount + $credit;
        
        $gain = $request->inputAddGain;
        
        $participant = Participants::find($idParticipant);
        $participant->amount = number_format($amount, 2);
        $participant->totalAmount = number_format($totalAmount, 2);
        $participant->save();

        $action = new Money();
        $action->pseudo = $pseudo;
        $action->id_pseudo = $idParticipant;
        $action->amount = number_format($amount, 2);
        if ($gain === "true") {
            $action->creditGain = number_format($credit, 2);
        } else {
            $action->credit = number_format($credit, 2);
        }
        $action->save();

        return redirect()->back()
            ->with('success', $credit.'€ ajouté sur le compte de '. $pseudo);
    }

    public function debitMoney(Request $request, $idParticipant)
    {
        $participant = Participants::query()
            ->where('id', '=', $idParticipant)
            ->get();

        $pseudo = $participant[0]->pseudo;

        $money = Money::query()
            ->where('id_pseudo', '=', $idParticipant)
            ->orderBy('id', 'desc')
            ->get();

        $debit = $request->inputMontant;
        $amount = $money[0]->amount;
            $amount = $amount - $debit;
        $totalAmount = $participant[0]->totalAmount;
        

        $participant = Participants::find($idParticipant);
        $participant->amount = number_format($amount, 2);
        $participant->totalAmount = number_format($totalAmount, 2);
        $participant->save();

        $action = new Money();
        $action->pseudo = $pseudo;
        $action->id_pseudo = $idParticipant;
        $action->date = $request->inputDate;
        $action->amount = number_format($amount, 2);
        $action->debit = number_format($debit, 2);
        $action->save();

        return redirect()->back()
            ->with('success', $debit.'€ retiré du compte de '. $pseudo);
    }

    public function debitAll(Request $request)
    {
        $nameGroup = $request->inputNameGroup;
        if (!$nameGroup || $nameGroup == '') {
            return redirect()->back()
            ->with('error', 'indiquez le groupe qui joue, s\'il vous plait');
        }

        $arrayParticipant = Participants::query()
            ->where('nameGroup', '=', $nameGroup)
            ->get();

        $debitValue = $request->inputAmount;
        $nbPersonnes = count($arrayParticipant);
        $debitIndividuel = bcdiv($debitValue, $nbPersonnes, 2); //downRounding 0.9999 = 0.99
        
        for ($i = 0; $i < count($arrayParticipant); $i++) {
            $participant = Participants::query()
                ->where('pseudo', '=', $arrayParticipant[$i]->pseudo)
                ->get();
            // dd($participant);
            $idParticipant = $participant[0]->id;
                
            $pseudo = $participant[0]->pseudo;

            $money = Money::query()
                ->where('id_pseudo', '=', $idParticipant)
                ->orderBy('id', 'desc')
                ->get();

            $debit = $debitIndividuel;
            $amount = $money[0]->amount;
                $amount = $amount - $debit;
            $totalAmount = $participant[0]->totalAmount;
        
            $participant = Participants::find($idParticipant);
            $participant->amount = number_format($amount, 2);
            $participant->totalAmount = number_format($totalAmount, 2);
            $participant->save();
        
            $action = new Money();
            $action->pseudo = $pseudo;
            $action->id_pseudo = $idParticipant;
            $action->amount = number_format($amount, 2);
            $action->debit = number_format($debit, 2);
            $action->date = $request->inputDate;
            $action->save();
        }

        return redirect()->back()
            ->with('success', $debit.' € retiré du(des) compte(s) du(des) Participant(s)');
    }
        
}
